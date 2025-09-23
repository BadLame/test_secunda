<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Компании
 *
 * @property string $id
 * @property string $name
 * @property array|null $phones
 * @property string $building_id
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property Building $building
 * @property Collection<BusinessDirection> $businessDirections
 *
 * @method static CompanyFactory factory($count = null, $state = [])
 */
class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'phones',
        'building_id',
    ];

    protected $casts = [
        'phones' => 'array',
    ];

    // Relations

    function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    function businessDirections(): BelongsToMany
    {
        return $this->belongsToMany(BusinessDirection::class, 'company_business_directions_pivot')
            ->withTimestamps();
    }
}
