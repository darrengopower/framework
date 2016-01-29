<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 18:54
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Collection;
/**
 * Class MorphOne
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
class MorphOne extends MorphOneOrMany {
    /**
     * @return mixed
     */
    public function getResults() {
        return $this->query->first();
    }
    /**
     * @param array $models
     * @param string $relation
     * @return array
     */
    public function initRelation(array $models, $relation) {
        foreach($models as $model) {
            $model->setRelation($relation, null);
        }
        return $models;
    }
    /**
     * @param array $models
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation) {
        return $this->matchOne($models, $results, $relation);
    }
}