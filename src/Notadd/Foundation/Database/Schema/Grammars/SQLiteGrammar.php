<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:21
 */
namespace Notadd\Foundation\Database\Schema\Grammars;
use Illuminate\Support\Fluent;
use Notadd\Foundation\Database\Connection;
use Notadd\Foundation\Database\Schema\Blueprint;
class SQLiteGrammar extends Grammar {
    /**
     * @var array
     */
    protected $modifiers = [
        'Nullable',
        'Default',
        'Increment'
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
        return "select * from sqlite_master where type = 'table' and name = ?";
    }
    /**
     * @param string $table
     * @return string
     */
    public function compileColumnExists($table) {
        return 'pragma table_info(' . str_replace('.', '__', $table) . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command) {
        $columns = implode(', ', $this->getColumns($blueprint));
        $sql = $blueprint->temporary ? 'create temporary' : 'create';
        $sql .= ' table ' . $this->wrapTable($blueprint) . " ($columns";
        $sql .= (string)$this->addForeignKeys($blueprint);
        $sql .= (string)$this->addPrimaryKeys($blueprint);
        return $sql . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @return string|null
     */
    protected function addForeignKeys(Blueprint $blueprint) {
        $sql = '';
        $foreigns = $this->getCommandsByName($blueprint, 'foreign');
        foreach($foreigns as $foreign) {
            $sql .= $this->getForeignKey($foreign);
            if(!is_null($foreign->onDelete)) {
                $sql .= " on delete {$foreign->onDelete}";
            }
            if(!is_null($foreign->onUpdate)) {
                $sql .= " on update {$foreign->onUpdate}";
            }
        }
        return $sql;
    }
    /**
     * @param \Illuminate\Support\Fluent $foreign
     * @return string
     */
    protected function getForeignKey($foreign) {
        $on = $this->wrapTable($foreign->on);
        $columns = $this->columnize($foreign->columns);
        $onColumns = $this->columnize((array)$foreign->references);
        return ", foreign key($columns) references $on($onColumns)";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @return string|null
     */
    protected function addPrimaryKeys(Blueprint $blueprint) {
        $primary = $this->getCommandByName($blueprint, 'primary');
        if(!is_null($primary)) {
            $columns = $this->columnize($primary->columns);
            return ", primary key ({$columns})";
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return array
     */
    public function compileAdd(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        $columns = $this->prefixArray('add column', $this->getColumns($blueprint));
        $statements = [];
        foreach($columns as $column) {
            $statements[] = 'alter table ' . $table . ' ' . $column;
        }
        return $statements;
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
    public function compileForeign(Blueprint $blueprint, Fluent $command) {
        // Handled on table creation...
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
     * @param \Notadd\Foundation\Database\Connection $connection
     * @return array
     */
    public function compileDropColumn(Blueprint $blueprint, Fluent $command, Connection $connection) {
        $schema = $connection->getDoctrineSchemaManager();
        $tableDiff = $this->getDoctrineTableDiff($blueprint, $schema);
        foreach($command->columns as $name) {
            $column = $connection->getDoctrineColumn($blueprint->getTable(), $name);
            $tableDiff->removedColumns[$name] = $column;
        }
        return (array)$schema->getDatabasePlatform()->getAlterTableSQL($tableDiff);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropUnique(Blueprint $blueprint, Fluent $command) {
        return "drop index {$command->index}";
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
    public function compileRename(Blueprint $blueprint, Fluent $command) {
        $from = $this->wrapTable($blueprint);
        return "alter table {$from} rename to " . $this->wrapTable($command->to);
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeChar(Fluent $column) {
        return 'varchar';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeString(Fluent $column) {
        return 'varchar';
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
        return 'integer';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBigInteger(Fluent $column) {
        return 'integer';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column) {
        return 'integer';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTinyInteger(Fluent $column) {
        return 'integer';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeSmallInteger(Fluent $column) {
        return 'integer';
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
        return 'numeric';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBoolean(Fluent $column) {
        return 'tinyint';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeEnum(Fluent $column) {
        return 'varchar';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeJson(Fluent $column) {
        return 'text';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeJsonb(Fluent $column) {
        return 'text';
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
        return 'datetime';
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
            return 'datetime default CURRENT_TIMESTAMP';
        }
        return 'datetime';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeBinary(Fluent $column) {
        return 'blob';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeUuid(Fluent $column) {
        return 'varchar';
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
            return ' primary key autoincrement';
        }
    }
}