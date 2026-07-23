<?php

namespace App\Ship\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Closure;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class ThisConditionThatCriteria extends Criteria
{
    public function __construct(
        private mixed $column,
        private mixed $operator = null,
        private mixed $value = null,
    ) {
    }

    public function apply($model, PrettusRepositoryInterface $repository)
    {
        if ($this->column instanceof Closure) {
            return $model->where($this->column);
        }

        if ($this->operator == 'date') {
            return $model->whereDate($this->column, $this->value);
        } elseif ($this->operator == 'time') {
            return $model->whereTime($this->column, $this->value);
        } elseif ($this->operator == 'between') {
            return $model->whereBetween($this->column, (array)$this->value);
        } elseif ($this->operator == 'in') {
            return $model->whereIn($this->column, (array)$this->value);
        } elseif ($this->operator == 'notin') {
            return $model->whereNotIn($this->column, (array)$this->value);
        } elseif ($this->operator == 'raw') {
            if (is_array($this->value)) {
                return $model->whereRaw($this->column, $this->value);
            }

            return $model->whereRaw($this->column);
        }

        return $model->where($this->column, $this->operator, $this->value);
    }
}
