<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:01
 */
namespace Notadd\Foundation\Database\Migrations;
use Notadd\Foundation\Database\ConnectionResolverInterface as Resolver;
class DatabaseMigrationRepository implements MigrationRepositoryInterface {
    /**
     * @var \Notadd\Foundation\Database\ConnectionResolverInterface
     */
    protected $resolver;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var string
     */
    protected $connection;
    /**
     * @param \Notadd\Foundation\Database\ConnectionResolverInterface $resolver
     * @param string $table
     */
    public function __construct(Resolver $resolver, $table) {
        $this->table = $table ? $table : 'migrations';
        $this->resolver = $resolver;
    }
    /**
     * @return array
     */
    public function getRan() {
        return $this->table()->orderBy('batch', 'asc')->orderBy('migration', 'asc')->lists('migration');
    }
    /**
     * @return array
     */
    public function getLast() {
        $query = $this->table()->where('batch', $this->getLastBatchNumber());
        return $query->orderBy('migration', 'desc')->get();
    }
    /**
     * @param string $file
     * @param int $batch
     * @return void
     */
    public function log($file, $batch) {
        $record = [
            'migration' => $file,
            'batch' => $batch
        ];
        $this->table()->insert($record);
    }
    /**
     * @param object $migration
     * @return void
     */
    public function delete($migration) {
        $this->table()->where('migration', $migration->migration)->delete();
    }
    /**
     * @return int
     */
    public function getNextBatchNumber() {
        return $this->getLastBatchNumber() + 1;
    }
    /**
     * @return int
     */
    public function getLastBatchNumber() {
        return $this->table()->max('batch');
    }
    /**
     * @return void
     */
    public function createRepository() {
        $schema = $this->getConnection()->getSchemaBuilder();
        $schema->create($this->table, function ($table) {
            $table->string('migration');
            $table->integer('batch');
        });
    }
    /**
     * @return bool
     */
    public function repositoryExists() {
        $schema = $this->getConnection()->getSchemaBuilder();
        return $schema->hasTable($this->table);
    }
    /**
     * @return \Notadd\Foundation\Database\Query\Builder
     */
    protected function table() {
        return $this->getConnection()->table($this->table);
    }
    /**
     * @return \Notadd\Foundation\Database\ConnectionResolverInterface
     */
    public function getConnectionResolver() {
        return $this->resolver;
    }
    /**
     * @return \Notadd\Foundation\Database\Connection
     */
    public function getConnection() {
        return $this->resolver->connection($this->connection);
    }
    /**
     * @param string $name
     * @return void
     */
    public function setSource($name) {
        $this->connection = $name;
    }
}