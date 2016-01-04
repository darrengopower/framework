<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 18:15
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Model;
/**
 * Class MorphOneOrMany
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
abstract class MorphOneOrMany extends HasOneOrMany {
    /**
     * @var string
     */
    protected $morphType;
    /**
     * @var string
     */
    protected $morphClass;
    /**
     * MorphOneOrMany constructor.
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $type
     * @param string $id
     * @param string $localKey
     */
    public function __construct(Builder $query, Model $parent, $type, $id, $localKey) {
        $this->morphType = $type;
        $this->morphClass = $parent->getMorphClass();
        parent::__construct($query, $parent, $id, $localKey);
    }
    /**
     * @return void
     */
    public function addConstraints() {
        if(static::$constraints) {
            parent::addConstraints();
            $this->query->where($this->morphType, $this->morphClass);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQuery(Builder $query, Builder $parent) {
        $query = parent::getRelationCountQuery($query, $parent);
        return $query->where($this->morphType, $this->morphClass);
    }
    /**
     * @param array $models
     * @return void
     */
    public function addEagerConstraints(array $models) {
        parent::addEagerConstraints($models);
        $this->query->where($this->morphType, $this->morphClass);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function save(Model $model) {
        $model->setAttribute($this->getPlainMorphType(), $this->morphClass);
        return parent::save($model);
    }
    /**
     * @param mixed $id
     * @param array $columns
     * @return \Illuminate\Support\Collection|\Notadd\Foundation\Database\Eloquent\Model
     */
    public function findOrNew($id, $columns = ['*']) {
        if(is_null($instance = $this->find($id, $columns))) {
            $instance = $this->related->newInstance();
            $this->setForeignAttributesForCreate($instance);
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
            $this->setForeignAttributesForCreate($instance);
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
        $this->setForeignAttributesForCreate($instance);
        $instance->save();
        return $instance;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return void
     */
    protected function setForeignAttributesForCreate(Model $model) {
        $model->{$this->getPlainForeignKey()} = $this->getParentKey();
        $model->{last(explode('.', $this->morphType))} = $this->morphClass;
    }
    /**
     * @return string
     */
    public function getMorphType() {
        return $this->morphType;
    }
    /**
     * @return string
     */
    public function getPlainMorphType() {
        return last(explode('.', $this->morphType));
    }
    /**
     * @return string
     */
    public function getMorphClass() {
        return $this->morphClass;
    }
}