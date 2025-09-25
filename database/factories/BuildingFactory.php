<?php

namespace Database\Factories;

use App\Models\Building;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    function definition(): array
    {
        // Что-то в Москве
        $lat = fake()->latitude(55.72181745011488, 55.78128316565161);
        $lng = fake()->longitude(37.577347420146005, 37.667710152797795);

        return [
            'address' => fake()->address(),
            'geo' => Point::makeGeodetic($lat, $lng),
        ];
    }

    function withLatLng(float $lng, float $lat): static
    {
        return $this->state(['geo' => Point::makeGeodetic($lat, $lng)]);
    }
}
