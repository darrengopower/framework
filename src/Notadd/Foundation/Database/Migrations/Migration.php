<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:03
 */
namespace Notadd\Foundation\Database\Migrations;
use Illuminate\Contracts\Foundation\Application;
abstract class Migration {
    /**
     * @var string
     */
    protected $connection;
    /**
     * @var \Notadd\Foundation\Database\Schema\Builder
     */
    protected $schema;
    /**
     * Migration constructor.
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @internal param \Notadd\Foundation\Database\Schema\Builder $schema
     */
    public function __construct(Application $application) {
        $this->schema = $application->make('db')->connection()->getSchemaBuilder();
    }
    /**
     * @return string
     */
    public function getConnection() {
        return $this->connection;
    }
}