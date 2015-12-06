<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:24
 */
namespace Notadd\Foundation\Database\Eloquent;
use ArrayAccess;
use Faker\Generator as Faker;
use Symfony\Component\Finder\Finder;
class Factory implements ArrayAccess {
    /**
     * @var \Faker\Generator
     */
    protected $faker;
    /**
     * @param \Faker\Generator $faker
     */
    public function __construct(Faker $faker) {
        $this->faker = $faker;
    }
    /**
     * @var array
     */
    protected $definitions = [];
    /**
     * @param \Faker\Generator $faker
     * @param string|null $pathToFactories
     * @return static
     */
    public static function construct(Faker $faker, $pathToFactories = null) {
        $pathToFactories = $pathToFactories ?: database_path('factories');
        $factory = new static($faker);
        if(is_dir($pathToFactories)) {
            foreach(Finder::create()->files()->in($pathToFactories) as $file) {
                require $file->getRealPath();
            }
        }
        return $factory;
    }
    /**
     * @param string $class
     * @param string $name
     * @param callable $attributes
     * @return void
     */
    public function defineAs($class, $name, callable $attributes) {
        return $this->define($class, $attributes, $name);
    }
    /**
     * @param string $class
     * @param callable $attributes
     * @param string $name
     * @return void
     */
    public function define($class, callable $attributes, $name = 'default') {
        $this->definitions[$class][$name] = $attributes;
    }
    /**
     * @param string $class
     * @param array $attributes
     * @return mixed
     */
    public function create($class, array $attributes = []) {
        return $this->of($class)->create($attributes);
    }
    /**
     * @param string $class
     * @param string $name
     * @param array $attributes
     * @return mixed
     */
    public function createAs($class, $name, array $attributes = []) {
        return $this->of($class, $name)->create($attributes);
    }
    /**
     * @param string $class
     * @param array $attributes
     * @return mixed
     */
    public function make($class, array $attributes = []) {
        return $this->of($class)->make($attributes);
    }
    /**
     * @param string $class
     * @param string $name
     * @param array $attributes
     * @return mixed
     */
    public function makeAs($class, $name, array $attributes = []) {
        return $this->of($class, $name)->make($attributes);
    }
    /**
     * @param string $class
     * @param string $name
     * @param array $attributes
     * @return array
     */
    public function rawOf($class, $name, array $attributes = []) {
        return $this->raw($class, $attributes, $name);
    }
    /**
     * @param string $class
     * @param array $attributes
     * @param string $name
     * @return array
     */
    public function raw($class, array $attributes = [], $name = 'default') {
        $raw = call_user_func($this->definitions[$class][$name], $this->faker);
        return array_merge($raw, $attributes);
    }
    /**
     * @param string $class
     * @param string $name
     * @return \Notadd\Foundation\Database\Eloquent\FactoryBuilder
     */
    public function of($class, $name = 'default') {
        return new FactoryBuilder($class, $name, $this->definitions, $this->faker);
    }
    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->definitions[$offset]);
    }
    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->make($offset);
    }
    /**
     * @param string $offset
     * @param callable $value
     * @return void
     */
    public function offsetSet($offset, $value) {
        return $this->define($offset, $value);
    }
    /**
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->definitions[$offset]);
    }
}