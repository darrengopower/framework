<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:17
 */
namespace Notadd\Foundation\Database\Eloquent;
use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Notadd\Foundation\Database\Eloquent\Relations\Relation;
use Notadd\Foundation\Database\Query\Builder as QueryBuilder;
use Notadd\Foundation\Database\Query\Expression;
/**
 * Class Builder
 * @package Notadd\Foundation\Database\Eloquent
 */
class Builder {
    /**
     * @var \Notadd\Foundation\Database\Query\Builder
     */
    protected $query;
    /**
     * @var \Notadd\Foundation\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var array
     */
    protected $eagerLoad = [];
    /**
     * @var array
     */
    protected $macros = [];
    /**
     * @var \Closure
     */
    protected $onDelete;
    /**
     * @var array
     */
    protected $passthru = [
        'insert',
        'insertGetId',
        'getBindings',
        'toSql',
        'exists',
        'count',
        'min',
        'max',
        'avg',
        'sum',
    ];
    /**
     * Builder constructor.
     * @param \Notadd\Foundation\Database\Query\Builder $query
     */
    public function __construct(QueryBuilder $query) {
        $this->query = $query;
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
        $this->query->where($this->model->getQualifiedKeyName(), '=', $id);
        return $this->first($columns);
    }
    /**
     * @param array $ids
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Collection
     */
    public function findMany($ids, $columns = ['*']) {
        if(empty($ids)) {
            return $this->model->newCollection();
        }
        $this->query->whereIn($this->model->getQualifiedKeyName(), $ids);
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
        throw (new ModelNotFoundException)->setModel(get_class($this->model));
    }
    /**
     * Execute the query and get the first result.
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Model|static|null
     */
    public function first($columns = ['*']) {
        return $this->take(1)->get($columns)->first();
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
        throw (new ModelNotFoundException)->setModel(get_class($this->model));
    }
    /**
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Collection|static[]
     */
    public function get($columns = ['*']) {
        $models = $this->getModels($columns);
        if(count($models) > 0) {
            $models = $this->eagerLoadRelations($models);
        }
        return $this->model->newCollection($models);
    }
    /**
     * @param string $column
     * @return mixed
     */
    public function value($column) {
        $result = $this->first([$column]);
        if($result) {
            return $result->{$column};
        }
    }
    /**
     * @param string $column
     * @return mixed
     * @deprecated since version 5.1.
     */
    public function pluck($column) {
        return $this->value($column);
    }
    /**
     * @param int $count
     * @param callable $callback
     * @return void
     */
    public function chunk($count, callable $callback) {
        $results = $this->forPage($page = 1, $count)->get();
        while(count($results) > 0) {
            if(call_user_func($callback, $results) === false) {
                break;
            }
            $page++;
            $results = $this->forPage($page, $count)->get();
        }
    }
    /**
     * @param string $column
     * @param string|null $key
     * @return \Illuminate\Support\Collection
     */
    public function lists($column, $key = null) {
        $results = $this->query->lists($column, $key);
        if($this->model->hasGetMutator($column)) {
            foreach($results as $key => &$value) {
                $fill = [$column => $value];
                $value = $this->model->newFromBuilder($fill)->$column;
            }
        }
        return collect($results);
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \InvalidArgumentException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
        $total = $this->query->getCountForPagination();
        $this->query->forPage($page = $page ?: Paginator::resolveCurrentPage($pageName), $perPage = $perPage ?: $this->model->getPerPage());
        return new LengthAwarePaginator($this->get($columns), $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page') {
        $page = Paginator::resolveCurrentPage($pageName);
        $perPage = $perPage ?: $this->model->getPerPage();
        $this->skip(($page - 1) * $perPage)->take($perPage + 1);
        return new Paginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
    /**
     * @param array $values
     * @return int
     */
    public function update(array $values) {
        return $this->query->update($this->addUpdatedAtColumn($values));
    }
    /**
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = []) {
        $extra = $this->addUpdatedAtColumn($extra);
        return $this->query->increment($column, $amount, $extra);
    }
    /**
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    public function decrement($column, $amount = 1, array $extra = []) {
        $extra = $this->addUpdatedAtColumn($extra);
        return $this->query->decrement($column, $amount, $extra);
    }
    /**
     * @param array $values
     * @return array
     */
    protected function addUpdatedAtColumn(array $values) {
        if(!$this->model->usesTimestamps()) {
            return $values;
        }
        $column = $this->model->getUpdatedAtColumn();
        return Arr::add($values, $column, $this->model->freshTimestampString());
    }
    /**
     * @return mixed
     */
    public function delete() {
        if(isset($this->onDelete)) {
            return call_user_func($this->onDelete, $this);
        }
        return $this->query->delete();
    }
    /**
     * @return mixed
     */
    public function forceDelete() {
        return $this->query->delete();
    }
    /**
     * @param \Closure $callback
     * @return void
     */
    public function onDelete(Closure $callback) {
        $this->onDelete = $callback;
    }
    /**
     * @param array $columns
     * @return \Notadd\Foundation\Database\Eloquent\Model[]
     */
    public function getModels($columns = ['*']) {
        $results = $this->query->get($columns);
        $connection = $this->model->getConnectionName();
        return $this->model->hydrate($results, $connection)->all();
    }
    /**
     * @param array $models
     * @return array
     */
    public function eagerLoadRelations(array $models) {
        foreach($this->eagerLoad as $name => $constraints) {
            if(strpos($name, '.') === false) {
                $models = $this->loadRelation($models, $name, $constraints);
            }
        }
        return $models;
    }
    /**
     * @param array $models
     * @param string $name
     * @param \Closure $constraints
     * @return array
     */
    protected function loadRelation(array $models, $name, Closure $constraints) {
        $relation = $this->getRelation($name);
        $relation->addEagerConstraints($models);
        call_user_func($constraints, $relation);
        $models = $relation->initRelation($models, $name);
        $results = $relation->getEager();
        return $relation->match($models, $results, $name);
    }
    /**
     * @param string $relation
     * @return \Notadd\Foundation\Database\Eloquent\Relations\Relation
     */
    public function getRelation($relation) {
        $query = Relation::noConstraints(function () use ($relation) {
            return $this->getModel()->$relation();
        });
        $nested = $this->nestedRelations($relation);
        if(count($nested) > 0) {
            $query->getQuery()->with($nested);
        }
        return $query;
    }
    /**
     * @param string $relation
     * @return array
     */
    protected function nestedRelations($relation) {
        $nested = [];
        foreach($this->eagerLoad as $name => $constraints) {
            if($this->isNested($name, $relation)) {
                $nested[substr($name, strlen($relation . '.'))] = $constraints;
            }
        }
        return $nested;
    }
    /**
     * @param string $name
     * @param string $relation
     * @return bool
     */
    protected function isNested($name, $relation) {
        $dots = Str::contains($name, '.');
        return $dots && Str::startsWith($name, $relation . '.');
    }
    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and') {
        if($column instanceof Closure) {
            $query = $this->model->newQueryWithoutScopes();
            call_user_func($column, $query);
            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            call_user_func_array([
                $this->query,
                'where'
            ], func_get_args());
        }
        return $this;
    }
    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function orWhere($column, $operator = null, $value = null) {
        return $this->where($column, $operator, $value, 'or');
    }
    /**
     * @param string $relation
     * @param string $operator
     * @param int $count
     * @param string $boolean
     * @param \Closure|null $callback
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null) {
        if(strpos($relation, '.') !== false) {
            return $this->hasNested($relation, $operator, $count, $boolean, $callback);
        }
        $relation = $this->getHasRelationQuery($relation);
        $query = $relation->getRelationCountQuery($relation->getRelated()->newQuery(), $this);
        if($callback) {
            call_user_func($callback, $query);
        }
        return $this->addHasWhere($query, $relation, $operator, $count, $boolean);
    }
    /**
     * @param string $relations
     * @param string $operator
     * @param int $count
     * @param string $boolean
     * @param \Closure|null $callback
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    protected function hasNested($relations, $operator = '>=', $count = 1, $boolean = 'and', $callback = null) {
        $relations = explode('.', $relations);
        $closure = function ($q) use (&$closure, &$relations, $operator, $count, $boolean, $callback) {
            if(count($relations) > 1) {
                $q->whereHas(array_shift($relations), $closure);
            } else {
                $q->has(array_shift($relations), $operator, $count, 'and', $callback);
            }
        };
        return $this->has(array_shift($relations), '>=', 1, $boolean, $closure);
    }
    /**
     * @param string $relation
     * @param string $boolean
     * @param \Closure|null $callback
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function doesntHave($relation, $boolean = 'and', Closure $callback = null) {
        return $this->has($relation, '<', 1, $boolean, $callback);
    }
    /**
     * @param string $relation
     * @param \Closure $callback
     * @param string $operator
     * @param int $count
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function whereHas($relation, Closure $callback, $operator = '>=', $count = 1) {
        return $this->has($relation, $operator, $count, 'and', $callback);
    }
    /**
     * @param string $relation
     * @param \Closure|null $callback
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function whereDoesntHave($relation, Closure $callback = null) {
        return $this->doesntHave($relation, 'and', $callback);
    }
    /**
     * @param string $relation
     * @param string $operator
     * @param int $count
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function orHas($relation, $operator = '>=', $count = 1) {
        return $this->has($relation, $operator, $count, 'or');
    }
    /**
     * @param string $relation
     * @param \Closure $callback
     * @param string $operator
     * @param int $count
     * @return \Notadd\Foundation\Database\Eloquent\Builder|static
     */
    public function orWhereHas($relation, Closure $callback, $operator = '>=', $count = 1) {
        return $this->has($relation, $operator, $count, 'or', $callback);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $hasQuery
     * @param \Notadd\Foundation\Database\Eloquent\Relations\Relation $relation
     * @param string $operator
     * @param int $count
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Eloquent\Builder
     */
    protected function addHasWhere(Builder $hasQuery, Relation $relation, $operator, $count, $boolean) {
        $this->mergeWheresToHas($hasQuery, $relation);
        if(is_numeric($count)) {
            $count = new Expression($count);
        }
        return $this->where(new Expression('(' . $hasQuery->toSql() . ')'), $operator, $count, $boolean);
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $hasQuery
     * @param \Notadd\Foundation\Database\Eloquent\Relations\Relation $relation
     * @return void
     */
    protected function mergeWheresToHas(Builder $hasQuery, Relation $relation) {
        $relationQuery = $relation->getBaseQuery();
        $hasQuery = $hasQuery->getModel()->removeGlobalScopes($hasQuery);
        $hasQuery->mergeWheres($relationQuery->wheres, $relationQuery->getBindings());
        $this->query->addBinding($hasQuery->getQuery()->getBindings(), 'where');
    }
    /**
     * @param string $relation
     * @return \Notadd\Foundation\Database\Eloquent\Relations\Relation
     */
    protected function getHasRelationQuery($relation) {
        return Relation::noConstraints(function () use ($relation) {
            return $this->getModel()->$relation();
        });
    }
    /**
     * @param mixed $relations
     * @return $this
     */
    public function with($relations) {
        if(is_string($relations)) {
            $relations = func_get_args();
        }
        $eagers = $this->parseRelations($relations);
        $this->eagerLoad = array_merge($this->eagerLoad, $eagers);
        return $this;
    }
    /**
     * @param array $relations
     * @return array
     */
    protected function parseRelations(array $relations) {
        $results = [];
        foreach($relations as $name => $constraints) {
            if(is_numeric($name)) {
                $f = function () {
                };
                list($name, $constraints) = [
                    $constraints,
                    $f
                ];
            }
            $results = $this->parseNested($name, $results);
            $results[$name] = $constraints;
        }
        return $results;
    }
    /**
     * @param string $name
     * @param array $results
     * @return array
     */
    protected function parseNested($name, $results) {
        $progress = [];
        foreach(explode('.', $name) as $segment) {
            $progress[] = $segment;
            if(!isset($results[$last = implode('.', $progress)])) {
                $results[$last] = function () {
                };
            }
        }
        return $results;
    }
    /**
     * @param string $scope
     * @param array $parameters
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    protected function callScope($scope, $parameters) {
        array_unshift($parameters, $this);
        return call_user_func_array([
            $this->model,
            $scope
        ], $parameters) ?: $this;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function getQuery() {
        return $this->query;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return $this
     */
    public function setQuery($query) {
        $this->query = $query;
        return $this;
    }
    /**
     * @return array
     */
    public function getEagerLoads() {
        return $this->eagerLoad;
    }
    /**
     * @param array $eagerLoad
     * @return $this
     */
    public function setEagerLoads(array $eagerLoad) {
        $this->eagerLoad = $eagerLoad;
        return $this;
    }
    /**
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    public function getModel() {
        return $this->model;
    }
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return $this
     */
    public function setModel(Model $model) {
        $this->model = $model;
        $this->query->from($model->getTable());
        return $this;
    }
    /**
     * @param string $name
     * @param \Closure $callback
     * @return void
     */
    public function macro($name, Closure $callback) {
        $this->macros[$name] = $callback;
    }
    /**
     * @param string $name
     * @return \Closure
     */
    public function getMacro($name) {
        return Arr::get($this->macros, $name);
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        if(isset($this->macros[$method])) {
            array_unshift($parameters, $this);
            return call_user_func_array($this->macros[$method], $parameters);
        } elseif(method_exists($this->model, $scope = 'scope' . ucfirst($method))) {
            return $this->callScope($scope, $parameters);
        }
        $result = call_user_func_array([
            $this->query,
            $method
        ], $parameters);
        return in_array($method, $this->passthru) ? $result : $this;
    }
    /**
     * @return void
     */
    public function __clone() {
        $this->query = clone $this->query;
    }
}