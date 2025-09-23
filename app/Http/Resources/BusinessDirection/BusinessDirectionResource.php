<?php

namespace App\Http\Resources\BusinessDirection;

use App\Models\BusinessDirection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessDirectionResource extends JsonResource
{
    function toArray(Request $request): array
    {
        /** @var BusinessDirection $bd */
        $bd = $this->resource;

        return [
            'title' => $bd->title,
            'parent' => $this->whenLoaded('parent', fn () => new static($bd->parent)),
            'children' => $this->whenLoaded(
                'children',
                fn () => static::collection($bd->children)
            ),
        ];
    }
}
