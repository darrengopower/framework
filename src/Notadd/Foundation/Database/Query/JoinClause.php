<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:37
 */
namespace Notadd\Foundation\Database\Query;
use Closure;
use InvalidArgumentException;
class JoinClause {
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $table;
    /**
     * @var array
     */
    public $clauses = [];
    /**
     * @var array
     */
    public $bindings = [];
    /**
     * @param string $type
     * @param string $table
     */
    public function __construct($type, $table) {
        $this->type = $type;
        $this->table = $table;
    }
    /**
     * @param string|\Closure $first
     * @param string|null $operator
     * @param string|null $second
     * @param string $boolean
     * @param bool $where
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function on($first, $operator = null, $second = null, $boolean = 'and', $where = false) {
        if($first instanceof Closure) {
            return $this->nest($first, $boolean);
        }
        if(func_num_args() < 3) {
            throw new InvalidArgumentException('Not enough arguments for the on clause.');
        }
        if($where) {
            $this->bindings[] = $second;
        }
        if($where && ($operator === 'in' || $operator === 'not in') && is_array($second)) {
            $second = count($second);
        }
        $nested = false;
        $this->clauses[] = compact('first', 'operator', 'second', 'boolean', 'where', 'nested');
        return $this;
    }
    /**
     * @param string|\Closure $first
     * @param string|null $operator
     * @param string|null $second
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function orOn($first, $operator = null, $second = null) {
        return $this->on($first, $operator, $second, 'or');
    }
    /**
     * @param string|\Closure $first
     * @param string|null $operator
     * @param string|null $second
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function where($first, $operator = null, $second = null, $boolean = 'and') {
        return $this->on($first, $operator, $second, $boolean, true);
    }
    /**
     * @param string|\Closure $first
     * @param string|null $operator
     * @param string|null $second
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function orWhere($first, $operator = null, $second = null) {
        return $this->on($first, $operator, $second, 'or', true);
    }
    /**
     * @param string $column
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function whereNull($column, $boolean = 'and') {
        return $this->on($column, 'is', new Expression('null'), $boolean, false);
    }
    /**
     * @param string $column
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function orWhereNull($column) {
        return $this->whereNull($column, 'or');
    }
    /**
     * @param string $column
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function whereNotNull($column, $boolean = 'and') {
        return $this->on($column, 'is', new Expression('not null'), $boolean, false);
    }
    /**
     * @param string $column
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function orWhereNotNull($column) {
        return $this->whereNotNull($column, 'or');
    }
    /**
     * @param string $column
     * @param array $values
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function whereIn($column, array $values) {
        return $this->on($column, 'in', $values, 'and', true);
    }
    /**
     * @param string $column
     * @param array $values
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function whereNotIn($column, array $values) {
        return $this->on($column, 'not in', $values, 'and', true);
    }
    /**
     * @param string $column
     * @param array $values
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function orWhereIn($column, array $values) {
        return $this->on($column, 'in', $values, 'or', true);
    }
    /**
     * @param string $column
     * @param array $values
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function orWhereNotIn($column, array $values) {
        return $this->on($column, 'not in', $values, 'or', true);
    }
    /**
     * @param \Closure $callback
     * @param string $boolean
     * @return \Notadd\Foundation\Database\Query\JoinClause
     */
    public function nest(Closure $callback, $boolean = 'and') {
        $join = new static($this->type, $this->table);
        $callback($join);
        if(count($join->clauses)) {
            $nested = true;
            $this->clauses[] = compact('nested', 'join', 'boolean');
            $this->bindings = array_merge($this->bindings, $join->bindings);
        }
        return $this;
    }
}