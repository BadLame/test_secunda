<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phones' => !!rand(0, 1)
                ? collect(range(0, rand(0, 4)))->map(fn ($_) => fake()->e164PhoneNumber())
                : [],
            'building_id' => Building::query()->inRandomOrder()->value('id'),
        ];
    }
}
