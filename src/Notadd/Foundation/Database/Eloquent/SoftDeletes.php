<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:48
 */
namespace Notadd\Foundation\Database\Eloquent;
trait SoftDeletes {
    /**
     * @var bool
     */
    protected $forceDeleting = false;
    /**
     * @return void
     */
    public static function bootSoftDeletes() {
        static::addGlobalScope(new SoftDeletingScope);
    }
    /**
     * @return void
     */
    public function forceDelete() {
        $this->forceDeleting = true;
        $this->delete();
        $this->forceDeleting = false;
    }
    /**
     * @return mixed
     */
    protected function performDeleteOnModel() {
        if($this->forceDeleting) {
            return $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey())->forceDelete();
        }
        return $this->runSoftDelete();
    }
    /**
     * @return void
     */
    protected function runSoftDelete() {
        $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());
        $this->{$this->getDeletedAtColumn()} = $time = $this->freshTimestamp();
        $query->update([$this->getDeletedAtColumn() => $this->fromDateTime($time)]);
    }
    /**
     * @return bool|null
     */
    public function restore() {
        if($this->fireModelEvent('restoring') === false) {
            return false;
        }
        $this->{$this->getDeletedAtColumn()} = null;
        $this->exists = true;
        $result = $this->save();
        $this->fireModelEvent('restored', false);
        return $result;
    }
    /**
     * @return bool
     */
    public function trashed() {
        return !is_null($this->{$this->getDeletedAtColumn()});
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public static function withTrashed() {
        return (new static)->newQueryWithoutScope(new SoftDeletingScope);
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public static function onlyTrashed() {
        $instance = new static;
        $column = $instance->getQualifiedDeletedAtColumn();
        return $instance->newQueryWithoutScope(new SoftDeletingScope)->whereNotNull($column);
    }
    /**
     * @param \Closure|string $callback
     * @return void
     */
    public static function restoring($callback) {
        static::registerModelEvent('restoring', $callback);
    }
    /**
     * @param \Closure|string $callback
     * @return void
     */
    public static function restored($callback) {
        static::registerModelEvent('restored', $callback);
    }
    /**
     * Get the name of the "deleted at" column.
     * @return string
     */
    public function getDeletedAtColumn() {
        return defined('static::DELETED_AT') ? static::DELETED_AT : 'deleted_at';
    }
    /**
     * @return string
     */
    public function getQualifiedDeletedAtColumn() {
        return $this->getTable() . '.' . $this->getDeletedAtColumn();
    }
}