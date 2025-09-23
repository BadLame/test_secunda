<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Company;
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
}
