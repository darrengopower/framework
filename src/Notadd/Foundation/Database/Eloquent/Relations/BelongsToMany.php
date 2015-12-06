<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 14:11
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Collection;
use Notadd\Foundation\Database\Eloquent\Model;
use Notadd\Foundation\Database\Eloquent\ModelNotFoundException;
use Notadd\Foundation\Database\Query\Expression;
class BelongsToMany extends Relation {
    /**
     * @var string
     */
    protected $table;
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
    protected $relationName;
    /**
     * @var array
     */
    protected $pivotColumns = [];
    /**
     * @var array
     */
    protected $pivotWheres = [];
    /**
     * @var array
     */
    protected $pivotCreatedAt;
    /**
     * @var array
     */
    protected $pivotUpdatedAt;
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $table
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relationName
     */
    public function __construct(Builder $query, Model $parent, $table, $foreignKey, $otherKey, $relationName = null) {
        $this->table = $table;
        $this->otherKey = $otherKey;
        $this->foreignKey = $foreignKey;
        $this->relationName = $relationName;
        parent::__construct($query, $parent);
    }
    /**
     * @return mixed
     */
    public function getResults() {
        return $this->get();
    }
    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Eloquent\Relations\BelongsToMany
     */
    public function wherePivot($column, $operator = null, $value = null, $boolean = 'and') {
        $this->pivotWheres[] = func_get_args();
        return $this->where($this->table . '.' . $column, $operator, $value, $boolean);
    }
    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return \Notadd\Foundation\Database\Eloquent\Relations\BelongsToMany
     */
    public function orWherePivot($column, $operator = null, $value = null) {
        return $this->wherePivot($column, $operator, $value, 'or');
    }
    /**
     * @param array $columns
     * @return mixed
     */
    public function first($columns = ['*']) {
        $results = $this->take(1)->get($columns);
        return count($results) > 0 ? $results->first() : null;
    }
    /**
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Model|static
     * @throws \Notadd\Foundation\Database\Eloquent\ModelNotFoundException
     */
    public function firstOrFail($columns = ['*']) {
        if(!is_null($model = $this->first($columns))) {
            return $model;
        }
        throw new ModelNotFoundException;
    }
    /**
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Collection
     */
    public function get($columns = ['*']) {
        $columns = $this->query->getQuery()->columns ? [] : $columns;
        $select = $this->getSelectColumns($columns);
        $models = $this->query->addSelect($select)->getModels();
        $this->hydratePivotRelation($models);
        if(count($models) > 0) {
            $models = $this->query->eagerLoadRelations($models);
        }
        return $this->related->newCollection($models);
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page') {
        $this->query->addSelect($this->getSelectColumns($columns));
        $paginator = $this->query->paginate($perPage, $columns, $pageName);
        $this->hydratePivotRelation($paginator->items());
        return $paginator;
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*']) {
        $this->query->addSelect($this->getSelectColumns($columns));
        $paginator = $this->query->simplePaginate($perPage, $columns);
        $this->hydratePivotRelation($paginator->items());
        return $paginator;
    }
    /**
     * @param int $count
     * @param callable $callback
     * @return void
     */
    public function chunk($count, callable $callback) {
        $this->query->addSelect($this->getSelectColumns());
        $this->query->chunk($count, function ($results) use ($callback) {
            $this->hydratePivotRelation($results->all());
            call_user_func($callback, $results);
        });
    }
    /**
     * @param array $models
     * @return void
     */
    protected function hydratePivotRelation(array $models) {
        foreach($models as $model) {
            $pivot = $this->newExistingPivot($this->cleanPivotAttributes($model));
            $model->setRelation('pivot', $pivot);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return array
     */
    protected function cleanPivotAttributes(Model $model) {
        $values = [];
        foreach($model->getAttributes() as $key => $value) {
            if(strpos($key, 'pivot_') === 0) {
                $values[substr($key, 6)] = $value;
                unset($model->$key);
            }
        }
        return $values;
    }
    /**
     * @return void
     */
    public function addConstraints() {
        $this->setJoin();
        if(static::$constraints) {
            $this->setWhere();
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQuery(Builder $query, Builder $parent) {
        if($parent->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationCountQueryForSelfJoin($query, $parent);
        }
        $this->setJoin($query);
        return parent::getRelationCountQuery($query, $parent);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQueryForSelfJoin(Builder $query, Builder $parent) {
        $query->select(new Expression('count(*)'));
        $query->from($this->table . ' as ' . $hash = $this->getRelationCountHash());
        $key = $this->wrap($this->getQualifiedParentKeyName());
        return $query->where($hash . '.' . $this->foreignKey, '=', new Expression($key));
    }
    /**
     * @return string
     */
    public function getRelationCountHash() {
        return 'self_' . md5(microtime(true));
    }
    /**
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Relations\BelongsToMany
     */
    protected function getSelectColumns(array $columns = ['*']) {
        if($columns == ['*']) {
            $columns = [$this->related->getTable() . '.*'];
        }
        return array_merge($columns, $this->getAliasedPivotColumns());
    }
    /**
     * @return array
     */
    protected function getAliasedPivotColumns() {
        $defaults = [
            $this->foreignKey,
            $this->otherKey
        ];
        $columns = [];
        foreach(array_merge($defaults, $this->pivotColumns) as $column) {
            $columns[] = $this->table . '.' . $column . ' as pivot_' . $column;
        }
        return array_unique($columns);
    }
    /**
     * @param string $column
     * @return bool
     */
    protected function hasPivotColumn($column) {
        return in_array($column, $this->pivotColumns);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder|null $query
     * @return $this
     */
    protected function setJoin($query = null) {
        $query = $query ?: $this->query;
        $baseTable = $this->related->getTable();
        $key = $baseTable . '.' . $this->related->getKeyName();
        $query->join($this->table, $key, '=', $this->getOtherKey());
        return $this;
    }
    /**
     * @return $this
     */
    protected function setWhere() {
        $foreign = $this->getForeignKey();
        $this->query->where($foreign, '=', $this->parent->getKey());
        return $this;
    }
    /**
     * @param array $models
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $this->query->whereIn($this->getForeignKey(), $this->getKeys($models));
    }
    /**
     * @param array $models
     * @param string $relation
     * @return array
     */
    public function initRelation(array $models, $relation) {
        foreach($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
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
        $dictionary = $this->buildDictionary($results);
        foreach($models as $model) {
            if(isset($dictionary[$key = $model->getKey()])) {
                $collection = $this->related->newCollection($dictionary[$key]);
                $model->setRelation($relation, $collection);
            }
        }
        return $models;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results) {
        $foreign = $this->foreignKey;
        $dictionary = [];
        foreach($results as $result) {
            $dictionary[$result->pivot->$foreign][] = $result;
        }
        return $dictionary;
    }
    /**
     * @return void
     */
    public function touch() {
        $key = $this->getRelated()->getKeyName();
        $columns = $this->getRelatedFreshUpdate();
        $ids = $this->getRelatedIds();
        if(count($ids) > 0) {
            $this->getRelated()->newQuery()->whereIn($key, $ids)->update($columns);
        }
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRelatedIds() {
        $related = $this->getRelated();
        $fullKey = $related->getQualifiedKeyName();
        return $this->getQuery()->select($fullKey)->lists($related->getKeyName());
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @param array $joining
     * @param bool $touch
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function save(Model $model, array $joining = [], $touch = true) {
        $model->save(['touch' => false]);
        $this->attach($model->getKey(), $joining, $touch);
        return $model;
    }
    /**
     * @param array $models
     * @param array $joinings
     * @return array
     */
    public function saveMany(array $models, array $joinings = []) {
        foreach($models as $key => $model) {
            $this->save($model, (array)Arr::get($joinings, $key), false);
        }
        $this->touchIfTouching();
        return $models;
    }
    /**
     * @param mixed $id
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Model|\Notadd\Foundation\Database\Eloquent\Collection|null
     */
    public function find($id, $columns = ['*']) {
        if(is_array($id)) {
            return $this->findMany($id, $columns);
        }
        $this->where($this->getRelated()->getQualifiedKeyName(), '=', $id);
        return $this->first($columns);
    }
    /**
     * @param mixed $ids
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Collection
     */
    public function findMany($ids, $columns = ['*']) {
        if(empty($ids)) {
            return $this->getRelated()->newCollection();
        }
        $this->whereIn($this->getRelated()->getQualifiedKeyName(), $ids);
        return $this->get($columns);
    }
    /**
     * @param mixed $id
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Model|\Notadd\Foundation\Database\Eloquent\Collection
     * @throws \Notadd\Foundation\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, $columns = ['*']) {
        $result = $this->find($id, $columns);
        if(is_array($id)) {
            if(count($result) == count(array_unique($id))) {
                return $result;
            }
        } elseif(!is_null($result)) {
            return $result;
        }
        throw (new ModelNotFoundException)->setModel(get_class($this->parent));
    }
    /**
     * @param mixed $id
     * @param array $columns
     * @return \Illuminate\Support\Collection|\Notadd\Foundation\Database\Eloquent\Model
     */
    public function findOrNew($id, $columns = ['*']) {
        if(is_null($instance = $this->find($id, $columns))) {
            $instance = $this->getRelated()->newInstance();
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
        }
        return $instance;
    }
    /**
     * @param array $attributes
     * @param array $joining
     * @param bool $touch
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function firstOrCreate(array $attributes, array $joining = [], $touch = true) {
        if(is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->create($attributes, $joining, $touch);
        }
        return $instance;
    }
    /**
     * @param array $attributes
     * @param array $values
     * @param array $joining
     * @param bool $touch
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function updateOrCreate(array $attributes, array $values = [], array $joining = [], $touch = true) {
        if(is_null($instance = $this->where($attributes)->first())) {
            return $this->create($values, $joining, $touch);
        }
        $instance->fill($values);
        $instance->save(['touch' => false]);
        return $instance;
    }
    /**
     * @param array $attributes
     * @param array $joining
     * @param bool $touch
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function create(array $attributes, array $joining = [], $touch = true) {
        $instance = $this->related->newInstance($attributes);
        $instance->save(['touch' => false]);
        $this->attach($instance->getKey(), $joining, $touch);
        return $instance;
    }
    /**
     * @param array $records
     * @param array $joinings
     * @return array
     */
    public function createMany(array $records, array $joinings = []) {
        $instances = [];
        foreach($records as $key => $record) {
            $instances[] = $this->create($record, (array)Arr::get($joinings, $key), false);
        }
        $this->touchIfTouching();
        return $instances;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Collection|array $ids
     * @param bool $detaching
     * @return array
     */
    public function sync($ids, $detaching = true) {
        $changes = [
            'attached' => [],
            'detached' => [],
            'updated' => [],
        ];
        if($ids instanceof Collection) {
            $ids = $ids->modelKeys();
        }
        $current = $this->newPivotQuery()->lists($this->otherKey);
        $records = $this->formatSyncList($ids);
        $detach = array_diff($current, array_keys($records));
        if($detaching && count($detach) > 0) {
            $this->detach($detach);
            $changes['detached'] = (array)array_map(function ($v) {
                return is_numeric($v) ? (int)$v : (string)$v;
            }, $detach);
        }
        $changes = array_merge($changes, $this->attachNew($records, $current, false));
        if(count($changes['attached']) || count($changes['updated'])) {
            $this->touchIfTouching();
        }
        return $changes;
    }
    /**
     * @param array $records
     * @return array
     */
    protected function formatSyncList(array $records) {
        $results = [];
        foreach($records as $id => $attributes) {
            if(!is_array($attributes)) {
                list($id, $attributes) = [
                    $attributes,
                    []
                ];
            }
            $results[$id] = $attributes;
        }
        return $results;
    }
    /**
     * @param array $records
     * @param array $current
     * @param bool $touch
     * @return array
     */
    protected function attachNew(array $records, array $current, $touch = true) {
        $changes = [
            'attached' => [],
            'updated' => []
        ];
        foreach($records as $id => $attributes) {
            if(!in_array($id, $current)) {
                $this->attach($id, $attributes, $touch);
                $changes['attached'][] = is_numeric($id) ? (int)$id : (string)$id;
            }
            elseif(count($attributes) > 0 && $this->updateExistingPivot($id, $attributes, $touch)) {
                $changes['updated'][] = is_numeric($id) ? (int)$id : (string)$id;
            }
        }
        return $changes;
    }
    /**
     * @param mixed $id
     * @param array $attributes
     * @param bool $touch
     * @return int
     */
    public function updateExistingPivot($id, array $attributes, $touch = true) {
        if(in_array($this->updatedAt(), $this->pivotColumns)) {
            $attributes = $this->setTimestampsOnAttach($attributes, true);
        }
        $updated = $this->newPivotStatementForId($id)->update($attributes);
        if($touch) {
            $this->touchIfTouching();
        }
        return $updated;
    }
    /**
     * @param mixed $id
     * @param array $attributes
     * @param bool $touch
     * @return void
     */
    public function attach($id, array $attributes = [], $touch = true) {
        if($id instanceof Model) {
            $id = $id->getKey();
        }
        $query = $this->newPivotStatement();
        $query->insert($this->createAttachRecords((array)$id, $attributes));
        if($touch) {
            $this->touchIfTouching();
        }
    }
    /**
     * @param array $ids
     * @param array $attributes
     * @return array
     */
    protected function createAttachRecords($ids, array $attributes) {
        $records = [];
        $timed = ($this->hasPivotColumn($this->createdAt()) || $this->hasPivotColumn($this->updatedAt()));
        foreach($ids as $key => $value) {
            $records[] = $this->attacher($key, $value, $attributes, $timed);
        }
        return $records;
    }
    /**
     * @param int $key
     * @param mixed $value
     * @param array $attributes
     * @param bool $timed
     * @return array
     */
    protected function attacher($key, $value, $attributes, $timed) {
        list($id, $extra) = $this->getAttachId($key, $value, $attributes);
        $record = $this->createAttachRecord($id, $timed);
        return array_merge($record, $extra);
    }
    /**
     * @param mixed $key
     * @param mixed $value
     * @param array $attributes
     * @return array
     */
    protected function getAttachId($key, $value, array $attributes) {
        if(is_array($value)) {
            return [
                $key,
                array_merge($value, $attributes)
            ];
        }
        return [
            $value,
            $attributes
        ];
    }
    /**
     * @param int $id
     * @param bool $timed
     * @return array
     */
    protected function createAttachRecord($id, $timed) {
        $record[$this->foreignKey] = $this->parent->getKey();
        $record[$this->otherKey] = $id;
        if($timed) {
            $record = $this->setTimestampsOnAttach($record);
        }
        return $record;
    }
    /**
     * @param array $record
     * @param bool $exists
     * @return array
     */
    protected function setTimestampsOnAttach(array $record, $exists = false) {
        $fresh = $this->parent->freshTimestamp();
        if(!$exists && $this->hasPivotColumn($this->createdAt())) {
            $record[$this->createdAt()] = $fresh;
        }
        if($this->hasPivotColumn($this->updatedAt())) {
            $record[$this->updatedAt()] = $fresh;
        }
        return $record;
    }
    /**
     * @param int|array $ids
     * @param bool $touch
     * @return int
     */
    public function detach($ids = [], $touch = true) {
        if($ids instanceof Model) {
            $ids = (array)$ids->getKey();
        }
        $query = $this->newPivotQuery();
        $ids = (array)$ids;
        if(count($ids) > 0) {
            $query->whereIn($this->otherKey, (array)$ids);
        }
        $results = $query->delete();
        if($touch) {
            $this->touchIfTouching();
        }
        return $results;
    }
    /**
     * @return void
     */
    public function touchIfTouching() {
        if($this->touchingParent()) {
            $this->getParent()->touch();
        }
        if($this->getParent()->touches($this->relationName)) {
            $this->touch();
        }
    }
    /**
     * @return bool
     */
    protected function touchingParent() {
        return $this->getRelated()->touches($this->guessInverseRelation());
    }
    /**
     * @return string
     */
    protected function guessInverseRelation() {
        return Str::camel(Str::plural(class_basename($this->getParent())));
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    protected function newPivotQuery() {
        $query = $this->newPivotStatement();
        foreach($this->pivotWheres as $whereArgs) {
            call_user_func_array([
                $query,
                'where'
            ], $whereArgs);
        }
        return $query->where($this->foreignKey, $this->parent->getKey());
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function newPivotStatement() {
        return $this->query->getQuery()->newQuery()->from($this->table);
    }
    /**
     * @param mixed $id
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function newPivotStatementForId($id) {
        return $this->newPivotQuery()->where($this->otherKey, $id);
    }
    /**
     * @param array $attributes
     * @param bool $exists
     * @return \Notadd\Foundation\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(array $attributes = [], $exists = false) {
        $pivot = $this->related->newPivot($this->parent, $attributes, $this->table, $exists);
        return $pivot->setPivotKeys($this->foreignKey, $this->otherKey);
    }
    /**
     * @param array $attributes
     * @return \Notadd\Foundation\Database\Eloquent\Relations\Pivot
     */
    public function newExistingPivot(array $attributes = []) {
        return $this->newPivot($attributes, true);
    }
    /**
     * @param array|mixed $columns
     * @return $this
     */
    public function withPivot($columns) {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->pivotColumns = array_merge($this->pivotColumns, $columns);
        return $this;
    }
    /**
     * @param mixed $createdAt
     * @param mixed $updatedAt
     * @return \Notadd\Foundation\Database\Eloquent\Relations\BelongsToMany
     */
    public function withTimestamps($createdAt = null, $updatedAt = null) {
        $this->pivotCreatedAt = $createdAt;
        $this->pivotUpdatedAt = $updatedAt;
        return $this->withPivot($this->createdAt(), $this->updatedAt());
    }
    /**
     * @return string
     */
    public function createdAt() {
        return $this->pivotCreatedAt ?: $this->parent->getCreatedAtColumn();
    }
    /**
     * @return string
     */
    public function updatedAt() {
        return $this->pivotUpdatedAt ?: $this->parent->getUpdatedAtColumn();
    }
    /**
     * @return string
     */
    public function getRelatedFreshUpdate() {
        return [$this->related->getUpdatedAtColumn() => $this->related->freshTimestamp()];
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
        return $this->table . '.' . $this->foreignKey;
    }
    /**
     * @return string
     */
    public function getOtherKey() {
        return $this->table . '.' . $this->otherKey;
    }
    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }
    /**
     * @return string
     */
    public function getRelationName() {
        return $this->relationName;
    }
}