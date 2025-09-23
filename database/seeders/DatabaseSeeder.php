<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    function run(): void
    {
        $this->call([
            BusinessDirectionSeeder::class,
            BuildingSeeder::class,
        ]);
    }
}
