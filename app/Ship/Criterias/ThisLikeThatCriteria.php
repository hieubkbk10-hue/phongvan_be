<?php

namespace App\Ship\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class ThisLikeThatCriteria extends Criteria
{
    public function __construct(
        private string $field,
        private string $value,
        private string $separator = ',',
        private string $wildcard = '*'
    ) {
    }

    public function apply($model, PrettusRepositoryInterface $repository)
    {
        return $model->where(function ($query) {
            $values = explode($this->separator, $this->value);
            $query->where($this->field, 'LIKE', str_replace($this->wildcard, '%', array_shift($values)));
            foreach ($values as $value) {
                $query->orWhere($this->field, 'LIKE', str_replace($this->wildcard, '%', $value));
            }
        });
    }
}
