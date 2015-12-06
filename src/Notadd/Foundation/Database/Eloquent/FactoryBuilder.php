<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:26
 */
namespace Notadd\Foundation\Database\Eloquent;
use Faker\Generator as Faker;
use InvalidArgumentException;
class FactoryBuilder {
    /**
     * @var array
     */
    protected $definitions;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var string
     */
    protected $name = 'default';
    /**
     * @var int
     */
    protected $amount = 1;
    /**
     * @var \Faker\Generator
     */
    protected $faker;
    /**
     * @param string $class
     * @param string $name
     * @param array $definitions
     * @param \Faker\Generator $faker
     */
    public function __construct($class, $name, array $definitions, Faker $faker) {
        $this->name = $name;
        $this->class = $class;
        $this->faker = $faker;
        $this->definitions = $definitions;
    }
    /**
     * @param int $amount
     * @return $this
     */
    public function times($amount) {
        $this->amount = $amount;
        return $this;
    }
    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes = []) {
        $results = $this->make($attributes);
        if($this->amount === 1) {
            $results->save();
        } else {
            foreach($results as $result) {
                $result->save();
            }
        }
        return $results;
    }
    /**
     * @param array $attributes
     * @return mixed
     */
    public function make(array $attributes = []) {
        if($this->amount === 1) {
            return $this->makeInstance($attributes);
        } else {
            $results = [];
            for($i = 0; $i < $this->amount; $i++) {
                $results[] = $this->makeInstance($attributes);
            }
            return new Collection($results);
        }
    }
    /**
     * @param array $attributes
     * @return \Notadd\Foundation\Database\Eloquent\Model
     */
    protected function makeInstance(array $attributes = []) {
        return Model::unguarded(function () use ($attributes) {
            if(!isset($this->definitions[$this->class][$this->name])) {
                throw new InvalidArgumentException("Unable to locate factory with name [{$this->name}] [{$this->class}].");
            }
            $definition = call_user_func($this->definitions[$this->class][$this->name], $this->faker, $attributes);
            return new $this->class(array_merge($definition, $attributes));
        });
    }
}