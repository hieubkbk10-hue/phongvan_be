<?php

namespace App\Ship\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Closure;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class WithRelationshipCriteria extends Criteria
{
    public function __construct(
        private string $with,
        private mixed $column = null,
        private string|array|null $value = null,
        private string $type = 'has',
    ) {
    }

    public function apply($model, PrettusRepositoryInterface $repository)
    {
        if ($this->type === 'has') {
            if (is_null($this->column)) {
                return $model->has($this->with);
            }

            if ($this->column instanceof Closure) {
                return $model->whereHas($this->with, $this->column);
            }

            return $model->whereHas($this->with, function ($query) {
                if ($this->column == 'raw') {
                    $query->whereRaw($this->value);
                } elseif (is_array($this->value)) {
                    $query->whereIn($this->column, $this->value);
                } else {
                    $query->where($this->column, $this->value);
                }
            });
        } else if ($this->type === 'doesntHave') {
            if (is_null($this->column)) {
                return $model->doesntHave($this->with);
            }

            if ($this->column instanceof Closure) {
                return $model->whereDoesntHave($this->with, $this->column);
            }

            return $model->whereDoesntHave($this->with, function ($query) {
                if ($this->column == 'raw') {
                    $query->whereRaw($this->value);
                } elseif (is_array($this->value)) {
                    $query->whereIn($this->column, $this->value);
                } else {
                    $query->where($this->column, $this->value);
                }
            });
        }
    }
}
