<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    function definition(): array
    {
        return [
            'address' => fake()->address(),
            // Что-то в Москве
            'lat' => fake()->latitude(55.72181745011488, 55.78128316565161),
            'lng' => fake()->longitude(37.577347420146005, 37.667710152797795),
        ];
    }
}
