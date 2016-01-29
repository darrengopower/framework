<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:04
 */
namespace Notadd\Foundation\Database;
/**
 * Class Grammar
 * @package Notadd\Foundation\Database
 */
abstract class Grammar {
    /**
     * @var string
     */
    protected $tablePrefix = '';
    /**
     * @param array $values
     * @return array
     */
    public function wrapArray(array $values) {
        return array_map([
            $this,
            'wrap'
        ], $values);
    }
    /**
     * @param string|\Notadd\Foundation\Database\Query\Expression $table
     * @return string
     */
    public function wrapTable($table) {
        if($this->isExpression($table)) {
            return $this->getValue($table);
        }
        return $this->wrap($this->tablePrefix . $table, true);
    }
    /**
     * @param string|\Notadd\Foundation\Database\Query\Expression $value
     * @param bool $prefixAlias
     * @return string
     */
    public function wrap($value, $prefixAlias = false) {
        if($this->isExpression($value)) {
            return $this->getValue($value);
        }
        if(strpos(strtolower($value), ' as ') !== false) {
            $segments = explode(' ', $value);
            if($prefixAlias) {
                $segments[2] = $this->tablePrefix . $segments[2];
            }
            return $this->wrap($segments[0]) . ' as ' . $this->wrapValue($segments[2]);
        }
        $wrapped = [];
        $segments = explode('.', $value);
        foreach($segments as $key => $segment) {
            if($key == 0 && count($segments) > 1) {
                $wrapped[] = $this->wrapTable($segment);
            } else {
                $wrapped[] = $this->wrapValue($segment);
            }
        }
        return implode('.', $wrapped);
    }
    /**
     * @param string $value
     * @return string
     */
    protected function wrapValue($value) {
        if($value === '*') {
            return $value;
        }
        return '"' . str_replace('"', '""', $value) . '"';
    }
    /**
     * @param array $columns
     * @return string
     */
    public function columnize(array $columns) {
        return implode(', ', array_map([
            $this,
            'wrap'
        ], $columns));
    }
    /**
     * @param array $values
     * @return string
     */
    public function parameterize(array $values) {
        return implode(', ', array_map([
            $this,
            'parameter'
        ], $values));
    }
    /**
     * @param mixed $value
     * @return string
     */
    public function parameter($value) {
        return $this->isExpression($value) ? $this->getValue($value) : '?';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Expression $expression
     * @return string
     */
    public function getValue($expression) {
        return $expression->getValue();
    }
    /**
     * @param mixed $value
     * @return bool
     */
    public function isExpression($value) {
        return $value instanceof Expression;
    }
    /**
     * @return string
     */
    public function getDateFormat() {
        return 'Y-m-d H:i:s';
    }
    /**
     * @return string
     */
    public function getTablePrefix() {
        return $this->tablePrefix;
    }
    /**
     * @param string $prefix
     * @return $this
     */
    public function setTablePrefix($prefix) {
        $this->tablePrefix = $prefix;
        return $this;
    }
}