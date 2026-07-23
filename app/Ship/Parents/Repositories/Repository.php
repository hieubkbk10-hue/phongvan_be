<?php

namespace App\Ship\Parents\Repositories;

use Illuminate\Support\Carbon;
use Apiato\Core\Abstracts\Repositories\Repository as AbstractRepository;
use App\Ship\Criterias\ThisBetweenDatesCriteria;
use App\Ship\Criterias\IsNullCriteria;
use App\Ship\Criterias\NotNullCriteria;
use App\Ship\Criterias\RandomCriteria;
use App\Ship\Criterias\ThisConditionThatCriteria;
use App\Ship\Criterias\ThisEqualThatCriteria;
use App\Ship\Criterias\ThisLikeThatCriteria;
use App\Ship\Criterias\WithRelationshipCriteria;

abstract class Repository extends AbstractRepository
{
    public function boot()
    {
        $request = request();
        $query = $request->query();
        if ($request->isMethod('get')) {
            if (array_key_exists('searchDate', $query) AND $query['searchDate']) {
                $searchDate = explode('|', $query['searchDate']);
                $field = 'created_at';
                if (isset($searchDate[1]) AND ($searchDate[1] == 'updated_at' OR array_key_exists($searchDate[1], $this->fieldSearchable))) {
                    $field = $searchDate[1];
                }

                $searchDate = explode(',', $searchDate[0]);
                $start = $searchDate[0] ? Carbon::parse($searchDate[0] . ' 00:00:00') : null;
                $end = null;
                if (isset($searchDate[1])) {
                    $end = Carbon::parse($searchDate[1] . ' 23:59:59');
                }

                $this->pushCriteria(new ThisBetweenDatesCriteria($field, $start, $end));

                unset($query['searchDate']);
            }

            if (array_key_exists('searchNull', $query) AND $query['searchNull']) {
                $searchNull = explode(';', $query['searchNull']);

                foreach ($searchNull as $_item) {
                    $arr = explode(':', $_item);
                    $field = $arr[0];
                    $val = isset($arr[1]) ? $arr[1] : null;

                    if (array_key_exists($field, $this->fieldSearchable)) {
                        if ($val == 'not') {
                            $this->pushCriteria(new NotNullCriteria($field));
                        } else {
                            $this->pushCriteria(new IsNullCriteria($field));
                        }
                    }
                }

                unset($query['searchNull']);
            }

            if (array_key_exists('searchInclude', $query) AND $query['searchInclude']) {
                $searchInclude = explode(';', $query['searchInclude']);
                
                foreach ($searchInclude as $_item) {
                    $arr = explode(':', $_item);
                    if (count($arr) != 2) {
                        continue;
                    }

                    $with = $arr[0];
                    $param = explode(',', $arr[1]);
                    if (count($param) < 2) {
                        continue;
                    }

                    $column = $param[0];
                    $value = [];
                    for ($i = 1; $i < count($param); $i++) {
                        $value[] = $param[$i];
                    }
                    if (count($value) == 1) {
                        $value = $value[0];
                    }

                    $this->pushCriteria(new WithRelationshipCriteria($with, $column, $value));
                }

                unset($query['searchInclude']);
            }

            if (array_key_exists('searchHas', $query) and $query['searchHas']) {
                $searchHas = explode(',', $query['searchHas']);

                foreach ($searchHas as $_item) {
                    $arr = explode(':', $_item);
                    $field = $arr[0];
                    $val = isset($arr[1]) ? $arr[1] : null;

                    if ($val == 'not') {
                        $this->pushCriteria(new WithRelationshipCriteria($field, type: 'doesntHave'));
                    } else {
                        $this->pushCriteria(new WithRelationshipCriteria($field, type: 'has'));
                    }
                }
            }

            if (array_key_exists('isRandom', $query) and $query['isRandom']) {
                $this->pushCriteria(new RandomCriteria());
            }

            $operator = [];
            if (array_key_exists('searchFields', $query)) {
                $fields = explode(';', $query['searchFields']);
                foreach ($fields as $item) {
                    $arr = explode(':', $item);
                    $operator[$arr[0]] = isset($arr[1]) ? $arr[1] : '=';
                }

                unset($query['searchFields']);
            }
            
            foreach ($query as $field => $value) {
                if (array_key_exists($field, $this->fieldSearchable)) {
                    $condition = isset($operator[$field]) ? $operator[$field] : $this->fieldSearchable[$field];

                    if ($condition == '=') {
                        $this->pushCriteria(new ThisEqualThatCriteria($field, $value));
                    } elseif ($condition == 'like') {
                        $this->pushCriteria(new ThisLikeThatCriteria($field, '*' . $value . '*'));
                    } else {
                        if (in_array($condition, ['in','notin','between'])) {
                            $value = explode(',', $value);
                        }
                        
                        $this->pushCriteria(new ThisConditionThatCriteria($field, $condition, $value));
                    }
                }
            }
        }
    }

    public static function instance(): static
    {
        return app(static::class)->resetCriteria()->resetScope();
    }

    public static function builder()
    {
        return static::instance()->getModel()->query();
    }
}
