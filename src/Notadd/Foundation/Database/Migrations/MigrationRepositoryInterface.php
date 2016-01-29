<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:00
 */
namespace Notadd\Foundation\Database\Migrations;
/**
 * Interface MigrationRepositoryInterface
 * @package Notadd\Foundation\Database\Migrations
 */
interface MigrationRepositoryInterface {
    /**
     * @return array
     */
    public function getRan();
    /**
     * @return array
     */
    public function getLast();
    /**
     * @param string $file
     * @param int $batch
     * @return void
     */
    public function log($file, $batch);
    /**
     * @param object $migration
     * @return void
     */
    public function delete($migration);
    /**
     * @return int
     */
    public function getNextBatchNumber();
    /**
     * @return void
     */
    public function createRepository();
    /**
     * @return bool
     */
    public function repositoryExists();
    /**
     * @param string $name
     * @return void
     */
    public function setSource($name);
}