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
    function withPublicInfo(): static
    {
        return $this->with(['building', 'businessDirections' => ['parent']]);
    }

    function byName(string $name): static
    {
        return $this->where(
            fn (self $q) => $q->where('companies.name', 'like', "%$name%")
        );
    }

    function aroundThePoint(float $lat, float $lng, float $radiusInKm): static
    {
        return $this->whereHas(
            'building',
            fn (BuildingQuery $q) => $q->aroundThePoint($lat, $lng, $radiusInKm)
        );
    }

    function byBuilding(Building|string $building): static
    {
        $building = is_object($building) ? $building->id : $building;

        return $this->whereHas(
            'building',
            fn (BuildingQuery $q) => $q->where('id', $building)
        );
    }

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
