<?php

namespace App\Models\Queries;

use App\Models\BusinessDirection;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

/** @mixin BusinessDirection */
class BusinessDirectionQuery extends Builder
{
    function ofParentNestingLevel(int $parentsOfParents = 1): self
    {
        if ($parentsOfParents < 1) {
            throw new InvalidArgumentException('$parentsOfParents должен быть не меньше одного');
        }

        return $this->whereHas('parent' . str_repeat('.parent', $parentsOfParents - 1));
    }
}
