<?php

namespace App\Ship\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Carbon\Carbon;

class ThisBetweenDatesCriteria extends Criteria
{
    public function __construct(
        private string $field,
        private Carbon|null $start,
        private Carbon|null $end,
    ) {
    }

    public function apply($model, $repository)
    {
        if (!$this->start AND !$this->end) {
            return $model;
        } else if (!$this->start) {
            return $model->where($this->field, '<=', $this->end->toDateTimeString());
        } else if (!$this->end) {
            return $model->where($this->field, '>=', $this->start->toDateTimeString());
        }

        return $model->whereBetween($this->field, [$this->start->toDateTimeString(), $this->end->toDateTimeString()]);
    }
}
