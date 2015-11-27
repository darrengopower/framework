<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 10:11
 */
namespace Notadd\Foundation;
class AliasLoader {
    /**
     * @var array
     */
    protected $aliases;
    /**
     * @var bool
     */
    protected $registered = false;
    /**
     * @var \Notadd\Foundation\AliasLoader
     */
    protected static $instance;
    /**
     * @param  array $aliases
     */
    private function __construct($aliases) {
        $this->aliases = $aliases;
    }
    /**
     * @param  array $aliases
     * @return \Notadd\Foundation\AliasLoader
     */
    public static function getInstance(array $aliases = []) {
        if(is_null(static::$instance)) {
            return static::$instance = new static($aliases);
        }
        $aliases = array_merge(static::$instance->getAliases(), $aliases);
        static::$instance->setAliases($aliases);
        return static::$instance;
    }
    /**
     * @param  string $alias
     * @return void
     */
    public function load($alias) {
        if(isset($this->aliases[$alias])) {
            return class_alias($this->aliases[$alias], $alias);
        }
    }
    /**
     * @param  string $class
     * @param  string $alias
     * @return void
     */
    public function alias($class, $alias) {
        $this->aliases[$class] = $alias;
    }
    /**
     * @return void
     */
    public function register() {
        if(!$this->registered) {
            $this->prependToLoaderStack();
            $this->registered = true;
        }
    }
    /**
     * @return void
     */
    protected function prependToLoaderStack() {
        spl_autoload_register([
            $this,
            'load'
        ], true, true);
    }
    /**
     * @return array
     */
    public function getAliases() {
        return $this->aliases;
    }
    /**
     * @param  array $aliases
     * @return void
     */
    public function setAliases(array $aliases) {
        $this->aliases = $aliases;
    }
    /**
     * @return bool
     */
    public function isRegistered() {
        return $this->registered;
    }
    /**
     * @param  bool $value
     * @return void
     */
    public function setRegistered($value) {
        $this->registered = $value;
    }
    /**
     * @param  \Notadd\Foundation\AliasLoader $loader
     * @return void
     */
    public static function setInstance($loader) {
        static::$instance = $loader;
    }
    /**
     * @return void
     */
    private function __clone() {
    }
}