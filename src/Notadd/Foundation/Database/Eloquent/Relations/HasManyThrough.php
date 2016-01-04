<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 18:12
 */
namespace Notadd\Foundation\Database\Eloquent\Relations;
use Notadd\Foundation\Database\Eloquent\Builder;
use Notadd\Foundation\Database\Eloquent\Collection;
use Notadd\Foundation\Database\Eloquent\Model;
use Notadd\Foundation\Database\Eloquent\ModelNotFoundException;
use Notadd\Foundation\Database\Query\Expression;
/**
 * Class HasManyThrough
 * @package Notadd\Foundation\Database\Eloquent\Relations
 */
class HasManyThrough extends Relation {
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Model
     */
    protected $farParent;
    /**
     * @var string
     */
    protected $firstKey;
    /**
     * @var string
     */
    protected $secondKey;
    /**
     * @var string
     */
    protected $localKey;
    /**
     * HasManyThrough constructor.
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Model $farParent
     * @param \Notadd\Foundation\Database\Eloquent\Model $parent
     * @param string $firstKey
     * @param string $secondKey
     * @param string $localKey
     */
    public function __construct(Builder $query, Model $farParent, Model $parent, $firstKey, $secondKey, $localKey) {
        $this->localKey = $localKey;
        $this->firstKey = $firstKey;
        $this->secondKey = $secondKey;
        $this->farParent = $farParent;
        parent::__construct($query, $parent);
    }
    /**
     * @return void
     */
    public function addConstraints() {
        $parentTable = $this->parent->getTable();
        $localValue = $this->farParent[$this->localKey];
        $this->setJoin();
        if(static::$constraints) {
            $this->query->where($parentTable . '.' . $this->firstKey, '=', $localValue);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $query
     * @param \Notadd\Foundation\Database\Eloquent\Builder $parent
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    public function getRelationCountQuery(Builder $query, Builder $parent) {
        $parentTable = $this->parent->getTable();
        $this->setJoin($query);
        $query->select(new Expression('count(*)'));
        $key = $this->wrap($parentTable . '.' . $this->firstKey);
        return $query->where($this->getHasCompareKey(), '=', new Expression($key));
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder|null $query
     * @return void
     */
    protected function setJoin(Builder $query = null) {
        $query = $query ?: $this->query;
        $foreignKey = $this->related->getTable() . '.' . $this->secondKey;
        $query->join($this->parent->getTable(), $this->getQualifiedParentKeyName(), '=', $foreignKey);
        if($this->parentSoftDeletes()) {
            $query->whereNull($this->parent->getQualifiedDeletedAtColumn());
        }
    }
    /**
     * @return bool
     */
    public function parentSoftDeletes() {
        return in_array('Notadd\Foundation\Database\Eloquent\SoftDeletes', class_uses_recursive(get_class($this->parent)));
    }
    /**
     * @param array $models
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $table = $this->parent->getTable();
        $this->query->whereIn($table . '.' . $this->firstKey, $this->getKeys($models));
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
            $key = $model->getKey();
            if(isset($dictionary[$key])) {
                $value = $this->related->newCollection($dictionary[$key]);
                $model->setRelation($relation, $value);
            }
        }
        return $models;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results) {
        $dictionary = [];
        $foreign = $this->firstKey;
        foreach($results as $result) {
            $dictionary[$result->{$foreign}][] = $result;
        }
        return $dictionary;
    }
    /**
     * @return mixed
     */
    public function getResults() {
        return $this->get();
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
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Collection
     */
    public function get($columns = ['*']) {
        $columns = $this->query->getQuery()->columns ? [] : $columns;
        $select = $this->getSelectColumns($columns);
        $models = $this->query->addSelect($select)->getModels();
        if(count($models) > 0) {
            $models = $this->query->eagerLoadRelations($models);
        }
        return $this->related->newCollection($models);
    }
    /**
     * @param array $columns
     * @return array
     */
    protected function getSelectColumns(array $columns = ['*']) {
        if($columns == ['*']) {
            $columns = [$this->related->getTable() . '.*'];
        }
        return array_merge($columns, [$this->parent->getTable() . '.' . $this->firstKey]);
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page') {
        $this->query->addSelect($this->getSelectColumns($columns));
        return $this->query->paginate($perPage, $columns, $pageName);
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*']) {
        $this->query->addSelect($this->getSelectColumns($columns));
        return $this->query->simplePaginate($perPage, $columns);
    }
    /**
     * @return string
     */
    public function getHasCompareKey() {
        return $this->farParent->getQualifiedKeyName();
    }
    /**
     * @return string
     */
    public function getForeignKey() {
        return $this->related->getTable() . '.' . $this->secondKey;
    }
    /**
     * @return string
     */
    public function getThroughKey() {
        return $this->parent->getTable() . '.' . $this->firstKey;
    }
}