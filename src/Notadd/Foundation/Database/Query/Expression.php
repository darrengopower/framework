<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:05
 */
namespace Notadd\Foundation\Database\Query;
/**
 * Class Expression
 * @package Notadd\Foundation\Database\Query
 */
class Expression {
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @param mixed $value
     */
    public function __construct($value) {
        $this->value = $value;
    }
    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->getValue();
    }
}