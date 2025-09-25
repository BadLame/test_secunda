<?php

namespace Tests\Feature\Http\Controllers;

use App\Helpers\GeoHelper;
use App\Http\Requests\Company\CompanyListFilterType;
use App\Models\Building;
use App\Models\BusinessDirection;
use App\Models\Company;
use Illuminate\Support\Str;
use Tests\TestCase;

class CompaniesControllerTest extends TestCase
{
    function testFindGivesInfoAboutCompany(): void
    {
        $company = Company::factory()->create();

        $this->authorized()
            ->getJson(route('api.company.find', ['company' => $company->id]))
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $company->id])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'phones',
                    'building',
                    'business_directions',
                ],
            ]);
    }

    function testListFiltersByName(): void
    {
        $symbolsForSearch = rand(5, 7);
        $companies = Company::factory()->createMany([
            ...collect(range(1, 3))
                ->map(fn ($_) => ['name' => Str::random()])
                ->toArray(),
        ]);

        foreach ($companies as $company) {
            $startPos = rand(0, strlen($company->name) - 1 - $symbolsForSearch);
            $partOfName = substr(
                $company->name,
                $startPos,
                $startPos + $symbolsForSearch
            );
            $this->authorized()
                ->getJson(route('api.company.list', [
                    'filter_type' => CompanyListFilterType::NAME->value,
                    'name' => $partOfName,
                ]))
                ->assertSuccessful()
                ->assertJsonFragment(['id' => $company->id])
                ->assertJsonCount(1, 'data');
        }
    }

    function testListFiltersByBuilding(): void
    {
        $building = Building::factory()->create();
        $companies = Company::factory(['building_id' => $building->id])
            ->count(rand(5, 10))
            ->create();

        $response = $this->authorized()
            ->getJson(route('api.company.list', [
                'filter_type' => CompanyListFilterType::BUILDING->value,
                'building' => $building->id,
            ]))
            ->assertSuccessful()
            ->assertJsonCount($companies->count(), 'data');

        foreach ($companies as $company) {
            $response->assertJsonFragment(['id' => $company->id]);
        }
    }

    function testListFiltersByConcreteBusinessDirection(): void
    {
        $companies = Company::factory(rand(5, 10))->create();
        $unexpectedCompanies = Company::factory(rand(5, 10))->create();
        $bd = BusinessDirection::factory()->hasChildren()->create();
        /** @var BusinessDirection $childBd */
        $childBd = $bd->children()->inRandomOrder()->firstOrFail();

        $companies->each(fn (Company $c) => $c->businessDirections()->save($bd));
        $unexpectedCompanies->each(fn (Company $c) => $c->businessDirections()->save($childBd));

        $response = $this->authorized()
            ->getJson(route('api.company.list', [
                'filter_type' => CompanyListFilterType::CONCRETE_BUSINESS_DIRECTION->value,
                'bd_code' => $bd->code,
            ]))
            ->assertSuccessful()
            ->assertJsonCount($companies->count(), 'data');

        foreach ($companies as $company) {
            $response->assertJsonFragment(['id' => $company->id]);
        }

        foreach ($unexpectedCompanies as $unexpectedCompany) {
            $response->assertJsonMissing(['id' => $unexpectedCompany->id]);
        }
    }

    function testListFiltersByBusinessDirectionAndItsChildren(): void
    {
        $companies = Company::factory(rand(5, 10))->create();
        $otherExpectedCompanies = Company::factory(rand(5, 10))->create();
        $bd = BusinessDirection::factory()->hasChildren()->create();
        /** @var BusinessDirection $childBd */
        $childBd = $bd->children()->inRandomOrder()->firstOrFail();

        $companies->each(fn (Company $c) => $c->businessDirections()->save($bd));
        $otherExpectedCompanies->each(fn (Company $c) => $c->businessDirections()->save($childBd));

        $response = $this->authorized()
            ->getJson(route('api.company.list', [
                'filter_type' => CompanyListFilterType::BUSINESS_DIRECTION->value,
                'bd_code' => $bd->code,
            ]))
            ->assertSuccessful()
            ->assertJsonCount($companies->count() + $otherExpectedCompanies->count(), 'data');

        foreach ($companies->merge($otherExpectedCompanies) as $company) {
            $response->assertJsonFragment(['id' => $company->id]);
        }
    }

    function testListFiltersByRadius(): void
    {
        $radius = rand(5, 10);
        [$lat, $lng] = [fake()->latitude, fake()->longitude];
        $randLngLatAtDistanceFn = function (float $lat, float $lng, float $distanceKm) {
            $point = GeoHelper::generateRandomPointAtDistance($lat, $lng, $distanceKm);
            return [$point['lng'], $point['lat']];
        };

        $companiesWithin = Company::factory(rand(5, 10))->create([
            'building_id' => Building::factory()
                ->withLatLng(...$randLngLatAtDistanceFn($lat, $lng, rand(0, $radius))),
        ]);
        $companiesOutside = Company::factory(rand(5, 10))->create([
            'building_id' => Building::factory()
                ->withLatLng(...$randLngLatAtDistanceFn($lat, $lng, $radius + 0.1)),
        ]);

        $response = $this->authorized()
            ->getJson(route('api.company.list', [
                'filter_type' => CompanyListFilterType::GEO_RADIUS->value,
                'radius' => $radius,
                'point_lat' => $lat,
                'point_lng' => $lng,
            ]))
            ->assertSuccessful()
            ->assertJsonCount($companiesWithin->count(), 'data');

        foreach ($companiesWithin as $companyWithin) {
            $response->assertJsonFragment(['id' => $companyWithin->id]);
        }

        foreach ($companiesOutside as $companyOutside) {
            $response->assertJsonMissing(['id' => $companyOutside->id]);
        }
    }
}
