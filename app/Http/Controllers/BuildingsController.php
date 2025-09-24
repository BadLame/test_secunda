<?php

namespace App\Http\Controllers;

use App\Http\Requests\Building\BuildingListFilterType;
use App\Http\Requests\Building\BuildingsListRequest;
use App\Http\Resources\Building\BuildingResource;
use App\Models\Building;

class BuildingsController extends Controller
{
    /** Здания в радиусе / прямоугольнике от географической точки */
    function list(BuildingsListRequest $r)
    {
        $buildings = match ($r->filterType) {
            BuildingListFilterType::RADIUS => Building::query()
                ->aroundThePoint($r->point_lat, $r->point_lng, $r->radius),
            BuildingListFilterType::RECT => Building::query()
                ->aroundInRect($r->point_lat, $r->point_lng, $r->distance_for_lat, $r->distance_for_lng),
        };

        return BuildingResource::collection($buildings->get());
    }
}
