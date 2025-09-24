<?php

namespace App\Http\Resources\Building;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    function toArray(Request $request): array
    {
        /** @var Building $b */
        $b = $this->resource;

        return [
            'id' => $b->id,
            'address' => $b->address,
            'lat' => $b->geo->getLatitude(),
            'lng' => $b->geo->getLongitude(),
        ];
    }
}
