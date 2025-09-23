<?php

namespace App\Http\Controllers;

use App\Http\Resources\Company\CompanyResource;
use App\Models\Company;

class CompaniesController extends Controller
{
    /** Информация об организации по её ID */
    function find(Company $company): CompanyResource
    {
        return new CompanyResource(
            $company->loadMissing(['building', 'businessDirections' => ['parent']])
        );
    }
}
