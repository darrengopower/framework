<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 14:16
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Collection;
use Notadd\Foundation\Database\Eloquent\Model;
use Notadd\Foundation\Database\Query\Expression;
/**
 * Class HasOneOrMany
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
abstract class HasOneOrMany extends Relation {
    /**
     * The foreign key of the parent model.
     * @var string
     */
    protected $foreignKey;
    /**
     * The local key of the parent model.
     * @var string
     */
    protected $localKey;
    /**
     * HasOneOrMany constructor.
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $foreignKey
     * @param string $localKey
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey) {
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
        parent::__construct($query, $parent);
    }
    /**
     * @return void
     */
    public function addConstraints() {
        if(static::$constraints) {
            $this->query->where($this->foreignKey, '=', $this->getParentKey());
            $this->query->whereNotNull($this->foreignKey);
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
        return parent::getRelationCountQuery($query, $parent);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQueryForSelfRelation(Builder $query, Builder $parent) {
        $query->select(new Expression('count(*)'));
        $query->from($query->getModel()->getTable() . ' as ' . $hash = $this->getRelationCountHash());
        $key = $this->wrap($this->getQualifiedParentKeyName());
        return $query->where($hash . '.' . $this->getPlainForeignKey(), '=', new Expression($key));
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
        $this->query->whereIn($this->foreignKey, $this->getKeys($models, $this->localKey));
    }
    /**
     * @param array $models
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     * @return array
     */
    public function matchOne(array $models, Collection $results, $relation) {
        return $this->matchOneOrMany($models, $results, $relation, 'one');
    }
    /**
     * @param array $models
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     * @return array
     */
    public function matchMany(array $models, Collection $results, $relation) {
        return $this->matchOneOrMany($models, $results, $relation, 'many');
    }
    /**
     * @param array $models
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @param string $relation
     * @param string $type
     * @return array
     */
    protected function matchOneOrMany(array $models, Collection $results, $relation, $type) {
        $dictionary = $this->buildDictionary($results);
        foreach($models as $model) {
            $key = $model->getAttribute($this->localKey);
            if(isset($dictionary[$key])) {
                $value = $this->getRelationValue($dictionary, $key, $type);
                $model->setRelation($relation, $value);
            }
        }
        return $models;
    }
    /**
     * @param array $dictionary
     * @param string $key
     * @param string $type
     * @return mixed
     */
    protected function getRelationValue(array $dictionary, $key, $type) {
        $value = $dictionary[$key];
        return $type == 'one' ? reset($value) : $this->related->newCollection($value);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results) {
        $dictionary = [];
        $foreign = $this->getPlainForeignKey();
        foreach($results as $result) {
            $dictionary[$result->{$foreign}][] = $result;
        }
        return $dictionary;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function save(Model $model) {
        $model->setAttribute($this->getPlainForeignKey(), $this->getParentKey());
        return $model->save() ? $model : false;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Collection|array $models
     * @return \Notadd\Foundation\Database\Eloquent\Collection|array
     */
    public function saveMany($models) {
        foreach($models as $model) {
            $this->save($model);
        }
        return $models;
    }
    /**
     * @param mixed $id
     * @param array $columns
     * @return \Illuminate\Support\Collection|\Notadd\Foundation\Database\Eloquent\Model
     */
    public function findOrNew($id, $columns = ['*']) {
        if(is_null($instance = $this->find($id, $columns))) {
            $instance = $this->related->newInstance();
            $instance->setAttribute($this->getPlainForeignKey(), $this->getParentKey());
        }
        return $instance;
    }
    /**
     * @param array $attributes
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function firstOrNew(array $attributes) {
        if(is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->related->newInstance($attributes);
            $instance->setAttribute($this->getPlainForeignKey(), $this->getParentKey());
        }
        return $instance;
    }
    /**
     * @param array $attributes
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes) {
        if(is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->create($attributes);
        }
        return $instance;
    }
    /**
     * @param array $attributes
     * @param array $values
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function updateOrCreate(array $attributes, array $values = []) {
        $instance = $this->firstOrNew($attributes);
        $instance->fill($values);
        $instance->save();
        return $instance;
    }
    /**
     * @param array $attributes
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function create(array $attributes) {
        $instance = $this->related->newInstance($attributes);
        $instance->setAttribute($this->getPlainForeignKey(), $this->getParentKey());
        $instance->save();
        return $instance;
    }
    /**
     * @param array $records
     * @return array
     */
    public function createMany(array $records) {
        $instances = [];
        foreach($records as $record) {
            $instances[] = $this->create($record);
        }
        return $instances;
    }
    /**
     * @param array $attributes
     * @return int
     */
    public function update(array $attributes) {
        if($this->related->usesTimestamps()) {
            $attributes[$this->relatedUpdatedAt()] = $this->related->freshTimestampString();
        }
        return $this->query->update($attributes);
    }
    /**
     * @return string
     */
    public function getHasCompareKey() {
        return $this->getForeignKey();
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
    public function getPlainForeignKey() {
        $segments = explode('.', $this->getForeignKey());
        return $segments[count($segments) - 1];
    }
    /**
     * @return mixed
     */
    public function getParentKey() {
        return $this->parent->getAttribute($this->localKey);
    }
    /**
     * @return string
     */
    public function getQualifiedParentKeyName() {
        return $this->parent->getTable() . '.' . $this->localKey;
    }
}