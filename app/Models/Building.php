<?php

namespace App\Models;

use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Здания
 *
 * @property int $id
 * @property string $address
 * @property float $lat
 * @property float $lng
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @method static BuildingFactory factory($count = null, $state = [])
 */
class Building extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'address',
        'lat',
        'lng',
    ];
}
