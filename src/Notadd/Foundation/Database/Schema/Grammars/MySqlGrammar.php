<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:15
 */
namespace Notadd\Foundation\Database\Schema\Grammars;
use Illuminate\Support\Fluent;
use Notadd\Foundation\Database\Connection;
use Notadd\Foundation\Database\Schema\Blueprint;
/**
 * Class MySqlGrammar
 * @package Notadd\Foundation\Database\Schema\Grammars
 */
class MySqlGrammar extends Grammar {
    /**
     * @var array
     */
    protected $modifiers = [
        'Unsigned',
        'Charset',
        'Collate',
        'Nullable',
        'Default',
        'Increment',
        'Comment',
        'After',
        'First'
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
        return 'select * from information_schema.tables where table_schema = ? and table_name = ?';
    }
    /**
     * @return string
     */
    public function compileColumnExists() {
        return 'select column_name from information_schema.columns where table_schema = ? and table_name = ?';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @param \Notadd\Foundation\Database\Connection $connection
     * @return string
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection) {
        $columns = implode(', ', $this->getColumns($blueprint));
        $sql = $blueprint->temporary ? 'create temporary' : 'create';
        $sql .= ' table ' . $this->wrapTable($blueprint) . " ($columns)";
        $sql = $this->compileCreateEncoding($sql, $connection, $blueprint);
        if(isset($blueprint->engine)) {
            $sql .= ' engine = ' . $blueprint->engine;
        }
        return $sql;
    }
    /**
     * @param string $sql
     * @param \Notadd\Foundation\Database\Connection $connection
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @return string
     */
    protected function compileCreateEncoding($sql, Connection $connection, Blueprint $blueprint) {
        if(isset($blueprint->charset)) {
            $sql .= ' default character set ' . $blueprint->charset;
        } elseif(!is_null($charset = $connection->getConfig('charset'))) {
            $sql .= ' default character set ' . $charset;
        }
        if(isset($blueprint->collation)) {
            $sql .= ' collate ' . $blueprint->collation;
        } elseif(!is_null($collation = $connection->getConfig('collation'))) {
            $sql .= ' collate ' . $collation;
        }
        return $sql;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileAdd(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        $columns = $this->prefixArray('add', $this->getColumns($blueprint));
        return 'alter table ' . $table . ' ' . implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compilePrimary(Blueprint $blueprint, Fluent $command) {
        $command->name(null);
        return $this->compileKey($blueprint, $command, 'primary key');
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileUnique(Blueprint $blueprint, Fluent $command) {
        return $this->compileKey($blueprint, $command, 'unique');
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileIndex(Blueprint $blueprint, Fluent $command) {
        return $this->compileKey($blueprint, $command, 'index');
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @param string $type
     * @return string
     */
    protected function compileKey(Blueprint $blueprint, Fluent $command, $type) {
        $columns = $this->columnize($command->columns);
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} add {$type} `{$command->index}`($columns)";
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
        $columns = $this->prefixArray('drop', $this->wrapArray($command->columns));
        $table = $this->wrapTable($blueprint);
        return 'alter table ' . $table . ' ' . implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropPrimary(Blueprint $blueprint, Fluent $command) {
        return 'alter table ' . $this->wrapTable($blueprint) . ' drop primary key';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropUnique(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} drop index `{$command->index}`";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropIndex(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} drop index `{$command->index}`";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropForeign(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        return "alter table {$table} drop foreign key `{$command->index}`";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileRename(Blueprint $blueprint, Fluent $command) {
        $from = $this->wrapTable($blueprint);
        return "rename table {$from} to " . $this->wrapTable($command->to);
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
        return 'mediumtext';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeLongText(Fluent $column) {
        return 'longtext';
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
    protected function typeInteger(Fluent $column) {
        return 'int';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeMediumInteger(Fluent $column) {
        return 'mediumint';
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
        return $this->typeDouble($column);
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeDouble(Fluent $column) {
        if($column->total && $column->places) {
            return "double({$column->total}, {$column->places})";
        }
        return 'double';
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
        return 'tinyint(1)';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeEnum(Fluent $column) {
        return "enum('" . implode("', '", $column->allowed) . "')";
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
            return 'timestamp default CURRENT_TIMESTAMP';
        }
        if(!$column->nullable && $column->default === null) {
            return 'timestamp default 0';
        }
        return 'timestamp';
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function typeTimestampTz(Fluent $column) {
        if($column->useCurrent) {
            return 'timestamp default CURRENT_TIMESTAMP';
        }
        if(!$column->nullable && $column->default === null) {
            return 'timestamp default 0';
        }
        return 'timestamp';
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
        return 'char(36)';
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyUnsigned(Blueprint $blueprint, Fluent $column) {
        if($column->unsigned) {
            return ' unsigned';
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyCharset(Blueprint $blueprint, Fluent $column) {
        if(!is_null($column->charset)) {
            return ' character set ' . $column->charset;
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyCollate(Blueprint $blueprint, Fluent $column) {
        if(!is_null($column->collation)) {
            return ' collate ' . $column->collation;
        }
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
            return ' auto_increment primary key';
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyFirst(Blueprint $blueprint, Fluent $column) {
        if(!is_null($column->first)) {
            return ' first';
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyAfter(Blueprint $blueprint, Fluent $column) {
        if(!is_null($column->after)) {
            return ' after ' . $this->wrap($column->after);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string|null
     */
    protected function modifyComment(Blueprint $blueprint, Fluent $column) {
        if(!is_null($column->comment)) {
            return ' comment "' . $column->comment . '"';
        }
    }
    /**
     * @param string $value
     * @return string
     */
    protected function wrapValue($value) {
        if($value === '*') {
            return $value;
        }
        return '`' . str_replace('`', '``', $value) . '`';
    }
}