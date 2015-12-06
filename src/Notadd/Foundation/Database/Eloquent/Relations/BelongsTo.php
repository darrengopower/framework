<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 14:08
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Collection;
use Notadd\Foundation\Database\Eloquent\Model;
use Notadd\Foundation\Database\Query\Expression;
class BelongsTo extends Relation {
    /**
     * @var string
     */
    protected $foreignKey;
    /**
     * @var string
     */
    protected $otherKey;
    /**
     * @var string
     */
    protected $relation;
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey, $relation) {
        $this->otherKey = $otherKey;
        $this->relation = $relation;
        $this->foreignKey = $foreignKey;
        parent::__construct($query, $parent);
    }
    /**
     * @return mixed
     */
    public function getResults() {
        return $this->query->first();
    }
    /**
     * @return void
     */
    public function addConstraints() {
        if(static::$constraints) {
            $table = $this->related->getTable();
            $this->query->where($table . '.' . $this->otherKey, '=', $this->parent->{$this->foreignKey});
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQuery(Builder $query, Builder $parent) {
        if($parent->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationCountQueryForSelfRelation($query, $parent);
        }
        $query->select(new Expression('count(*)'));
        $otherKey = $this->wrap($query->getModel()->getTable() . '.' . $this->otherKey);
        return $query->where($this->getQualifiedForeignKey(), '=', new Expression($otherKey));
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQueryForSelfRelation(Builder $query, Builder $parent) {
        $query->select(new Expression('count(*)'));
        $query->from($query->getModel()->getTable() . ' as ' . $hash = $this->getRelationCountHash());
        $key = $this->wrap($this->getQualifiedForeignKey());
        return $query->where($hash . '.' . $query->getModel()->getKeyName(), '=', new Expression($key));
    }
    /**
     * @return string
     */
    public function getRelationCountHash() {
        return 'self_' . md5(microtime(true));
    }
    /**
     * @param array $models
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $key = $this->related->getTable() . '.' . $this->otherKey;
        $this->query->whereIn($key, $this->getEagerModelKeys($models));
    }
    /**
     * @param array $models
     * @return array
     */
    protected function getEagerModelKeys(array $models) {
        $keys = [];
        foreach($models as $model) {
            if(!is_null($value = $model->{$this->foreignKey})) {
                $keys[] = $value;
            }
        }
        if(count($keys) == 0) {
            return [0];
        }
        return array_values(array_unique($keys));
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
        $foreign = $this->foreignKey;
        $other = $this->otherKey;
        $dictionary = [];
        foreach($results as $result) {
            $dictionary[$result->getAttribute($other)] = $result;
        }
        foreach($models as $model) {
            if(isset($dictionary[$model->$foreign])) {
                $model->setRelation($relation, $dictionary[$model->$foreign]);
            }
        }
        return $models;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model|int $model
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function associate($model) {
        $otherKey = ($model instanceof Model ? $model->getAttribute($this->otherKey) : $model);
        $this->parent->setAttribute($this->foreignKey, $otherKey);
        if($model instanceof Model) {
            $this->parent->setRelation($this->relation, $model);
        }
        return $this->parent;
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function dissociate() {
        $this->parent->setAttribute($this->foreignKey, null);
        return $this->parent->setRelation($this->relation, null);
    }
    /**
     * @param array $attributes
     * @return mixed
     */
    public function update(array $attributes) {
        $instance = $this->getResults();
        return $instance->fill($attributes)->save();
    }
    /**
     * @return string
     */
    public function getForeignKey() {
        return $this->foreignKey;
    }
    /**
     * @return string
     */
    public function getQualifiedForeignKey() {
        return $this->parent->getTable() . '.' . $this->foreignKey;
    }
    /**
     * @return string
     */
    public function getOtherKey() {
        return $this->otherKey;
    }
    /**
     * @return string
     */
    public function getQualifiedOtherKeyName() {
        return $this->related->getTable() . '.' . $this->otherKey;
    }
}