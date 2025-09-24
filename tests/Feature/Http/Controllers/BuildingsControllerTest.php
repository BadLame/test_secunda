<?php

namespace Tests\Feature\Http\Controllers;

use App\Helpers\GeoHelper;
use App\Http\Requests\Building\BuildingListFilterType;
use App\Models\Building;
use Tests\TestCase;

class BuildingsControllerTest extends TestCase
{
    function testListFilterValidationByFilterType()
    {
        $point = ['point_lat' => fake()->latitude, 'point_lng' => fake()->longitude];

        $this->authorized()
            ->getJson(route('api.building.list', [
                ...$point,
                'filter_type' => BuildingListFilterType::RADIUS,
            ]))
            ->assertJsonValidationErrorFor('radius');
        $this->authorized()
            ->getJson(route('api.building.list', [
                ...$point,
                'filter_type' => BuildingListFilterType::RECT,
            ]))
            ->assertJsonValidationErrorFor('distance_for_lat')
            ->assertJsonValidationErrorFor('distance_for_lng');
    }

    function testListFilterByRadius(): void
    {
        // Подготовка данных

        $radius = rand(5, 10);
        $point = ['lat' => fake()->latitude, 'lng' => fake()->longitude];

        $coordsWithin = collect(range(1, 5))
            ->map(fn ($_) => $this->generateRandomPointAtDistance(
                $point['lat'],
                $point['lng'],
                $radius - rand(1, 2))
            )
            ->toArray();
        $coordsOutside = $this->generateRandomPointAtDistance(
            $point['lat'],
            $point['lng'],
            $radius + rand(1, 2)
        );

        $buildingsWithin = [];
        foreach ($coordsWithin as $coords) {
            $buildingsWithin[] = Building::factory()->withLatLng($coords['lng'], $coords['lat'])->create();
        }
        $buildingOutside = Building::factory()->withLatLng($coordsOutside['lng'], $coordsOutside['lat'])->create();

        // Запрос

        $response = $this->authorized()
            ->getJson(route('api.building.list', [
                'point_lat' => $point['lat'],
                'point_lng' => $point['lng'],
                'filter_type' => BuildingListFilterType::RADIUS->value,
                'radius' => $radius,
            ]))
            ->assertSuccessful();

        // Сверка результатов

        foreach ($buildingsWithin as $buildingWithin) {
            $response->assertJsonFragment(['id' => $buildingWithin->id]);
        }
        $response->assertJsonMissing(['id' => $buildingOutside->id]);
    }

    function testListFilterByRect(): void
    {
        // Подготовка данных

        [$lat, $lng] = [fake()->latitude, fake()->longitude];
        [$rectWidth, $rectHeight] = [rand(1, 10), rand(1, 10)];
        ['n' => $n, 's' => $s, 'w' => $w, 'e' => $e] = GeoHelper::calculateRectangleCorners(
            $lat, $lng, $rectWidth * 1000, $rectHeight * 1000
        );
        $randFloat = fn (float $i, float $j) => rand($i * 1_000, $j * 1_000) / 1_000;

        $buildingsWithin = collect(range(1, 5))
            ->map(
                fn ($_) => Building::factory()
                    ->withLatLng($randFloat($w, $e), $randFloat($s, $n))
                    ->create()
            );
        $buildingOutsideY = Building::factory()
            ->withLatLng($randFloat($w, $e), $n + $randFloat(0.1, 1))
            ->create();
        $buildingOutsideX = Building::factory()
            ->withLatLng($w + $randFloat(0.1, 1), $randFloat($s, $n))
            ->create();

        // Запрос

        $response = $this->authorized()
            ->getJson(route('api.building.list', [
                'point_lat' => $lat,
                'point_lng' => $lng,
                'filter_type' => BuildingListFilterType::RECT->value,
                'distance_for_lat' => $rectHeight,
                'distance_for_lng' => $rectWidth,
            ]))
            ->assertSuccessful();

        // Сверка результатов

        foreach ($buildingsWithin as $buildingWithin) {
            $response->assertJsonFragment(['id' => $buildingWithin->id]);
        }
        foreach ([$buildingOutsideX, $buildingOutsideY] as $buildingOutside) {
            $response->assertJsonMissing(['id' => $buildingOutside->id]);
        }
    }

    // Helpers

    /**
     * @param $centerLat
     * @param $centerLon
     * @param $distanceKm
     * @return array{lat: float, lng: float}
     */
    protected function generateRandomPointAtDistance($centerLat, $centerLon, $distanceKm): array
    {
        $earthRadius = 6371;

        // Случайный азимут (0-360 градусов)
        $bearing = deg2rad(mt_rand(0, 360));

        // Угловое расстояние в радианах
        $angularDistance = $distanceKm / $earthRadius;

        $centerLatRad = deg2rad($centerLat);
        $centerLonRad = deg2rad($centerLon);

        // Вычисляем новую точку
        $newLat = asin(sin($centerLatRad) * cos($angularDistance) +
            cos($centerLatRad) * sin($angularDistance) * cos($bearing));

        $newLon = $centerLonRad + atan2(sin($bearing) * sin($angularDistance) * cos($centerLatRad),
                cos($angularDistance) - sin($centerLatRad) * sin($newLat));

        // Нормализуем долготу
        $newLon = fmod(($newLon + 3 * M_PI), (2 * M_PI)) - M_PI;

        return [
            'lat' => rad2deg($newLat),
            'lng' => rad2deg($newLon),
        ];
    }
}
