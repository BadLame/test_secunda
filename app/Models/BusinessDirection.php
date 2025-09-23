<?php

namespace App\Models;

use App\Models\Queries\BusinessDirectionQuery;
use Database\Factories\BusinessDirectionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Направление деятельности компании
 *
 * @property string $id
 * @property string $title
 * @property string|null $parent_id
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property Collection<BusinessDirection> $children
 * @property BusinessDirection $parent
 *
 * @method static BusinessDirectionFactory factory($count = null, $state = [])
 * @method static BusinessDirectionQuery|BusinessDirection query()
 *
 * @mixin BusinessDirectionQuery
 */
class BusinessDirection extends Model
{
    use HasFactory, HasUuids;

    const int MAX_NESTING_LEVEL = 3;

    protected $fillable = [
        'title',
        'parent_id',
    ];

    // Relations

    function parent(): BelongsTo
    {
        return $this->belongsTo(BusinessDirection::class, 'parent_id');
    }

    function children(): HasMany
    {
        return $this->hasMany(BusinessDirection::class, 'parent_id');
    }

    // Misc

    protected static function booted(): void
    {
        static::saving(function (self $bd) {
            if ($bd->parent_id) {
                $isParentOnMaxNestingLevel = static::query()
                    ->where('id', $bd->parent_id)
                    ->ofParentNestingLevel(static::MAX_NESTING_LEVEL - 1)
                    ->exists();

                $bd->parent_id = $isParentOnMaxNestingLevel ? null : $bd->parent_id;
            }
        });
    }

    function newEloquentBuilder($query): BusinessDirectionQuery
    {
        return new BusinessDirectionQuery($query);
    }
}
