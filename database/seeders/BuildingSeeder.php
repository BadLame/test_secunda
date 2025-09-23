<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    function run(): void
    {
        Building::factory(rand(20, 30))->create();
    }
}
