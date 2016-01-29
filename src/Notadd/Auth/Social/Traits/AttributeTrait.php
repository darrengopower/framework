<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 18:47
 */
namespace Notadd\Auth\Social\Traits;
/**
 * Class AttributeTrait
 * @package Notadd\Auth\Social\Traits
 */
trait AttributeTrait {
    /**
     * @var array
     */
    protected $attributes;
    /**
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }
    /**
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function getAttribute($name, $default = null) {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }
    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
        return $this;
    }
    /**
     * @param array $attributes
     * @return $this
     */
    public function merge(array $attributes) {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }
    /**
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->attributes);
    }
    /**
     * @param $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->getAttribute($offset);
    }
    /**
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value) {
        $this->setAttribute($offset, $value);
    }
    /**
     * @param $offset
     */
    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }
    /**
     * @param $property
     * @return mixed
     */
    public function __get($property) {
        return $this->getAttribute($property);
    }
}