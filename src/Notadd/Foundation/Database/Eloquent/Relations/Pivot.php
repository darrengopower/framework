<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 14:06
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Model;
/**
 * Class Pivot
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
class Pivot extends Model {
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Model
     */
    protected $parent;
    /**
     * @var string
     */
    protected $foreignKey;
    /**
     * @var string
     */
    protected $otherKey;
    /**
     * @var array
     */
    protected $guarded = [];
    /**
     * Pivot constructor.
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $attributes
     * @param string $table
     * @param bool $exists
     */
    public function __construct(Model $parent, $attributes, $table, $exists = false) {
        parent::__construct();
        $this->setTable($table);
        $this->setConnection($parent->getConnectionName());
        $this->forceFill($attributes);
        $this->syncOriginal();
        $this->parent = $parent;
        $this->exists = $exists;
        $this->timestamps = $this->hasTimestampAttributes();
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query) {
        $query->where($this->foreignKey, $this->getAttribute($this->foreignKey));
        return $query->where($this->otherKey, $this->getAttribute($this->otherKey));
    }
    /**
     * @return int
     */
    public function delete() {
        return $this->getDeleteQuery()->delete();
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    protected function getDeleteQuery() {
        $foreign = $this->getAttribute($this->foreignKey);
        $query = $this->newQuery()->where($this->foreignKey, $foreign);
        return $query->where($this->otherKey, $this->getAttribute($this->otherKey));
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
    public function getOtherKey() {
        return $this->otherKey;
    }
    /**
     * @param string $foreignKey
     * @param string $otherKey
     * @return $this
     */
    public function setPivotKeys($foreignKey, $otherKey) {
        $this->foreignKey = $foreignKey;
        $this->otherKey = $otherKey;
        return $this;
    }
    /**
     * @return bool
     */
    public function hasTimestampAttributes() {
        return array_key_exists($this->getCreatedAtColumn(), $this->attributes);
    }
    /**
     * @return string
     */
    public function getCreatedAtColumn() {
        return $this->parent->getCreatedAtColumn();
    }
    /**
     * @return string
     */
    public function getUpdatedAtColumn() {
        return $this->parent->getUpdatedAtColumn();
    }
}