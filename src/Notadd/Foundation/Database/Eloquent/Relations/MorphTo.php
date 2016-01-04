<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 18:56
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Collection;
use Notadd\Foundation\Database\Eloquent\Model;
/**
 * Class MorphTo
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
class MorphTo extends BelongsTo {
    /**
     * @var string
     */
    protected $morphType;
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Collection
     */
    protected $models;
    /**
     * @var array
     */
    protected $dictionary = [];
    /*
     * @var bool
     */
    protected $withTrashed = false;
    /**
     * MorphTo constructor.
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $type
     * @param string $relation
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey, $type, $relation) {
        $this->morphType = $type;
        parent::__construct($query, $parent, $foreignKey, $otherKey, $relation);
    }
    /**
     * @return mixed
     */
    public function getResults() {
        if(!$this->otherKey) {
            return;
        }
        return $this->query->first();
    }
    /**
     * @param array $models
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $this->buildDictionary($this->models = Collection::make($models));
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Collection $models
     * @return void
     */
    protected function buildDictionary(Collection $models) {
        foreach($models as $model) {
            if($model->{$this->morphType}) {
                $this->dictionary[$model->{$this->morphType}][$model->{$this->foreignKey}][] = $model;
            }
        }
    }
    /**
     * @param array $models
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation) {
        foreach(array_keys($this->dictionary) as $type) {
            $this->matchToMorphParents($type, $this->getResultsByType($type), $relation);
        }
        return $models;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function associate($model) {
        $this->parent->setAttribute($this->foreignKey, $model->getKey());
        $this->parent->setAttribute($this->morphType, $model->getMorphClass());
        return $this->parent->setRelation($this->relation, $model);
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function dissociate() {
        $this->parent->setAttribute($this->foreignKey, null);
        $this->parent->setAttribute($this->morphType, null);
        return $this->parent->setRelation($this->relation, null);
    }
    /**
     * @param string $type
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     */
    protected function matchToMorphParents($type, Collection $results, $relation) {
        foreach($results as $result) {
            if(isset($this->dictionary[$type][$result->getKey()])) {
                foreach($this->dictionary[$type][$result->getKey()] as $model) {
                    $model->setRelation($relation, $result);
                }
            }
        }
    }
    /**
     * @param string $type
     * @return \Notadd\Foundation\Database\Eloquent\Collection
     */
    protected function getResultsByType($type) {
        $instance = $this->createModelByType($type);
        $key = $instance->getTable() . '.' . $instance->getKeyName();
        $query = $instance->newQuery();
        $query = $this->useWithTrashed($query);
        return $query->whereIn($key, $this->gatherKeysByType($type)->all())->get();
    }
    /**
     * @param string $type
     * @return array
     */
    protected function gatherKeysByType($type) {
        $foreign = $this->foreignKey;
        return collect($this->dictionary[$type])->map(function ($models) use ($foreign) {
            return head($models)->{$foreign};
        })->values()->unique();
    }
    /**
     * @param string $type
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function createModelByType($type) {
        $class = $this->parent->getActualClassNameForMorph($type);
        return new $class;
    }
    /**
     * @return string
     */
    public function getMorphType() {
        return $this->morphType;
    }
    /**
     * @return array
     */
    public function getDictionary() {
        return $this->dictionary;
    }
    /**
     * @return $this
     */
    public function withTrashed() {
        $this->withTrashed = true;
        $this->query = $this->useWithTrashed($this->query);
        return $this;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    protected function useWithTrashed(Builder $query) {
        if($this->withTrashed && $query->getMacro('withTrashed') !== null) {
            return $query->withTrashed();
        }
        return $query;
    }
}