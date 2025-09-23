<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    function run(): void
    {
        $this->call([
            BusinessDirectionSeeder::class,
            BuildingSeeder::class,
            CompanySeeder::class,
        ]);
    }
}
