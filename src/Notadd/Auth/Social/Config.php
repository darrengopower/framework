<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-13 21:53
 */
namespace Notadd\Auth\Social;
use ArrayAccess;
use InvalidArgumentException;
class Config implements ArrayAccess {
    /**
     * @var array
     */
    protected $config;
    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        if(is_null($key)) {
            return $this->config;
        }
        if(isset($this->config[$key])) {
            return $this->config[$key];
        }
        foreach(explode('.', $key) as $segment) {
            if(!is_array($this->config) || !array_key_exists($segment, $this->config)) {
                return $default;
            }
            $this->config = $this->config[$segment];
        }
        return $this->config;
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public function set($key, $value) {
        if(is_null($key)) {
            throw new InvalidArgumentException("Invalid config key.");
        }
        $keys = explode('.', $key);
        while(count($keys) > 1) {
            $key = array_shift($keys);
            if(!isset($this->config[$key]) || !is_array($this->config[$key])) {
                $this->config[$key] = [];
            }
            $this->config = &$this->config[$key];
        }
        $this->config[array_shift($keys)] = $value;
        return $this->config;
    }
    /**
     * @param mixed $offset
     * @return bool
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->config);
    }
    /**
     * @param mixed $offset
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }
    /**
     * @param mixed $offset
     * @param mixed $value
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }
    /**
     * @param mixed $offset
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        $this->set($offset, null);
    }
}