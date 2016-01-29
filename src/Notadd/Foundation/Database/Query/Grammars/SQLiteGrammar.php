<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:09
 */
namespace Notadd\Foundation\Database\Query\Grammars;
use Notadd\Foundation\Database\Query\Builder;
/**
 * Class SQLiteGrammar
 * @package Notadd\Foundation\Database\Query\Grammars
 */
class SQLiteGrammar extends Grammar {
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
        'not like',
        'between',
        'ilike',
        '&',
        '|',
        '<<',
        '>>',
    ];
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
        if(count($values) == 1) {
            return parent::compileInsert($query, reset($values));
        }
        $names = $this->columnize(array_keys(reset($values)));
        $columns = [];
        foreach(array_keys(reset($values)) as $column) {
            $columns[] = '? as ' . $this->wrap($column);
        }
        $columns = array_fill(0, count($values), implode(', ', $columns));
        return "insert into $table ($names) select " . implode(' union all select ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return array
     */
    public function compileTruncate(Builder $query) {
        $sql = ['delete from sqlite_sequence where name = ?' => [$query->from]];
        $sql['delete from ' . $this->wrapTable($query->from)] = [];
        return $sql;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereDate(Builder $query, $where) {
        return $this->dateBasedWhere('%Y-%m-%d', $query, $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereDay(Builder $query, $where) {
        return $this->dateBasedWhere('%d', $query, $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereMonth(Builder $query, $where) {
        return $this->dateBasedWhere('%m', $query, $where);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function whereYear(Builder $query, $where) {
        return $this->dateBasedWhere('%Y', $query, $where);
    }
    /**
     * @param string $type
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $where
     * @return string
     */
    protected function dateBasedWhere($type, Builder $query, $where) {
        $value = str_pad($where['value'], 2, '0', STR_PAD_LEFT);
        $value = $this->parameter($value);
        return 'strftime(\'' . $type . '\', ' . $this->wrap($where['column']) . ') ' . $where['operator'] . ' ' . $value;
    }
}