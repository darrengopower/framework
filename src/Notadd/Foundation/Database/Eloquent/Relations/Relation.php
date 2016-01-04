<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:51
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Closure;
use Illuminate\Support\Arr;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Collection;
use Notadd\Foundation\Database\Eloquent\Model;
use Notadd\Foundation\Database\Query\Expression;
/**
 * Class Relation
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
abstract class Relation {
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Builder
     */
    protected $query;
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Model
     */
    protected $parent;
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Model
     */
    protected $related;
    /**
     * @var bool
     */
    protected static $constraints = true;
    /**
     * @var array
     */
    protected static $morphMap = [];
    /**
     * Relation constructor.
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     */
    public function __construct(Builder $query, Model $parent) {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();
        $this->addConstraints();
    }
    /**
     * @return void
     */
    abstract public function addConstraints();
    /**
     * @param array $models
     * @return void
     */
    abstract public function addEagerConstraints(array $models);
    /**
     * @param array $models
     * @param string $relation
     * @return array
     */
    abstract public function initRelation(array $models, $relation);
    /**
     * @param array $models
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     * @return array
     */
    abstract public function match(array $models, Collection $results, $relation);
    /**
     * @return mixed
     */
    abstract public function getResults();
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Collection
     */
    public function getEager() {
        return $this->get();
    }
    /**
     * @return void
     */
    public function touch() {
        $column = $this->getRelated()->getUpdatedAtColumn();
        $this->rawUpdate([$column => $this->getRelated()->freshTimestampString()]);
    }
    /**
     * @param array $attributes
     * @return int
     */
    public function rawUpdate(array $attributes = []) {
        return $this->query->update($attributes);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQuery(Builder $query, Builder $parent) {
        $query->select(new Expression('count(*)'));
        $key = $this->wrap($this->getQualifiedParentKeyName());
        return $query->where($this->getHasCompareKey(), '=', new Expression($key));
    }
    /**
     * @param \Closure $callback
     * @return mixed
     */
    public static function noConstraints(Closure $callback) {
        $previous = static::$constraints;
        static::$constraints = false;
        try {
            $results = call_user_func($callback);
        } finally {
            static::$constraints = $previous;
        }
        return $results;
    }
    /**
     * @param array $models
     * @param string $key
     * @return array
     */
    protected function getKeys(array $models, $key = null) {
        return array_unique(array_values(array_map(function ($value) use ($key) {
            return $key ? $value->getAttribute($key) : $value->getKey();
        }, $models)));
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getQuery() {
        return $this->query;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function getBaseQuery() {
        return $this->query->getQuery();
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function getParent() {
        return $this->parent;
    }
    /**
     * @return string
     */
    public function getQualifiedParentKeyName() {
        return $this->parent->getQualifiedKeyName();
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function getRelated() {
        return $this->related;
    }
    /**
     * @return string
     */
    public function createdAt() {
        return $this->parent->getCreatedAtColumn();
    }
    /**
     * @return string
     */
    public function updatedAt() {
        return $this->parent->getUpdatedAtColumn();
    }
    /**
     * @return string
     */
    public function relatedUpdatedAt() {
        return $this->related->getUpdatedAtColumn();
    }
    /**
     * @param string $value
     * @return string
     */
    public function wrap($value) {
        return $this->parent->newQueryWithoutScopes()->getQuery()->getGrammar()->wrap($value);
    }
    /**
     * @param array|null $map
     * @param bool $merge
     * @return array
     */
    public static function morphMap(array $map = null, $merge = true) {
        $map = static::buildMorphMapFromModels($map);
        if(is_array($map)) {
            static::$morphMap = $merge ? array_merge(static::$morphMap, $map) : $map;
        }
        return static::$morphMap;
    }
    /**
     * @param string[]|null $models
     * @return array|null
     */
    protected static function buildMorphMapFromModels(array $models = null) {
        if(is_null($models) || Arr::isAssoc($models)) {
            return $models;
        }
        $tables = array_map(function ($model) {
            return (new $model)->getTable();
        }, $models);
        return array_combine($tables, $models);
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        $result = call_user_func_array([
            $this->query,
            $method
        ], $parameters);
        if($result === $this->query) {
            return $this;
        }
        return $result;
    }
}