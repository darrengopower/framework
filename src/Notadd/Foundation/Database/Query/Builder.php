<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:30
 */
namespace Notadd\Foundation\Database\Query;
use BadMethodCallException;
use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Notadd\Foundation\Database\ConnectionInterface;
use Notadd\Foundation\Database\Query\Grammars\Grammar;
use Notadd\Foundation\Database\Query\Processors\Processor;
class Builder {
    use Macroable {
        __call as macroCall;
    }
    /**
     * @var \Notadd\Foundation\Database\Connection
     */
    protected $connection;
    /**
     * @var \Notadd\Foundation\Database\Query\Grammars\Grammar
     */
    protected $grammar;
    /**
     * @var \Notadd\Foundation\Database\Query\Processors\Processor
     */
    protected $processor;
    /**
     * @var array
     */
    protected $bindings = [
        'select' => [],
        'join' => [],
        'where' => [],
        'having' => [],
        'order' => [],
        'union' => [],
    ];
    /**
     * @var array
     */
    public $aggregate;
    /**
     * @var array
     */
    public $columns;
    /**
     * @var bool
     */
    public $distinct = false;
    /**
     * @var string
     */
    public $from;
    /**
     * @var array
     */
    public $joins;
    /**
     * @var array
     */
    public $wheres;
    /**
     * @var array
     */
    public $groups;
    /**
     * @var array
     */
    public $havings;
    /**
     * @var array
     */
    public $orders;
    /**
     * @var int
     */
    public $limit;
    /**
     * @var int
     */
    public $offset;
    /**
     * @var array
     */
    public $unions;
    /**
     * @var int
     */
    public $unionLimit;
    /**
     * @var int
     */
    public $unionOffset;
    /**
     * @var array
     */
    public $unionOrders;
    /**
     * @var string|bool
     */
    public $lock;
    /**
     * @var array
     */
    protected $backups = [];
    /**
     * @var array
     */
    protected $bindingBackups = [];
    /**
     * @var array
     */
    protected $operators = [
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '<>',
        '!=',
        'like',
        'like binary',
        'not like',
        'between',
        'ilike',
        '&',
        '|',
        '^',
        '<<',
        '>>',
        'rlike',
        'regexp',
        'not regexp',
        '~',
        '~*',
        '!~',
        '!~*',
        'similar to',
        'not similar to',
    ];
    /**
     * @var bool
     */
    protected $useWritePdo = false;
    /**
     * @param \Notadd\Foundation\Database\ConnectionInterface $connection
     * @param \Notadd\Foundation\Database\Query\Grammars\Grammar $grammar
     * @param \Notadd\Foundation\Database\Query\Processors\Processor $processor
     */
    public function __construct(ConnectionInterface $connection, Grammar $grammar, Processor $processor) {
        $this->grammar = $grammar;
        $this->processor = $processor;
        $this->connection = $connection;
    }
    /**
     * @param array|mixed $columns
     * @return $this
     */
    public function select($columns = ['*']) {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
    /**
     * @param string $expression
     * @param array $bindings
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function selectRaw($expression, array $bindings = []) {
        $this->addSelect(new Expression($expression));
        if($bindings) {
            $this->addBinding($bindings, 'select');
        }
        return $this;
    }
    /**
     * @param \Closure|\Notadd\Foundation\Database\Query\Builder|string $query
     * @param string $as
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function selectSub($query, $as) {
        if($query instanceof Closure) {
            $callback = $query;
            $callback($query = $this->newQuery());
        }
        if($query instanceof self) {
            $bindings = $query->getBindings();
            $query = $query->toSql();
        } elseif(is_string($query)) {
            $bindings = [];
        } else {
            throw new InvalidArgumentException;
        }
        return $this->selectRaw('(' . $query . ') as ' . $this->grammar->wrap($as), $bindings);
    }
    /**
     * @param array|mixed $column
     * @return $this
     */
    public function addSelect($column) {
        $column = is_array($column) ? $column : func_get_args();
        $this->columns = array_merge((array)$this->columns, $column);
        return $this;
    }
    /**
     * @return $this
     */
    public function distinct() {
        $this->distinct = true;
        return $this;
    }
    /**
     * @param string $table
     * @return $this
     */
    public function from($table) {
        $this->from = $table;
        return $this;
    }
    /**
     * @param string $table
     * @param string $one
     * @param string $operator
     * @param string $two
     * @param string $type
     * @param bool $where
     * @return $this
     */
    public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false) {
        if($one instanceof Closure) {
            $join = new JoinClause($type, $table);
            call_user_func($one, $join);
            $this->joins[] = $join;
            $this->addBinding($join->bindings, 'join');
        }
        else {
            $join = new JoinClause($type, $table);
            $this->joins[] = $join->on($one, $operator, $two, 'and', $where);
            $this->addBinding($join->bindings, 'join');
        }
        return $this;
    }
    /**
     * @param string $table
     * @param string $one
     * @param string $operator
     * @param string $two
     * @param string $type
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function joinWhere($table, $one, $operator, $two, $type = 'inner') {
        return $this->join($table, $one, $operator, $two, $type, true);
    }
    /**
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function leftJoin($table, $first, $operator = null, $second = null) {
        return $this->join($table, $first, $operator, $second, 'left');
    }
    /**
     * @param string $table
     * @param string $one
     * @param string $operator
     * @param string $two
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function leftJoinWhere($table, $one, $operator, $two) {
        return $this->joinWhere($table, $one, $operator, $two, 'left');
    }
    /**
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function rightJoin($table, $first, $operator = null, $second = null) {
        return $this->join($table, $first, $operator, $second, 'right');
    }
    /**
     * @param string $table
     * @param string $one
     * @param string $operator
     * @param string $two
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function rightJoinWhere($table, $one, $operator, $two) {
        return $this->joinWhere($table, $one, $operator, $two, 'right');
    }
    /**
     * @param string|array|\Closure $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and') {
        if(is_array($column)) {
            return $this->whereNested(function ($query) use ($column) {
                foreach($column as $key => $value) {
                    $query->where($key, '=', $value);
                }
            }, $boolean);
        }
        if(func_num_args() == 2) {
            list($value, $operator) = [
                $operator,
                '='
            ];
        } elseif($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException('Illegal operator and value combination.');
        }
        if($column instanceof Closure) {
            return $this->whereNested($column, $boolean);
        }
        if(!in_array(strtolower($operator), $this->operators, true)) {
            list($value, $operator) = [
                $operator,
                '='
            ];
        }
        if($value instanceof Closure) {
            return $this->whereSub($column, $operator, $value, $boolean);
        }
        if(is_null($value)) {
            return $this->whereNull($column, $boolean, $operator != '=');
        }
        $type = 'Basic';
        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');
        if(!$value instanceof Expression) {
            $this->addBinding($value, 'where');
        }
        return $this;
    }
    /**
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhere($column, $operator = null, $value = null) {
        return $this->where($column, $operator, $value, 'or');
    }
    /**
     * @param string $operator
     * @param mixed $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value) {
        $isOperator = in_array($operator, $this->operators);
        return $isOperator && $operator != '=' && is_null($value);
    }
    /**
     * @param string $sql
     * @param array $bindings
     * @param string $boolean
     * @return $this
     */
    public function whereRaw($sql, array $bindings = [], $boolean = 'and') {
        $type = 'raw';
        $this->wheres[] = compact('type', 'sql', 'boolean');
        $this->addBinding($bindings, 'where');
        return $this;
    }
    /**
     * @param string $sql
     * @param array $bindings
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereRaw($sql, array $bindings = []) {
        return $this->whereRaw($sql, $bindings, 'or');
    }
    /**
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false) {
        $type = 'between';
        $this->wheres[] = compact('column', 'type', 'boolean', 'not');
        $this->addBinding($values, 'where');
        return $this;
    }
    /**
     * @param string $column
     * @param array $values
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereBetween($column, array $values) {
        return $this->whereBetween($column, $values, 'or');
    }
    /**
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereNotBetween($column, array $values, $boolean = 'and') {
        return $this->whereBetween($column, $values, $boolean, true);
    }
    /**
     * @param string $column
     * @param array $values
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereNotBetween($column, array $values) {
        return $this->whereNotBetween($column, $values, 'or');
    }
    /**
     * @param \Closure $callback
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereNested(Closure $callback, $boolean = 'and') {
        $query = $this->newQuery();
        $query->from($this->from);
        call_user_func($callback, $query);
        return $this->addNestedWhereQuery($query, $boolean);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder|static $query
     * @param string $boolean
     * @return $this
     */
    public function addNestedWhereQuery($query, $boolean = 'and') {
        if(count($query->wheres)) {
            $type = 'Nested';
            $this->wheres[] = compact('type', 'query', 'boolean');
            $this->addBinding($query->getBindings(), 'where');
        }
        return $this;
    }
    /**
     * @param string $column
     * @param string $operator
     * @param \Closure $callback
     * @param string $boolean
     * @return $this
     */
    protected function whereSub($column, $operator, Closure $callback, $boolean) {
        $type = 'Sub';
        $query = $this->newQuery();
        call_user_func($callback, $query);
        $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');
        $this->addBinding($query->getBindings(), 'where');
        return $this;
    }
    /**
     * @param \Closure $callback
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereExists(Closure $callback, $boolean = 'and', $not = false) {
        $type = $not ? 'NotExists' : 'Exists';
        $query = $this->newQuery();
        call_user_func($callback, $query);
        $this->wheres[] = compact('type', 'operator', 'query', 'boolean');
        $this->addBinding($query->getBindings(), 'where');
        return $this;
    }
    /**
     * @param \Closure $callback
     * @param bool $not
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereExists(Closure $callback, $not = false) {
        return $this->whereExists($callback, 'or', $not);
    }
    /**
     * @param \Closure $callback
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereNotExists(Closure $callback, $boolean = 'and') {
        return $this->whereExists($callback, $boolean, true);
    }
    /**
     * @param \Closure $callback
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereNotExists(Closure $callback) {
        return $this->orWhereExists($callback, true);
    }
    /**
     * @param string $column
     * @param mixed $values
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false) {
        $type = $not ? 'NotIn' : 'In';
        if($values instanceof Closure) {
            return $this->whereInSub($column, $values, $boolean, $not);
        }
        if($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        $this->wheres[] = compact('type', 'column', 'values', 'boolean');
        $this->addBinding($values, 'where');
        return $this;
    }
    /**
     * @param string $column
     * @param mixed $values
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereIn($column, $values) {
        return $this->whereIn($column, $values, 'or');
    }
    /**
     * @param string $column
     * @param mixed $values
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereNotIn($column, $values, $boolean = 'and') {
        return $this->whereIn($column, $values, $boolean, true);
    }
    /**
     * @param string $column
     * @param mixed $values
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereNotIn($column, $values) {
        return $this->whereNotIn($column, $values, 'or');
    }
    /**
     * @param string $column
     * @param \Closure $callback
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    protected function whereInSub($column, Closure $callback, $boolean, $not) {
        $type = $not ? 'NotInSub' : 'InSub';
        call_user_func($callback, $query = $this->newQuery());
        $this->wheres[] = compact('type', 'column', 'query', 'boolean');
        $this->addBinding($query->getBindings(), 'where');
        return $this;
    }
    /**
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereNull($column, $boolean = 'and', $not = false) {
        $type = $not ? 'NotNull' : 'Null';
        $this->wheres[] = compact('type', 'column', 'boolean');
        return $this;
    }
    /**
     * @param string $column
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereNull($column) {
        return $this->whereNull($column, 'or');
    }
    /**
     * @param string $column
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereNotNull($column, $boolean = 'and') {
        return $this->whereNull($column, $boolean, true);
    }
    /**
     * @param string $column
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orWhereNotNull($column) {
        return $this->whereNotNull($column, 'or');
    }
    /**
     * @param string $column
     * @param string $operator
     * @param int $value
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereDate($column, $operator, $value, $boolean = 'and') {
        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
    }
    /**
     * @param string $column
     * @param string $operator
     * @param int $value
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereDay($column, $operator, $value, $boolean = 'and') {
        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }
    /**
     * @param string $column
     * @param string $operator
     * @param int $value
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereMonth($column, $operator, $value, $boolean = 'and') {
        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }
    /**
     * @param string $column
     * @param string $operator
     * @param int $value
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function whereYear($column, $operator, $value, $boolean = 'and') {
        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }
    /**
     * @param string $type
     * @param string $column
     * @param string $operator
     * @param int $value
     * @param string $boolean
     * @return $this
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and') {
        $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');
        $this->addBinding($value, 'where');
        return $this;
    }
    /**
     * @param string $method
     * @param string $parameters
     * @return $this
     */
    public function dynamicWhere($method, $parameters) {
        $finder = substr($method, 5);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);
        $connector = 'and';
        $index = 0;
        foreach($segments as $segment) {
            if($segment != 'And' && $segment != 'Or') {
                $this->addDynamic($segment, $connector, $parameters, $index);
                $index++;
            }
            else {
                $connector = $segment;
            }
        }
        return $this;
    }
    /**
     * @param string $segment
     * @param string $connector
     * @param array $parameters
     * @param int $index
     * @return void
     */
    protected function addDynamic($segment, $connector, $parameters, $index) {
        $bool = strtolower($connector);
        $this->where(Str::snake($segment), '=', $parameters[$index], $bool);
    }
    /**
     * @return $this
     * @internal param array|string $column
     */
    public function groupBy() {
        foreach(func_get_args() as $arg) {
            $this->groups = array_merge((array)$this->groups, is_array($arg) ? $arg : [$arg]);
        }
        return $this;
    }
    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param string $boolean
     * @return $this
     */
    public function having($column, $operator = null, $value = null, $boolean = 'and') {
        $type = 'basic';
        $this->havings[] = compact('type', 'column', 'operator', 'value', 'boolean');
        if(!$value instanceof Expression) {
            $this->addBinding($value, 'having');
        }
        return $this;
    }
    /**
     * @param string $column
     * @param string $operator
     * @param string $value
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orHaving($column, $operator = null, $value = null) {
        return $this->having($column, $operator, $value, 'or');
    }
    /**
     * @param string $sql
     * @param array $bindings
     * @param string $boolean
     * @return $this
     */
    public function havingRaw($sql, array $bindings = [], $boolean = 'and') {
        $type = 'raw';
        $this->havings[] = compact('type', 'sql', 'boolean');
        $this->addBinding($bindings, 'having');
        return $this;
    }
    /**
     * @param string $sql
     * @param array $bindings
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function orHavingRaw($sql, array $bindings = []) {
        return $this->havingRaw($sql, $bindings, 'or');
    }
    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc') {
        $property = $this->unions ? 'unionOrders' : 'orders';
        $direction = strtolower($direction) == 'asc' ? 'asc' : 'desc';
        $this->{$property}[] = compact('column', 'direction');
        return $this;
    }
    /**
     * @param string $column
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function latest($column = 'created_at') {
        return $this->orderBy($column, 'desc');
    }
    /**
     * @param string $column
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function oldest($column = 'created_at') {
        return $this->orderBy($column, 'asc');
    }
    /**
     * @param string $sql
     * @param array $bindings
     * @return $this
     */
    public function orderByRaw($sql, $bindings = []) {
        $property = $this->unions ? 'unionOrders' : 'orders';
        $type = 'raw';
        $this->{$property}[] = compact('type', 'sql');
        $this->addBinding($bindings, 'order');
        return $this;
    }
    /**
     * @param int $value
     * @return $this
     */
    public function offset($value) {
        $property = $this->unions ? 'unionOffset' : 'offset';
        $this->$property = max(0, $value);
        return $this;
    }
    /**
     * @param int $value
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function skip($value) {
        return $this->offset($value);
    }
    /**
     * @param int $value
     * @return $this
     */
    public function limit($value) {
        $property = $this->unions ? 'unionLimit' : 'limit';
        if($value >= 0) {
            $this->$property = $value;
        }
        return $this;
    }
    /**
     * @param int $value
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function take($value) {
        return $this->limit($value);
    }
    /**
     * @param int $page
     * @param int $perPage
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function forPage($page, $perPage = 15) {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder|\Closure $query
     * @param bool $all
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function union($query, $all = false) {
        if($query instanceof Closure) {
            call_user_func($query, $query = $this->newQuery());
        }
        $this->unions[] = compact('query', 'all');
        $this->addBinding($query->getBindings(), 'union');
        return $this;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder|\Closure $query
     * @return \Notadd\Foundation\Database\Query\Builder|static
     */
    public function unionAll($query) {
        return $this->union($query, true);
    }
    /**
     * @param bool $value
     * @return $this
     */
    public function lock($value = true) {
        $this->lock = $value;
        if($this->lock) {
            $this->useWritePdo();
        }
        return $this;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function lockForUpdate() {
        return $this->lock(true);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function sharedLock() {
        return $this->lock(false);
    }
    /**
     * @return string
     */
    public function toSql() {
        return $this->grammar->compileSelect($this);
    }
    /**
     * @param int $id
     * @param array $columns
     * @return mixed|static
     */
    public function find($id, $columns = ['*']) {
        return $this->where('id', '=', $id)->first($columns);
    }
    /**
     * @param string $column
     * @return mixed
     */
    public function value($column) {
        $result = (array)$this->first([$column]);
        return count($result) > 0 ? reset($result) : null;
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
     * @param array $columns
     * @return mixed|static
     */
    public function first($columns = ['*']) {
        $results = $this->take(1)->get($columns);
        return count($results) > 0 ? reset($results) : null;
    }
    /**
     * @param array $columns
     * @return array|static[]
     */
    public function get($columns = ['*']) {
        if(is_null($this->columns)) {
            $this->columns = $columns;
        }
        return $this->processor->processSelect($this, $this->runSelect());
    }
    /**
     * @param array $columns
     * @return array|static[]
     * @deprecated since version 5.1. Use get instead.
     */
    public function getFresh($columns = ['*']) {
        return $this->get($columns);
    }
    /**
     * @return array
     */
    protected function runSelect() {
        return $this->connection->select($this->toSql(), $this->getBindings(), !$this->useWritePdo);
    }
    /**
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null) {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $total = $this->getCountForPagination($columns);
        $results = $this->forPage($page, $perPage)->get($columns);
        return new LengthAwarePaginator($results, $total, $perPage, $page, [
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
    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page') {
        $page = Paginator::resolveCurrentPage($pageName);
        $this->skip(($page - 1) * $perPage)->take($perPage + 1);
        return new Paginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
    /**
     * @param array $columns
     * @return int
     */
    public function getCountForPagination($columns = ['*']) {
        $this->backupFieldsForCount();
        $this->aggregate = [
            'function' => 'count',
            'columns' => $this->clearSelectAliases($columns)
        ];
        $results = $this->get();
        $this->aggregate = null;
        $this->restoreFieldsForCount();
        if(isset($this->groups)) {
            return count($results);
        }
        return isset($results[0]) ? (int)array_change_key_case((array)$results[0])['aggregate'] : 0;
    }
    /**
     * @return void
     */
    protected function backupFieldsForCount() {
        foreach([
                    'orders',
                    'limit',
                    'offset',
                    'columns'
                ] as $field) {
            $this->backups[$field] = $this->{$field};
            $this->{$field} = null;
        }
        foreach([
                    'order',
                    'select'
                ] as $key) {
            $this->bindingBackups[$key] = $this->bindings[$key];
            $this->bindings[$key] = [];
        }
    }
    /**
     * @param array $columns
     * @return array
     */
    protected function clearSelectAliases(array $columns) {
        return array_map(function ($column) {
            return is_string($column) && ($aliasPosition = strpos(strtolower($column), ' as ')) !== false ? substr($column, 0, $aliasPosition) : $column;
        }, $columns);
    }
    /**
     * @return void
     */
    protected function restoreFieldsForCount() {
        foreach([
                    'orders',
                    'limit',
                    'offset',
                    'columns'
                ] as $field) {
            $this->{$field} = $this->backups[$field];
        }
        foreach([
                    'order',
                    'select'
                ] as $key) {
            $this->bindings[$key] = $this->bindingBackups[$key];
        }
        $this->backups = [];
        $this->bindingBackups = [];
    }
    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     */
    public function chunk($count, callable $callback) {
        $results = $this->forPage($page = 1, $count)->get();
        while(count($results) > 0) {
            if(call_user_func($callback, $results) === false) {
                return false;
            }
            $page++;
            $results = $this->forPage($page, $count)->get();
        }
        return true;
    }
    /**
     * @param string $column
     * @param string|null $key
     * @return array
     */
    public function lists($column, $key = null) {
        $columns = $this->getListSelect($column, $key);
        $results = new Collection($this->get($columns));
        return $results->pluck($columns[0], Arr::get($columns, 1))->all();
    }
    /**
     * @param string $column
     * @param string $key
     * @return array
     */
    protected function getListSelect($column, $key) {
        $select = is_null($key) ? [$column] : [
            $column,
            $key
        ];
        return array_map(function ($column) {
            $dot = strpos($column, '.');
            return $dot === false ? $column : substr($column, $dot + 1);
        }, $select);
    }
    /**
     * @param string $column
     * @param string $glue
     * @return string
     */
    public function implode($column, $glue = '') {
        return implode($glue, $this->lists($column));
    }
    /**
     * @return bool|null
     */
    public function exists() {
        $sql = $this->grammar->compileExists($this);
        $results = $this->connection->select($sql, $this->getBindings(), !$this->useWritePdo);
        if(isset($results[0])) {
            $results = (array)$results[0];
            return (bool)$results['exists'];
        }
    }
    /**
     * @param string $columns
     * @return int
     */
    public function count($columns = '*') {
        if(!is_array($columns)) {
            $columns = [$columns];
        }
        return (int)$this->aggregate(__FUNCTION__, $columns);
    }
    /**
     * @param string $column
     * @return float|int
     */
    public function min($column) {
        return $this->aggregate(__FUNCTION__, [$column]);
    }
    /**
     * @param string $column
     * @return float|int
     */
    public function max($column) {
        return $this->aggregate(__FUNCTION__, [$column]);
    }
    /**
     * @param string $column
     * @return float|int
     */
    public function sum($column) {
        $result = $this->aggregate(__FUNCTION__, [$column]);
        return $result ?: 0;
    }
    /**
     * @param string $column
     * @return float|int
     */
    public function avg($column) {
        return $this->aggregate(__FUNCTION__, [$column]);
    }
    /**
     * @param string $column
     * @return float|int
     */
    public function average($column) {
        return $this->avg($column);
    }
    /**
     * @param string $function
     * @param array $columns
     * @return float|int
     */
    public function aggregate($function, $columns = ['*']) {
        $this->aggregate = compact('function', 'columns');
        $previousColumns = $this->columns;
        $previousSelectBindings = $this->bindings['select'];
        $this->bindings['select'] = [];
        $results = $this->get($columns);
        $this->aggregate = null;
        $this->columns = $previousColumns;
        $this->bindings['select'] = $previousSelectBindings;
        if(isset($results[0])) {
            $result = array_change_key_case((array)$results[0]);
            return $result['aggregate'];
        }
    }
    /**
     * @param array $values
     * @return bool
     */
    public function insert(array $values) {
        if(empty($values)) {
            return true;
        }
        if(!is_array(reset($values))) {
            $values = [$values];
        }
        else {
            foreach($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }
        $bindings = [];
        foreach($values as $record) {
            foreach($record as $value) {
                $bindings[] = $value;
            }
        }
        $sql = $this->grammar->compileInsert($this, $values);
        $bindings = $this->cleanBindings($bindings);
        return $this->connection->insert($sql, $bindings);
    }
    /**
     * @param array $values
     * @param string $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null) {
        $sql = $this->grammar->compileInsertGetId($this, $values, $sequence);
        $values = $this->cleanBindings($values);
        return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
    }
    /**
     * @param array $values
     * @return int
     */
    public function update(array $values) {
        $bindings = array_values(array_merge($values, $this->getBindings()));
        $sql = $this->grammar->compileUpdate($this, $values);
        return $this->connection->update($sql, $this->cleanBindings($bindings));
    }
    /**
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = []) {
        $wrapped = $this->grammar->wrap($column);
        $columns = array_merge([$column => $this->raw("$wrapped + $amount")], $extra);
        return $this->update($columns);
    }
    /**
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    public function decrement($column, $amount = 1, array $extra = []) {
        $wrapped = $this->grammar->wrap($column);
        $columns = array_merge([$column => $this->raw("$wrapped - $amount")], $extra);
        return $this->update($columns);
    }
    /**
     * @param mixed $id
     * @return int
     */
    public function delete($id = null) {
        if(!is_null($id)) {
            $this->where('id', '=', $id);
        }
        $sql = $this->grammar->compileDelete($this);
        return $this->connection->delete($sql, $this->getBindings());
    }
    /**
     * @return void
     */
    public function truncate() {
        foreach($this->grammar->compileTruncate($this) as $sql => $bindings) {
            $this->connection->statement($sql, $bindings);
        }
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    public function newQuery() {
        return new static($this->connection, $this->grammar, $this->processor);
    }
    /**
     * @param array $wheres
     * @param array $bindings
     * @return void
     */
    public function mergeWheres($wheres, $bindings) {
        $this->wheres = array_merge((array)$this->wheres, (array)$wheres);
        $this->bindings['where'] = array_values(array_merge($this->bindings['where'], (array)$bindings));
    }
    /**
     * @param array $bindings
     * @return array
     */
    protected function cleanBindings(array $bindings) {
        return array_values(array_filter($bindings, function ($binding) {
            return !$binding instanceof Expression;
        }));
    }
    /**
     * @param mixed $value
     * @return \Notadd\Foundation\Database\Query\Expression
     */
    public function raw($value) {
        return $this->connection->raw($value);
    }
    /**
     * @return array
     */
    public function getBindings() {
        return Arr::flatten($this->bindings);
    }
    /**
     * @return array
     */
    public function getRawBindings() {
        return $this->bindings;
    }
    /**
     * @param array $bindings
     * @param string $type
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setBindings(array $bindings, $type = 'where') {
        if(!array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }
        $this->bindings[$type] = $bindings;
        return $this;
    }
    /**
     * @param mixed $value
     * @param string $type
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addBinding($value, $type = 'where') {
        if(!array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }
        if(is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }
        return $this;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return $this
     */
    public function mergeBindings(Builder $query) {
        $this->bindings = array_merge_recursive($this->bindings, $query->bindings);
        return $this;
    }
    /**
     * @return \Notadd\Foundation\Database\ConnectionInterface
     */
    public function getConnection() {
        return $this->connection;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Processors\Processor
     */
    public function getProcessor() {
        return $this->processor;
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Grammars\Grammar
     */
    public function getGrammar() {
        return $this->grammar;
    }
    /**
     * @return $this
     */
    public function useWritePdo() {
        $this->useWritePdo = true;
        return $this;
    }
    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters) {
        if(static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
        if(Str::startsWith($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }
        $className = get_class($this);
        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}