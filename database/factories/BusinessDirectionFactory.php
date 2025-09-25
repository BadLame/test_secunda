<?php

namespace Database\Factories;

use App\Models\BusinessDirection;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessDirectionFactory extends Factory
{
    protected $model = BusinessDirection::class;

    function definition(): array
    {
        return [
            'title' => fake()->unique()->word(),
            'parent_id' => null,
        ];
    }

    function hasParent(): static
    {
        return $this->state(['parent_id' => BusinessDirection::factory()]);
    }

    function hasChildren(int $count = 1): static
    {
        return $this->afterCreating(
            fn (BusinessDirection $bd) => BusinessDirection::factory(['parent_id' => $bd->id])
                ->count($count)
                ->create()
        );
    }
}
