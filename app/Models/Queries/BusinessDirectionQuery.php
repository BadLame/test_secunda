<?php

namespace App\Models\Queries;

use App\Models\BusinessDirection;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

/** @mixin BusinessDirection */
class BusinessDirectionQuery extends Builder
{
    function ofParentNestingLevel(int $parentsOfParents = 1): static
    {
        if ($parentsOfParents < 1) {
            throw new InvalidArgumentException('$parentsOfParents должен быть не меньше одного');
        }

        return $this->whereHas('parent' . str_repeat('.parent', $parentsOfParents - 1));
    }

    function withAllChildren(): static
    {
        return $this->with(
            // 1 - Корневой элемент, детей которого выбираем
            'children' // 2 - дети корневого элемента
            . str_repeat('.children', BusinessDirection::MAX_NESTING_LEVEL - 2) // поэтому - 2
        );
    }
}
