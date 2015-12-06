<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:24
 */
namespace Notadd\Foundation\Database\Schema\Grammars;
use Illuminate\Support\Fluent;
use Notadd\Foundation\Database\Schema\Blueprint;
class SqlServerGrammar extends Grammar {
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
        'tinyInteger',
        'smallInteger',
        'mediumInteger',
        'integer',
        'bigInteger'
    ];
    /**
     * @return string
     */
    public function compileTableExists() {
        return "select * from sysobjects where type = 'U' and name = ?";
    }
    /**
     * @param string $table
     * @return string
     */
    public function compileColumnExists($table) {
        return "select col.name from sys.columns as col
                join sys.objects as obj on col.object_id = obj.object_id
                where obj.type = 'U' and obj.name = '$table'";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command) {
        $columns = implode(', ', $this->getColumns($blueprint));
        return 'create table ' . $this->wrapTable($blueprint) . " ($columns)";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileAdd(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        $columns = $this->getColumns($blueprint);
        return 'alter table ' . $table . ' add ' . implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compilePrimary(Blueprint $blueprint, Fluent $command) {
        $columns = $this->columnize($command->columns);
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} add constraint {$command->index} primary key ({$columns})";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileUnique(Blueprint $blueprint, Fluent $command) {
        $columns = $this->columnize($command->columns);
        $table = $this->wrapTable($blueprint);
        return "create unique index {$command->index} on {$table} ({$columns})";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileIndex(Blueprint $blueprint, Fluent $command) {
        $columns = $this->columnize($command->columns);
        $table = $this->wrapTable($blueprint);
        return "create index {$command->index} on {$table} ({$columns})";
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
        return 'if exists (select * from INFORMATION_SCHEMA.TABLES where TABLE_NAME = \'' . $blueprint->getTable() . '\') drop table ' . $blueprint->getTable();
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropColumn(Blueprint $blueprint, Fluent $command) {
        $columns = $this->wrapArray($command->columns);
        $table = $this->wrapTable($blueprint);
        return 'alter table ' . $table . ' drop column ' . implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropPrimary(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} drop constraint {$command->index}";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropUnique(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "drop index {$command->index} on {$table}";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropIndex(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "drop index {$command->index} on {$table}";
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
        return "sp_rename {$from}, " . $this->wrapTable($command->to);
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeChar(Fluent $column) {
        return "nchar({$column->length})";
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeString(Fluent $column) {
        return "nvarchar({$column->length})";
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeText(Fluent $column) {
        return 'nvarchar(max)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeMediumText(Fluent $column) {
        return 'nvarchar(max)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeLongText(Fluent $column) {
        return 'nvarchar(max)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeInteger(Fluent $column) {
        return 'int';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBigInteger(Fluent $column) {
        return 'bigint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column) {
        return 'int';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTinyInteger(Fluent $column) {
        return 'tinyint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeSmallInteger(Fluent $column) {
        return 'smallint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeFloat(Fluent $column) {
        return 'float';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDouble(Fluent $column) {
        return 'float';
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
        return 'bit';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeEnum(Fluent $column) {
        return 'nvarchar(255)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeJson(Fluent $column) {
        return 'nvarchar(max)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeJsonb(Fluent $column) {
        return 'nvarchar(max)';
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
        return 'datetime';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDateTimeTz(Fluent $column) {
        return 'datetimeoffset(0)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTime(Fluent $column) {
        return 'time';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimeTz(Fluent $column) {
        return 'time';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimestamp(Fluent $column) {
        if($column->useCurrent) {
            return 'datetime default CURRENT_TIMESTAMP';
        }
        return 'datetime';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimestampTz(Fluent $column) {
        if($column->useCurrent) {
            return 'datetimeoffset(0) default CURRENT_TIMESTAMP';
        }
        return 'datetimeoffset(0)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBinary(Fluent $column) {
        return 'varbinary(max)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeUuid(Fluent $column) {
        return 'uniqueidentifier';
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
            return ' identity primary key';
        }
    }
}