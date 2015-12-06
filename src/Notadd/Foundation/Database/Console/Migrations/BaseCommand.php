<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 00:47
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Console\Command;
class BaseCommand extends Command {
    /**
     * @return string
     */
    protected function getMigrationPath() {
        return $this->notadd->databasePath() . '/migrations';
    }
}