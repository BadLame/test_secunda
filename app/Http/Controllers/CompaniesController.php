<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyListFilterType as FilterType;
use App\Http\Requests\Company\CompanyListRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompaniesController extends Controller
{
    /**
     * Информация об организации по её ID
     * @param string $company ID компании
     * @return CompanyResource
     */
    function find(string $company): CompanyResource
    {
        return new CompanyResource(
            Company::query()->withPublicInfo()->findOrFail($company)
        );
    }

    /**
     * Поиск компаний по различным признакам
     * @param CompanyListRequest $r
     * @return AnonymousResourceCollection
     */
    function list(CompanyListRequest $r): AnonymousResourceCollection
    {
        $q = Company::query()->withPublicInfo();

        $q = match ($r->filterType) {
            FilterType::NAME => $q->byName($r->name),
            FilterType::BUILDING => $q->byBuilding($r->building),
            FilterType::CONCRETE_BUSINESS_DIRECTION => $q->byExactBusinessDirection($r->bd_code),
            FilterType::BUSINESS_DIRECTION => $q->byBusinessDirectionAndChildren($r->bd_code),
            FilterType::GEO_RADIUS => $q->aroundThePoint($r->point_lat, $r->point_lng, $r->radius),
            FilterType::GEO_RECT => $q->aroundInRect(
                $r->point_lat, $r->point_lng, $r->distance_for_lat, $r->distance_for_lng
            ),
        };

        return CompanyResource::collection($q->get());
    }
}
