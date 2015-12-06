<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:11
 */
namespace Notadd\Foundation\Database\Schema\Grammars;
use Doctrine\DBAL\Schema\AbstractSchemaManager as SchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Fluent;
use Notadd\Foundation\Database\Connection;
use Notadd\Foundation\Database\Grammar as BaseGrammar;
use Notadd\Foundation\Database\Query\Expression;
use Notadd\Foundation\Database\Schema\Blueprint;
use RuntimeException;
abstract class Grammar extends BaseGrammar {
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @param \Notadd\Foundation\Database\Connection $connection
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function compileRenameColumn(Blueprint $blueprint, Fluent $command, Connection $connection) {
        $schema = $connection->getDoctrineSchemaManager();
        $table = $this->getTablePrefix() . $blueprint->getTable();
        $column = $connection->getDoctrineColumn($table, $command->from);
        $tableDiff = $this->getRenamedDiff($blueprint, $command, $column, $schema);
        return (array)$schema->getDatabasePlatform()->getAlterTableSQL($tableDiff);
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected function getRenamedDiff(Blueprint $blueprint, Fluent $command, Column $column, SchemaManager $schema) {
        $tableDiff = $this->getDoctrineTableDiff($blueprint, $schema);
        return $this->setRenamedColumns($tableDiff, $command, $column);
    }
    /**
     * @param \Doctrine\DBAL\Schema\TableDiff $tableDiff
     * @param \Illuminate\Support\Fluent $command
     * @param \Doctrine\DBAL\Schema\Column $column
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected function setRenamedColumns(TableDiff $tableDiff, Fluent $command, Column $column) {
        $newColumn = new Column($command->to, $column->getType(), $column->toArray());
        $tableDiff->renamedColumns = [$command->from => $newColumn];
        return $tableDiff;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileForeign(Blueprint $blueprint, Fluent $command) {
        $table = $this->wrapTable($blueprint);
        $on = $this->wrapTable($command->on);
        $columns = $this->columnize($command->columns);
        $onColumns = $this->columnize((array)$command->references);
        $sql = "alter table {$table} add constraint {$command->index} ";
        $sql .= "foreign key ({$columns}) references {$on} ({$onColumns})";
        if(!is_null($command->onDelete)) {
            $sql .= " on delete {$command->onDelete}";
        }
        if(!is_null($command->onUpdate)) {
            $sql .= " on update {$command->onUpdate}";
        }
        return $sql;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @return array
     */
    protected function getColumns(Blueprint $blueprint) {
        $columns = [];
        foreach($blueprint->getAddedColumns() as $column) {
            $sql = $this->wrap($column) . ' ' . $this->getType($column);
            $columns[] = $this->addModifiers($sql, $blueprint, $column);
        }
        return $columns;
    }
    /**
     * @param string $sql
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function addModifiers($sql, Blueprint $blueprint, Fluent $column) {
        foreach($this->modifiers as $modifier) {
            if(method_exists($this, $method = "modify{$modifier}")) {
                $sql .= $this->{$method}($blueprint, $column);
            }
        }
        return $sql;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param string $name
     * @return \Illuminate\Support\Fluent|null
     */
    protected function getCommandByName(Blueprint $blueprint, $name) {
        $commands = $this->getCommandsByName($blueprint, $name);
        if(count($commands) > 0) {
            return reset($commands);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param string $name
     * @return array
     */
    protected function getCommandsByName(Blueprint $blueprint, $name) {
        return array_filter($blueprint->getCommands(), function ($value) use ($name) {
            return $value->name == $name;
        });
    }
    /**
     * @param \Illuminate\Support\Fluent $column
     * @return string
     */
    protected function getType(Fluent $column) {
        return $this->{'type' . ucfirst($column->type)}($column);
    }
    /**
     * @param string $prefix
     * @param array $values
     * @return array
     */
    public function prefixArray($prefix, array $values) {
        return array_map(function ($value) use ($prefix) {
            return $prefix . ' ' . $value;
        }, $values);
    }
    /**
     * @param mixed $table
     * @return string
     */
    public function wrapTable($table) {
        if($table instanceof Blueprint) {
            $table = $table->getTable();
        }
        return parent::wrapTable($table);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Expression|string $value
     * @param bool $prefixAlias
     * @return string
     */
    public function wrap($value, $prefixAlias = false) {
        if($value instanceof Fluent) {
            $value = $value->name;
        }
        return parent::wrap($value, $prefixAlias);
    }
    /**
     * @param mixed $value
     * @return string
     */
    protected function getDefaultValue($value) {
        if($value instanceof Expression) {
            return $value;
        }
        if(is_bool($value)) {
            return "'" . (int)$value . "'";
        }
        return "'" . strval($value) . "'";
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected function getDoctrineTableDiff(Blueprint $blueprint, SchemaManager $schema) {
        $table = $this->getTablePrefix() . $blueprint->getTable();
        $tableDiff = new TableDiff($table);
        $tableDiff->fromTable = $schema->listTableDetails($table);
        return $tableDiff;
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Support\Fluent $command
     * @param \Notadd\Foundation\Database\Connection $connection
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function compileChange(Blueprint $blueprint, Fluent $command, Connection $connection) {
        if(!$connection->isDoctrineAvailable()) {
            throw new RuntimeException(sprintf('Changing columns for table "%s" requires Doctrine DBAL; install "doctrine/dbal".', $blueprint->getTable()));
        }
        $schema = $connection->getDoctrineSchemaManager();
        $tableDiff = $this->getChangedDiff($blueprint, $schema);
        if($tableDiff !== false) {
            return (array)$schema->getDatabasePlatform()->getAlterTableSQL($tableDiff);
        }
        return [];
    }
    /**
     * Get the Doctrine table difference for the given changes.
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     * @return \Doctrine\DBAL\Schema\TableDiff|bool
     */
    protected function getChangedDiff(Blueprint $blueprint, SchemaManager $schema) {
        $table = $schema->listTableDetails($this->getTablePrefix() . $blueprint->getTable());
        return (new Comparator)->diffTable($table, $this->getTableWithColumnChanges($blueprint, $table));
    }
    /**
     * @param \Notadd\Foundation\Database\Schema\Blueprint $blueprint
     * @param \Doctrine\DBAL\Schema\Table $table
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected function getTableWithColumnChanges(Blueprint $blueprint, Table $table) {
        $table = clone $table;
        foreach($blueprint->getChangedColumns() as $fluent) {
            $column = $this->getDoctrineColumnForChange($table, $fluent);
            foreach($fluent->getAttributes() as $key => $value) {
                if(!is_null($option = $this->mapFluentOptionToDoctrine($key))) {
                    if(method_exists($column, $method = 'set' . ucfirst($option))) {
                        $column->{$method}($this->mapFluentValueToDoctrine($option, $value));
                    }
                }
            }
        }
        return $table;
    }
    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param \Illuminate\Support\Fluent $fluent
     * @return \Doctrine\DBAL\Schema\Column
     */
    protected function getDoctrineColumnForChange(Table $table, Fluent $fluent) {
        return $table->changeColumn($fluent['name'], $this->getDoctrineColumnChangeOptions($fluent))->getColumn($fluent['name']);
    }
    /**
     * @param \Illuminate\Support\Fluent $fluent
     * @return array
     */
    protected function getDoctrineColumnChangeOptions(Fluent $fluent) {
        $options = ['type' => $this->getDoctrineColumnType($fluent['type'])];
        if(in_array($fluent['type'], [
            'text',
            'mediumText',
            'longText'
        ])) {
            $options['length'] = $this->calculateDoctrineTextLength($fluent['type']);
        }
        return $options;
    }
    /**
     * @param string $type
     * @return \Doctrine\DBAL\Types\Type
     */
    protected function getDoctrineColumnType($type) {
        $type = strtolower($type);
        switch($type) {
            case 'biginteger':
                $type = 'bigint';
                break;
            case 'smallinteger':
                $type = 'smallint';
                break;
            case 'mediumtext':
            case 'longtext':
                $type = 'text';
                break;
        }
        return Type::getType($type);
    }
    /**
     * @param string $type
     * @return int
     */
    protected function calculateDoctrineTextLength($type) {
        switch($type) {
            case 'mediumText':
                return 65535 + 1;
            case 'longText':
                return 16777215 + 1;
            default:
                return 255 + 1;
        }
    }
    /**
     * @param string $attribute
     * @return string|null
     */
    protected function mapFluentOptionToDoctrine($attribute) {
        switch($attribute) {
            case 'type':
            case 'name':
                return;
            case 'nullable':
                return 'notnull';
            case 'total':
                return 'precision';
            case 'places':
                return 'scale';
            default:
                return $attribute;
        }
    }
    /**
     * @param string $option
     * @param mixed $value
     * @return mixed
     */
    protected function mapFluentValueToDoctrine($option, $value) {
        return $option == 'notnull' ? !$value : $value;
    }
}