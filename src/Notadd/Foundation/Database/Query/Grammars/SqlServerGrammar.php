<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:11
 */
namespace Notadd\Foundation\Database\Query\Grammars;
use Notadd\Foundation\Database\Query\Builder;
class SqlServerGrammar extends Grammar {
    /**
     * @var array
     */
    protected $operators = [
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '!<',
        '!>',
        '<>',
        '!=',
        'like',
        'not like',
        'between',
        'ilike',
        '&',
        '&=',
        '|',
        '|=',
        '^',
        '^=',
    ];
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileSelect(Builder $query) {
        if(is_null($query->columns)) {
            $query->columns = ['*'];
        }
        $components = $this->compileComponents($query);
        if($query->offset > 0) {
            return $this->compileAnsiOffset($query, $components);
        }
        return $this->concatenate($components);
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
        if($query->limit > 0 && $query->offset <= 0) {
            $select .= 'top ' . $query->limit . ' ';
        }
        return $select . $this->columnize($columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param string $table
     * @return string
     */
    protected function compileFrom(Builder $query, $table) {
        $from = parent::compileFrom($query, $table);
        if(is_string($query->lock)) {
            return $from . ' ' . $query->lock;
        }
        if(!is_null($query->lock)) {
            return $from . ' with(rowlock,' . ($query->lock ? 'updlock,' : '') . 'holdlock)';
        }
        return $from;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $components
     * @return string
     */
    protected function compileAnsiOffset(Builder $query, $components) {
        if(!isset($components['orders'])) {
            $components['orders'] = 'order by (select 0)';
        }
        $orderings = $components['orders'];
        $components['columns'] .= $this->compileOver($orderings);
        unset($components['orders']);
        $constraint = $this->compileRowConstraint($query);
        $sql = $this->concatenate($components);
        return $this->compileTableExpression($sql, $constraint);
    }
    /**
     * @param string $orderings
     * @return string
     */
    protected function compileOver($orderings) {
        return ", row_number() over ({$orderings}) as row_num";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    protected function compileRowConstraint($query) {
        $start = $query->offset + 1;
        if($query->limit > 0) {
            $finish = $query->offset + $query->limit;
            return "between {$start} and {$finish}";
        }
        return ">= {$start}";
    }
    /**
     * @param string $sql
     * @param string $constraint
     * @return string
     */
    protected function compileTableExpression($sql, $constraint) {
        return "select * from ({$sql}) as temp_table where row_num {$constraint}";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param int $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit) {
        return '';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param int $offset
     * @return string
     */
    protected function compileOffset(Builder $query, $offset) {
        return '';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return array
     */
    public function compileTruncate(Builder $query) {
        return ['truncate table ' . $this->wrapTable($query->from) => []];
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileExists(Builder $query) {
        $select = $this->compileSelect($query);
        return "select cast(case when exists($select) then 1 else 0 end as bit) as {$this->wrap('exists')}";
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereDate(Builder $query, $where) {
        $value = $this->parameter($where['value']);
        return 'cast(' . $this->wrap($where['column']) . ' as date) ' . $where['operator'] . ' ' . $value;
    }
    /**
     * @return bool
     */
    public function supportsSavepoints() {
        return false;
    }
    /**
     * @return string
     */
    public function getDateFormat() {
        return 'Y-m-d H:i:s.000';
    }
    /**
     * @param string $value
     * @return string
     */
    protected function wrapValue($value) {
        if($value === '*') {
            return $value;
        }
        return '[' . str_replace(']', ']]', $value) . ']';
    }
}