<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 18:58
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Illuminate\Support\Arr;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Model;
class MorphToMany extends BelongsToMany {
    /**
     * @var string
     */
    protected $morphType;
    /**
     * @var string
     */
    protected $morphClass;
    /**
     * @var bool
     */
    protected $inverse;
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $name
     * @param string $table
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relationName
     * @param bool $inverse
     */
    public function __construct(Builder $query, Model $parent, $name, $table, $foreignKey, $otherKey, $relationName = null, $inverse = false) {
        $this->inverse = $inverse;
        $this->morphType = $name . '_type';
        $this->morphClass = $inverse ? $query->getModel()->getMorphClass() : $parent->getMorphClass();
        parent::__construct($query, $parent, $table, $foreignKey, $otherKey, $relationName);
    }
    /**
     * @return $this
     */
    protected function setWhere() {
        parent::setWhere();
        $this->query->where($this->table . '.' . $this->morphType, $this->morphClass);
        return $this;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQuery(Builder $query, Builder $parent) {
        $query = parent::getRelationCountQuery($query, $parent);
        return $query->where($this->table . '.' . $this->morphType, $this->morphClass);
    }
    /**
     * @param array $models
     * @return void
     */
    public function addEagerConstraints(array $models) {
        parent::addEagerConstraints($models);
        $this->query->where($this->table . '.' . $this->morphType, $this->morphClass);
    }
    /**
     * @param int $id
     * @param bool $timed
     * @return array
     */
    protected function createAttachRecord($id, $timed) {
        $record = parent::createAttachRecord($id, $timed);
        return Arr::add($record, $this->morphType, $this->morphClass);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    protected function newPivotQuery() {
        $query = parent::newPivotQuery();
        return $query->where($this->morphType, $this->morphClass);
    }
    /**
     * @param array $attributes
     * @param bool $exists
     * @return \Notadd\Foundation\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(array $attributes = [], $exists = false) {
        $pivot = new MorphPivot($this->parent, $attributes, $this->table, $exists);
        $pivot->setPivotKeys($this->foreignKey, $this->otherKey)->setMorphType($this->morphType)->setMorphClass($this->morphClass);
        return $pivot;
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
    public function getMorphClass() {
        return $this->morphClass;
    }
}