<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:29
 */
namespace Notadd\Foundation\Database\Query\Grammars;
use Notadd\Foundation\Database\Grammar as BaseGrammar;
use Notadd\Foundation\Database\Query\Builder;
/**
 * Class Grammar
 * @package Notadd\Foundation\Database\Query\Grammars
 */
class Grammar extends BaseGrammar {
    /**
     * @var array
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'unions',
        'lock',
    ];
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileSelect(Builder $query) {
        if(is_null($query->columns)) {
            $query->columns = ['*'];
        }
        return trim($this->concatenate($this->compileComponents($query)));
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return array
     */
    protected function compileComponents(Builder $query) {
        $sql = [];
        foreach($this->selectComponents as $component) {
            if(!is_null($query->$component)) {
                $method = 'compile' . ucfirst($component);
                $sql[$component] = $this->$method($query, $query->$component);
            }
        }
        return $sql;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $aggregate
     * @return string
     */
    protected function compileAggregate(Builder $query, $aggregate) {
        $column = $this->columnize($aggregate['columns']);
        if($query->distinct && $column !== '*') {
            $column = 'distinct ' . $column;
        }
        return 'select ' . $aggregate['function'] . '(' . $column . ') as aggregate';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $columns
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns) {
        if(!is_null($query->aggregate)) {
            return;
        }
        $select = $query->distinct ? 'select distinct ' : 'select ';
        return $select . $this->columnize($columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param string $table
     * @return string
     */
    protected function compileFrom(Builder $query, $table) {
        return 'from ' . $this->wrapTable($table);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $joins
     * @return string
     */
    protected function compileJoins(Builder $query, $joins) {
        $sql = [];
        foreach($joins as $join) {
            $table = $this->wrapTable($join->table);
            $clauses = [];
            foreach($join->clauses as $clause) {
                $clauses[] = $this->compileJoinConstraint($clause);
            }
            $clauses[0] = $this->removeLeadingBoolean($clauses[0]);
            $clauses = implode(' ', $clauses);
            $type = $join->type;
            $sql[] = "$type join $table on $clauses";
        }
        return implode(' ', $sql);
    }
    /**
     * @param array $clause
     * @return string
     */
    protected function compileJoinConstraint(array $clause) {
        if($clause['nested']) {
            return $this->compileNestedJoinConstraint($clause);
        }
        $first = $this->wrap($clause['first']);
        if($clause['where']) {
            if($clause['operator'] === 'in' || $clause['operator'] === 'not in') {
                $second = '(' . implode(', ', array_fill(0, $clause['second'], '?')) . ')';
            } else {
                $second = '?';
            }
        } else {
            $second = $this->wrap($clause['second']);
        }
        return "{$clause['boolean']} $first {$clause['operator']} $second";
    }
    /**
     * @param array $clause
     * @return string
     */
    protected function compileNestedJoinConstraint(array $clause) {
        $clauses = [];
        foreach($clause['join']->clauses as $nestedClause) {
            $clauses[] = $this->compileJoinConstraint($nestedClause);
        }
        $clauses[0] = $this->removeLeadingBoolean($clauses[0]);
        $clauses = implode(' ', $clauses);
        return "{$clause['boolean']} ({$clauses})";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    protected function compileWheres(Builder $query) {
        $sql = [];
        if(is_null($query->wheres)) {
            return '';
        }
        foreach($query->wheres as $where) {
            $method = "where{$where['type']}";
            $sql[] = $where['boolean'] . ' ' . $this->$method($query, $where);
        }
        if(count($sql) > 0) {
            $sql = implode(' ', $sql);
            return 'where ' . $this->removeLeadingBoolean($sql);
        }
        return '';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereNested(Builder $query, $where) {
        $nested = $where['query'];
        return '(' . substr($this->compileWheres($nested), 6) . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereSub(Builder $query, $where) {
        $select = $this->compileSelect($where['query']);
        return $this->wrap($where['column']) . ' ' . $where['operator'] . " ($select)";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereBasic(Builder $query, $where) {
        $value = $this->parameter($where['value']);
        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' ' . $value;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereBetween(Builder $query, $where) {
        $between = $where['not'] ? 'not between' : 'between';
        return $this->wrap($where['column']) . ' ' . $between . ' ? and ?';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereExists(Builder $query, $where) {
        return 'exists (' . $this->compileSelect($where['query']) . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereNotExists(Builder $query, $where) {
        return 'not exists (' . $this->compileSelect($where['query']) . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereIn(Builder $query, $where) {
        if(empty($where['values'])) {
            return '0 = 1';
        }
        $values = $this->parameterize($where['values']);
        return $this->wrap($where['column']) . ' in (' . $values . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereNotIn(Builder $query, $where) {
        if(empty($where['values'])) {
            return '1 = 1';
        }
        $values = $this->parameterize($where['values']);
        return $this->wrap($where['column']) . ' not in (' . $values . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereInSub(Builder $query, $where) {
        $select = $this->compileSelect($where['query']);
        return $this->wrap($where['column']) . ' in (' . $select . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereNotInSub(Builder $query, $where) {
        $select = $this->compileSelect($where['query']);
        return $this->wrap($where['column']) . ' not in (' . $select . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereNull(Builder $query, $where) {
        return $this->wrap($where['column']) . ' is null';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereNotNull(Builder $query, $where) {
        return $this->wrap($where['column']) . ' is not null';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereDate(Builder $query, $where) {
        return $this->dateBasedWhere('date', $query, $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereDay(Builder $query, $where) {
        return $this->dateBasedWhere('day', $query, $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereMonth(Builder $query, $where) {
        return $this->dateBasedWhere('month', $query, $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereYear(Builder $query, $where) {
        return $this->dateBasedWhere('year', $query, $where);
    }
    /**
     * @param string $type
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function dateBasedWhere($type, Builder $query, $where) {
        $value = $this->parameter($where['value']);
        return $type . '(' . $this->wrap($where['column']) . ') ' . $where['operator'] . ' ' . $value;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereRaw(Builder $query, $where) {
        return $where['sql'];
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $groups
     * @return string
     */
    protected function compileGroups(Builder $query, $groups) {
        return 'group by ' . $this->columnize($groups);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $havings
     * @return string
     */
    protected function compileHavings(Builder $query, $havings) {
        $sql = implode(' ', array_map([
            $this,
            'compileHaving'
        ], $havings));
        return 'having ' . $this->removeLeadingBoolean($sql);
    }
    /**
     * @param array $having
     * @return string
     */
    protected function compileHaving(array $having) {
        if($having['type'] === 'raw') {
            return $having['boolean'] . ' ' . $having['sql'];
        }
        return $this->compileBasicHaving($having);
    }
    /**
     * @param array $having
     * @return string
     */
    protected function compileBasicHaving($having) {
        $column = $this->wrap($having['column']);
        $parameter = $this->parameter($having['value']);
        return $having['boolean'] . ' ' . $column . ' ' . $having['operator'] . ' ' . $parameter;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $orders
     * @return string
     */
    protected function compileOrders(Builder $query, $orders) {
        return 'order by ' . implode(', ', array_map(function ($order) {
            if(isset($order['sql'])) {
                return $order['sql'];
            }
            return $this->wrap($order['column']) . ' ' . $order['direction'];
        }, $orders));
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param int $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit) {
        return 'limit ' . (int)$limit;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param int $offset
     * @return string
     */
    protected function compileOffset(Builder $query, $offset) {
        return 'offset ' . (int)$offset;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    protected function compileUnions(Builder $query) {
        $sql = '';
        foreach($query->unions as $union) {
            $sql .= $this->compileUnion($union);
        }
        if(isset($query->unionOrders)) {
            $sql .= ' ' . $this->compileOrders($query, $query->unionOrders);
        }
        if(isset($query->unionLimit)) {
            $sql .= ' ' . $this->compileLimit($query, $query->unionLimit);
        }
        if(isset($query->unionOffset)) {
            $sql .= ' ' . $this->compileOffset($query, $query->unionOffset);
        }
        return ltrim($sql);
    }
    /**
     * @param array $union
     * @return string
     */
    protected function compileUnion(array $union) {
        $joiner = $union['all'] ? ' union all ' : ' union ';
        return $joiner . $union['query']->toSql();
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileExists(Builder $query) {
        $select = $this->compileSelect($query);
        return "select exists($select) as {$this->wrap('exists')}";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $values
     * @return string
     */
    public function compileInsert(Builder $query, array $values) {
        $table = $this->wrapTable($query->from);
        if(!is_array(reset($values))) {
            $values = [$values];
        }
        $columns = $this->columnize(array_keys(reset($values)));
        $parameters = [];
        foreach($values as $record) {
            $parameters[] = '(' . $this->parameterize($record) . ')';
        }
        $parameters = implode(', ', $parameters);
        return "insert into $table ($columns) values $parameters";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $values
     * @param string $sequence
     * @return string
     */
    public function compileInsertGetId(Builder $query, $values, $sequence) {
        return $this->compileInsert($query, $values);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $values
     * @return string
     */
    public function compileUpdate(Builder $query, $values) {
        $table = $this->wrapTable($query->from);
        $columns = [];
        foreach($values as $key => $value) {
            $columns[] = $this->wrap($key) . ' = ' . $this->parameter($value);
        }
        $columns = implode(', ', $columns);
        if(isset($query->joins)) {
            $joins = ' ' . $this->compileJoins($query, $query->joins);
        } else {
            $joins = '';
        }
        $where = $this->compileWheres($query);
        return trim("update {$table}{$joins} set $columns $where");
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileDelete(Builder $query) {
        $table = $this->wrapTable($query->from);
        $where = is_array($query->wheres) ? $this->compileWheres($query) : '';
        return trim("delete from $table " . $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return array
     */
    public function compileTruncate(Builder $query) {
        return ['truncate ' . $this->wrapTable($query->from) => []];
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param bool|string $value
     * @return string
     */
    protected function compileLock(Builder $query, $value) {
        return is_string($value) ? $value : '';
    }
    /**
     * @return bool
     */
    public function supportsSavepoints() {
        return true;
    }
    /**
     * @param string $name
     * @return string
     */
    public function compileSavepoint($name) {
        return 'SAVEPOINT ' . $name;
    }
    /**
     * @param string $name
     * @return string
     */
    public function compileSavepointRollBack($name) {
        return 'ROLLBACK TO SAVEPOINT ' . $name;
    }
    /**
     * @param array $segments
     * @return string
     */
    protected function concatenate($segments) {
        return implode(' ', array_filter($segments, function ($value) {
            return (string)$value !== '';
        }));
    }
    /**
     * @param string $value
     * @return string
     */
    protected function removeLeadingBoolean($value) {
        return preg_replace('/and |or /i', '', $value, 1);
    }
}