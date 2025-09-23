<?php

namespace Database\Seeders;

use App\Models\BusinessDirection;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    function run(): void
    {
        Company::factory(rand(50, 100))->create();
    }
}
