<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:13
 */
namespace Notadd\Foundation\Database\Schema;
use Closure;
use Illuminate\Support\Fluent;
use Notadd\Foundation\Database\Connection;
use Notadd\Foundation\Database\Schema\Grammars\Grammar;
/**
 * Class Blueprint
 * @package Notadd\Foundation\Database\Schema
 */
class Blueprint {
    /**
     * @var string
     */
    protected $table;
    /**
     * @var array
     */
    protected $columns = [];
    /**
     * @var array
     */
    protected $commands = [];
    /**
     * @var string
     */
    public $engine;
    /**
     */
    public $charset;
    /**
     */
    public $collation;
    /**
     * Whether to make the table temporary.
     * @var bool
     */
    public $temporary = false;
    /**
     * Blueprint constructor.
     * @param string $table
     * @param \Closure|null $callback
     */
    public function __construct($table, Closure $callback = null) {
        $this->table = $table;
        if(!is_null($callback)) {
            $callback($this);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Connection $connection
     * @param \Notadd\Foundation\Database\Schema\Grammars\Grammar $grammar
     */
    public function build(Connection $connection, Grammar $grammar) {
        foreach($this->toSql($connection, $grammar) as $statement) {
            $connection->statement($statement);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Connection $connection
     * @param \Notadd\Foundation\Database\Schema\Grammars\Grammar $grammar
     * @return array
     */
    public function toSql(Connection $connection, Grammar $grammar) {
        $this->addImpliedCommands();
        $statements = [];
        foreach($this->commands as $command) {
            $method = 'compile' . ucfirst($command->name);
            if(method_exists($grammar, $method)) {
                if(!is_null($sql = $grammar->$method($this, $command, $connection))) {
                    $statements = array_merge($statements, (array)$sql);
                }
            }
        }
        return $statements;
    }
    /**
     * @return void
     */
    protected function addImpliedCommands() {
        if(count($this->getAddedColumns()) > 0 && !$this->creating()) {
            array_unshift($this->commands, $this->createCommand('add'));
        }
        if(count($this->getChangedColumns()) > 0 && !$this->creating()) {
            array_unshift($this->commands, $this->createCommand('change'));
        }
        $this->addFluentIndexes();
    }
    /**
     * @return void
     */
    protected function addFluentIndexes() {
        foreach($this->columns as $column) {
            foreach([
                        'primary',
                        'unique',
                        'index'
                    ] as $index) {
                if($column->$index === true) {
                    $this->$index($column->name);
                    continue 2;
                }
                elseif(isset($column->$index)) {
                    $this->$index($column->name, $column->$index);
                    continue 2;
                }
            }
        }
    }
    /**
     * @return bool
     */
    protected function creating() {
        foreach($this->commands as $command) {
            if($command->name == 'create') {
                return true;
            }
        }
        return false;
    }
    /**
     * @return \Illuminate\Support\Fluent
     */
    public function create() {
        return $this->addCommand('create');
    }
    /**
     * @return void
     */
    public function temporary() {
        $this->temporary = true;
    }
    /**
     * @return \Illuminate\Support\Fluent
     */
    public function drop() {
        return $this->addCommand('drop');
    }
    /**
     * @return \Illuminate\Support\Fluent
     */
    public function dropIfExists() {
        return $this->addCommand('dropIfExists');
    }
    /**
     * @param array|mixed $columns
     * @return \Illuminate\Support\Fluent
     */
    public function dropColumn($columns) {
        $columns = is_array($columns) ? $columns : (array)func_get_args();
        return $this->addCommand('dropColumn', compact('columns'));
    }
    /**
     * @param string $from
     * @param string $to
     * @return \Illuminate\Support\Fluent
     */
    public function renameColumn($from, $to) {
        return $this->addCommand('renameColumn', compact('from', 'to'));
    }
    /**
     * @param string|array $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropPrimary($index = null) {
        return $this->dropIndexCommand('dropPrimary', 'primary', $index);
    }
    /**
     * @param string|array $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropUnique($index) {
        return $this->dropIndexCommand('dropUnique', 'unique', $index);
    }
    /**
     * @param string|array $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropIndex($index) {
        return $this->dropIndexCommand('dropIndex', 'index', $index);
    }
    /**
     * @param string $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropForeign($index) {
        return $this->dropIndexCommand('dropForeign', 'foreign', $index);
    }
    /**
     * @return void
     */
    public function dropTimestamps() {
        $this->dropColumn('created_at', 'updated_at');
    }
    /**
     * @return void
     */
    public function dropTimestampsTz() {
        $this->dropTimestamps();
    }
    /**
     * @return void
     */
    public function dropSoftDeletes() {
        $this->dropColumn('deleted_at');
    }
    /**
     * @return void
     */
    public function dropRememberToken() {
        $this->dropColumn('remember_token');
    }
    /**
     * @param string $to
     * @return \Illuminate\Support\Fluent
     */
    public function rename($to) {
        return $this->addCommand('rename', compact('to'));
    }
    /**
     * @param string|array $columns
     * @param string $name
     * @return \Illuminate\Support\Fluent
     */
    public function primary($columns, $name = null) {
        return $this->indexCommand('primary', $columns, $name);
    }
    /**
     * @param string|array $columns
     * @param string $name
     * @return \Illuminate\Support\Fluent
     */
    public function unique($columns, $name = null) {
        return $this->indexCommand('unique', $columns, $name);
    }
    /**
     * @param string|array $columns
     * @param string $name
     * @return \Illuminate\Support\Fluent
     */
    public function index($columns, $name = null) {
        return $this->indexCommand('index', $columns, $name);
    }
    /**
     * @param string|array $columns
     * @param string $name
     * @return \Illuminate\Support\Fluent
     */
    public function foreign($columns, $name = null) {
        return $this->indexCommand('foreign', $columns, $name);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function increments($column) {
        return $this->unsignedInteger($column, true);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function smallIncrements($column) {
        return $this->unsignedSmallInteger($column, true);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function mediumIncrements($column) {
        return $this->unsignedMediumInteger($column, true);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function bigIncrements($column) {
        return $this->unsignedBigInteger($column, true);
    }
    /**
     * @param string $column
     * @param int $length
     * @return \Illuminate\Support\Fluent
     */
    public function char($column, $length = 255) {
        return $this->addColumn('char', $column, compact('length'));
    }
    /**
     * @param string $column
     * @param int $length
     * @return \Illuminate\Support\Fluent
     */
    public function string($column, $length = 255) {
        return $this->addColumn('string', $column, compact('length'));
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function text($column) {
        return $this->addColumn('text', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function mediumText($column) {
        return $this->addColumn('mediumText', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function longText($column) {
        return $this->addColumn('longText', $column);
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     * @return \Illuminate\Support\Fluent
     */
    public function integer($column, $autoIncrement = false, $unsigned = false) {
        return $this->addColumn('integer', $column, compact('autoIncrement', 'unsigned'));
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     * @return \Illuminate\Support\Fluent
     */
    public function tinyInteger($column, $autoIncrement = false, $unsigned = false) {
        return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     * @return \Illuminate\Support\Fluent
     */
    public function smallInteger($column, $autoIncrement = false, $unsigned = false) {
        return $this->addColumn('smallInteger', $column, compact('autoIncrement', 'unsigned'));
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     * @return \Illuminate\Support\Fluent
     */
    public function mediumInteger($column, $autoIncrement = false, $unsigned = false) {
        return $this->addColumn('mediumInteger', $column, compact('autoIncrement', 'unsigned'));
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @param bool $unsigned
     * @return \Illuminate\Support\Fluent
     */
    public function bigInteger($column, $autoIncrement = false, $unsigned = false) {
        return $this->addColumn('bigInteger', $column, compact('autoIncrement', 'unsigned'));
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @return \Illuminate\Support\Fluent
     */
    public function unsignedTinyInteger($column, $autoIncrement = false) {
        return $this->tinyInteger($column, $autoIncrement, true);
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @return \Illuminate\Support\Fluent
     */
    public function unsignedSmallInteger($column, $autoIncrement = false) {
        return $this->smallInteger($column, $autoIncrement, true);
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @return \Illuminate\Support\Fluent
     */
    public function unsignedMediumInteger($column, $autoIncrement = false) {
        return $this->mediumInteger($column, $autoIncrement, true);
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @return \Illuminate\Support\Fluent
     */
    public function unsignedInteger($column, $autoIncrement = false) {
        return $this->integer($column, $autoIncrement, true);
    }
    /**
     * @param string $column
     * @param bool $autoIncrement
     * @return \Illuminate\Support\Fluent
     */
    public function unsignedBigInteger($column, $autoIncrement = false) {
        return $this->bigInteger($column, $autoIncrement, true);
    }
    /**
     * @param string $column
     * @param int $total
     * @param int $places
     * @return \Illuminate\Support\Fluent
     */
    public function float($column, $total = 8, $places = 2) {
        return $this->addColumn('float', $column, compact('total', 'places'));
    }
    /**
     * @param string $column
     * @param int|null $total
     * @param int|null $places
     * @return \Illuminate\Support\Fluent
     */
    public function double($column, $total = null, $places = null) {
        return $this->addColumn('double', $column, compact('total', 'places'));
    }
    /**
     * @param string $column
     * @param int $total
     * @param int $places
     * @return \Illuminate\Support\Fluent
     */
    public function decimal($column, $total = 8, $places = 2) {
        return $this->addColumn('decimal', $column, compact('total', 'places'));
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function boolean($column) {
        return $this->addColumn('boolean', $column);
    }
    /**
     * @param string $column
     * @param array $allowed
     * @return \Illuminate\Support\Fluent
     */
    public function enum($column, array $allowed) {
        return $this->addColumn('enum', $column, compact('allowed'));
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function json($column) {
        return $this->addColumn('json', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function jsonb($column) {
        return $this->addColumn('jsonb', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function date($column) {
        return $this->addColumn('date', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function dateTime($column) {
        return $this->addColumn('dateTime', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function dateTimeTz($column) {
        return $this->addColumn('dateTimeTz', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function time($column) {
        return $this->addColumn('time', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function timeTz($column) {
        return $this->addColumn('timeTz', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function timestamp($column) {
        return $this->addColumn('timestamp', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function timestampTz($column) {
        return $this->addColumn('timestampTz', $column);
    }
    /**
     * @return void
     */
    public function nullableTimestamps() {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
    }
    /**
     * @return void
     */
    public function timestamps() {
        $this->timestamp('created_at');
        $this->timestamp('updated_at');
    }
    /**
     * @return void
     */
    public function timestampsTz() {
        $this->timestampTz('created_at');
        $this->timestampTz('updated_at');
    }
    /**
     * @return \Illuminate\Support\Fluent
     */
    public function softDeletes() {
        return $this->timestamp('deleted_at')->nullable();
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function binary($column) {
        return $this->addColumn('binary', $column);
    }
    /**
     * @param string $column
     * @return \Illuminate\Support\Fluent
     */
    public function uuid($column) {
        return $this->addColumn('uuid', $column);
    }
    /**
     * @param string $name
     * @param string|null $indexName
     * @return void
     */
    public function morphs($name, $indexName = null) {
        $this->unsignedInteger("{$name}_id");
        $this->string("{$name}_type");
        $this->index([
            "{$name}_id",
            "{$name}_type"
        ], $indexName);
    }
    /**
     * @return \Illuminate\Support\Fluent
     */
    public function rememberToken() {
        return $this->string('remember_token', 100)->nullable();
    }
    /**
     * @param string $command
     * @param string $type
     * @param string|array $index
     * @return \Illuminate\Support\Fluent
     */
    protected function dropIndexCommand($command, $type, $index) {
        $columns = [];
        if(is_array($index)) {
            $columns = $index;
            $index = $this->createIndexName($type, $columns);
        }
        return $this->indexCommand($command, $columns, $index);
    }
    /**
     * @param string $type
     * @param string|array $columns
     * @param string $index
     * @return \Illuminate\Support\Fluent
     */
    protected function indexCommand($type, $columns, $index) {
        $columns = (array)$columns;
        if(is_null($index)) {
            $index = $this->createIndexName($type, $columns);
        }
        return $this->addCommand($type, compact('index', 'columns'));
    }
    /**
     * @param string $type
     * @param array $columns
     * @return string
     */
    protected function createIndexName($type, array $columns) {
        $index = strtolower($this->table . '_' . implode('_', $columns) . '_' . $type);
        return str_replace([
            '-',
            '.'
        ], '_', $index);
    }
    /**
     * @param string $type
     * @param string $name
     * @param array $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function addColumn($type, $name, array $parameters = []) {
        $attributes = array_merge(compact('type', 'name'), $parameters);
        $this->columns[] = $column = new Fluent($attributes);
        return $column;
    }
    /**
     * @param string $name
     * @return $this
     */
    public function removeColumn($name) {
        $this->columns = array_values(array_filter($this->columns, function ($c) use ($name) {
            return $c['attributes']['name'] != $name;
        }));
        return $this;
    }
    /**
     * @param string $name
     * @param array $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function addCommand($name, array $parameters = []) {
        $this->commands[] = $command = $this->createCommand($name, $parameters);
        return $command;
    }
    /**
     * @param string $name
     * @param array $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand($name, array $parameters = []) {
        return new Fluent(array_merge(compact('name'), $parameters));
    }
    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }
    /**
     * @return array
     */
    public function getColumns() {
        return $this->columns;
    }
    /**
     * @return array
     */
    public function getCommands() {
        return $this->commands;
    }
    /**
     * @return array
     */
    public function getAddedColumns() {
        return array_filter($this->columns, function ($column) {
            return !$column->change;
        });
    }
    /**
     * @return array
     */
    public function getChangedColumns() {
        return array_filter($this->columns, function ($column) {
            return !!$column->change;
        });
    }
}