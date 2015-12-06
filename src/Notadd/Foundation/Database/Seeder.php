<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:39
 */
namespace Notadd\Foundation\Database;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
class Seeder {
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;
    /**
     * @var \Illuminate\Console\Command
     */
    protected $command;
    /**
     * @return void
     */
    public function run() {
    }
    /**
     * @param string $class
     * @return void
     */
    public function call($class) {
        $this->resolve($class)->run();
        if(isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Seeded:</info> $class");
        }
    }
    /**
     * @param string $class
     * @return \Notadd\Foundation\Database\Seeder
     */
    protected function resolve($class) {
        if(isset($this->container)) {
            $instance = $this->container->make($class);
            $instance->setContainer($this->container);
        } else {
            $instance = new $class;
        }
        if(isset($this->command)) {
            $instance->setCommand($this->command);
        }
        return $instance;
    }
    /**
     * @param \Illuminate\Container\Container $container
     * @return $this
     */
    public function setContainer(Container $container) {
        $this->container = $container;
        return $this;
    }
    /**
     * @param \Illuminate\Console\Command $command
     * @return $this
     */
    public function setCommand(Command $command) {
        $this->command = $command;
        return $this;
    }
}