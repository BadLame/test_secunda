<?php

namespace App\Models\Queries;

use App\Models\Building;
use App\Models\BusinessDirection;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/** @mixin Company */
class CompanyQuery extends Builder
{
    /** Вызывает with с отношениями для подробной информации */
    function withPublicInfo(): static
    {
        return $this->with(['building', 'businessDirections' => ['parent']]);
    }

    /** Поиск по части названия компании */
    function byName(string $name): static
    {
        return $this->where(
            fn (self $q) => $q->where('companies.name', 'like', "%$name%")
        );
    }

    /** Вокруг переданной точки */
    function aroundThePoint(float $lat, float $lng, float $radiusInKm): static
    {
        return $this->whereHas(
            'building',
            fn (BuildingQuery $q) => $q->aroundThePoint($lat, $lng, $radiusInKm)
        );
    }

    /** В прямоугольнике вокруг переданной точки */
    function aroundInRect(float $lat, float $lng, float $latDistanceKm, float $lngDistanceKm): static
    {
        return $this->whereHas(
            'building',
            fn (BuildingQuery $q) => $q->aroundInRect($lat, $lng, $latDistanceKm, $lngDistanceKm)
        );
    }

    /** Принадлежащие к зданию */
    function byBuilding(Building|string $building): static
    {
        $building = is_object($building) ? $building->id : $building;

        return $this->whereHas(
            'building',
            fn (BuildingQuery $q) => $q->where('id', $building)
        );
    }

    /** По направлению деятельности (включая дочерние направления) */
    function byBusinessDirectionAndChildren(BusinessDirection|string $bdIdOrCode): static
    {
        $bd = is_object($bdIdOrCode) ? $bdIdOrCode->id : $bdIdOrCode;

        /** @var BusinessDirection $bd */
        $bd = BusinessDirection::query()
            ->when(
                Str::isUuid($bd),
                fn (BusinessDirectionQuery $q) => $q->where('id', $bd),
                fn (BusinessDirectionQuery $q) => $q->where('code', $bd)
            )
            ->withAllChildren()
            ->firstOrFail();

        $bdIds = [$bd->id];
        $childrenArr = $bd->children->toArray();
        array_walk_recursive($childrenArr, function ($val, $key) use (&$bdIds) {
            if ($key == 'id') $bdIds[] = $val;
        });

        return $this->whereHas(
            'businessDirections',
            fn (BusinessDirectionQuery $q) => $q->whereIn('id', $bdIds)
        );
    }

    /** По направлению деятельности (исключая дочерние направления) */
    function byExactBusinessDirection(BusinessDirection|string $bdIdOrCode): static
    {
        $bd = is_object($bdIdOrCode) ? $bdIdOrCode->id : $bdIdOrCode;

        return $this->whereHas(
            'businessDirections',
            fn (BusinessDirectionQuery $q) => $q->when(
                Str::isUuid($bd),
                fn (BusinessDirectionQuery $q) => $q->where('id', $bd),
                fn (BusinessDirectionQuery $q) => $q->where('code', $bd)
            )
        );
    }
}
