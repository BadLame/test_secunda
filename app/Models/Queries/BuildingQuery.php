<?php

namespace App\Models\Queries;

use App\Helpers\GeoHelper;
use App\Models\Building;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/** @mixin Building */
class BuildingQuery extends Builder
{
    /** Находятся вокруг точки на заданном радиусе */
    function aroundThePoint(float $lat, float $lng, float $radiusInKm): static
    {
        return $this->where(
            fn (self $q) => $q->whereRaw(
                'ST_Distance(buildings.geo::geography, ST_Point(?, ?)) <= ?',
                [$lng, $lat, $radiusInKm * 1000]
            )
        );
    }

    /** Находятся вокруг точки в пределах прямоугольника */
    function aroundInRect(float $lat, float $lng, float $latDistanceKm, float $lngDistanceKm): static
    {
        [$latD, $lngD] = [$latDistanceKm * 1_000, $lngDistanceKm * 1_000];
        ['n' => $n, 's' => $s, 'w' => $w, 'e' => $e] = GeoHelper::calculateRectangleEdges($lat, $lng, $lngD, $latD);

        return $this->where(
            fn (self $q) => $q
                ->whereBetween(DB::raw('ST_Y(buildings.geo::geometry)'), [$s, $n])
                ->whereBetween(DB::raw('ST_X(buildings.geo::geometry)'), [$w, $e])
        );
    }
}
