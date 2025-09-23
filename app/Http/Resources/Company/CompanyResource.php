<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\Building\BuildingResource;
use App\Http\Resources\BusinessDirection\BusinessDirectionResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    function toArray(Request $request): array
    {
        /** @var Company $c */
        $c = $this->resource;

        return [
            'id' => $c->id,
            'name' => $c->name,
            'phones' => $c->phones,
            'building' => $this->whenLoaded(
                'building',
                fn () => new BuildingResource($c->building)
            ),
            'business_directions' => $this->whenLoaded(
                'businessDirections',
                fn () => BusinessDirectionResource::collection($c->businessDirections)
            ),
        ];
    }
}
