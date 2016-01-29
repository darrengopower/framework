<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:18
 */
namespace Notadd\Foundation\Database\Schema\Grammars;
use Illuminate\Support\Fluent;
use Notadd\Foundation\Database\Schema\Blueprint;
/**
 * Class PostgresGrammar
 * @package Notadd\Foundation\Database\Schema\Grammars
 */
class PostgresGrammar extends Grammar {
    /**
     * @var array
     */
    protected $modifiers = [
        'Increment',
        'Nullable',
        'Default'
    ];
    /**
     * @var array
     */
    protected $serials = [
        'bigInteger',
        'integer',
        'mediumInteger',
        'smallInteger',
        'tinyInteger'
    ];
    /**
     * @return string
     */
    public function compileTableExists() {
        return 'select * from information_schema.tables where table_name = ?';
    }
    /**
     * @param string $table
     * @return string
     */
    public function compileColumnExists($table) {
        return "select column_name from information_schema.columns where table_name = '$table'";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command) {
        $columns = implode(', ', $this->getColumns($blueprint));
        $sql = $blueprint->temporary ? 'create temporary' : 'create';
        $sql .= ' table ' . $this->wrapTable($blueprint) . " ($columns)";
        return $sql;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileAdd(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        $columns = $this->prefixArray('add column', $this->getColumns($blueprint));
        return 'alter table ' . $table . ' ' . implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compilePrimary(Blueprint $blueprint, Fluent $command) {
        $columns = $this->columnize($command->columns);
        return 'alter table ' . $this->wrapTable($blueprint) . " add primary key ({$columns})";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileUnique(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        $columns = $this->columnize($command->columns);
        return "alter table $table add constraint {$command->index} unique ($columns)";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileIndex(Blueprint $blueprint, Fluent $command) {
        $columns = $this->columnize($command->columns);
        return "create index {$command->index} on " . $this->wrapTable($blueprint) . " ({$columns})";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDrop(Blueprint $blueprint, Fluent $command) {
        return 'drop table ' . $this->wrapTable($blueprint);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropIfExists(Blueprint $blueprint, Fluent $command) {
        return 'drop table if exists ' . $this->wrapTable($blueprint);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropColumn(Blueprint $blueprint, Fluent $command) {
        $columns = $this->prefixArray('drop column', $this->wrapArray($command->columns));
        $table = $this->wrapTable($blueprint);
        return 'alter table ' . $table . ' ' . implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropPrimary(Blueprint $blueprint, Fluent $command) {
        $table = $blueprint->getTable();
        return 'alter table ' . $this->wrapTable($blueprint) . " drop constraint {$table}_pkey";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropUnique(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} drop constraint {$command->index}";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropIndex(Blueprint $blueprint, Fluent $command) {
        return "drop index {$command->index}";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropForeign(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} drop constraint {$command->index}";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileRename(Blueprint $blueprint, Fluent $command) {
        $from = $this->wrapTable($blueprint);
        return "alter table {$from} rename to " . $this->wrapTable($command->to);
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeChar(Fluent $column) {
        return "char({$column->length})";
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeString(Fluent $column) {
        return "varchar({$column->length})";
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeText(Fluent $column) {
        return 'text';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeMediumText(Fluent $column) {
        return 'text';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeLongText(Fluent $column) {
        return 'text';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeInteger(Fluent $column) {
        return $column->autoIncrement ? 'serial' : 'integer';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBigInteger(Fluent $column) {
        return $column->autoIncrement ? 'bigserial' : 'bigint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column) {
        return $column->autoIncrement ? 'serial' : 'integer';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTinyInteger(Fluent $column) {
        return $column->autoIncrement ? 'smallserial' : 'smallint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeSmallInteger(Fluent $column) {
        return $column->autoIncrement ? 'smallserial' : 'smallint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeFloat(Fluent $column) {
        return $this->typeDouble($column);
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDouble(Fluent $column) {
        return 'double precision';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDecimal(Fluent $column) {
        return "decimal({$column->total}, {$column->places})";
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBoolean(Fluent $column) {
        return 'boolean';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeEnum(Fluent $column) {
        $allowed = array_map(function ($a) {
            return "'" . $a . "'";
        }, $column->allowed);
        return "varchar(255) check (\"{$column->name}\" in (" . implode(', ', $allowed) . '))';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeJson(Fluent $column) {
        return 'json';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeJsonb(Fluent $column) {
        return 'jsonb';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDate(Fluent $column) {
        return 'date';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDateTime(Fluent $column) {
        return 'timestamp(0) without time zone';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDateTimeTz(Fluent $column) {
        return 'timestamp(0) with time zone';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTime(Fluent $column) {
        return 'time(0) without time zone';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimeTz(Fluent $column) {
        return 'time(0) with time zone';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimestamp(Fluent $column) {
        if($column->useCurrent) {
            return 'timestamp(0) without time zone default CURRENT_TIMESTAMP(0)';
        }
        return 'timestamp(0) without time zone';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimestampTz(Fluent $column) {
        if($column->useCurrent) {
            return 'timestamp(0) with time zone default CURRENT_TIMESTAMP(0)';
        }
        return 'timestamp(0) with time zone';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBinary(Fluent $column) {
        return 'bytea';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeUuid(Fluent $column) {
        return 'uuid';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyNullable(Blueprint $blueprint, Fluent $column) {
        return $column->nullable ? ' null' : ' not null';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyDefault(Blueprint $blueprint, Fluent $column) {
        if(!is_null($column->default)) {
            return ' default ' . $this->getDefaultValue($column->default);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyIncrement(Blueprint $blueprint, Fluent $column) {
        if(in_array($column->type, $this->serials) && $column->autoIncrement) {
            return ' primary key';
        }
    }
}