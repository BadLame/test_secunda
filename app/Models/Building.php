<?php

namespace App\Models;

use App\Models\Queries\BuildingQuery;
use Clickbar\Magellan\Data\Geometries\Point;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Здания
 *
 * @property int $id
 * @property string $address
 * @property Point $geo
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property Collection<Company> $companies
 *
 * @method static BuildingFactory factory($count = null, $state = [])
 * @method static BuildingQuery|Building query()
 *
 * @mixin BuildingQuery
 */
class Building extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'geo' => Point::class,
    ];

    protected $fillable = [
        'address',
        'geo',
    ];

    // Relations

    function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    // Misc

    function newEloquentBuilder($query): BuildingQuery
    {
        return new BuildingQuery($query);
    }
}
