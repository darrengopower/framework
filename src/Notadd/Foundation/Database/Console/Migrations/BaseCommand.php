<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 00:47
 */
namespace Notadd\Foundation\Database\Console\Migrations;
use Notadd\Foundation\Console\Command;
/**
 * Class BaseCommand
 * @package Notadd\Foundation\Database\Console\Migrations
 */
class BaseCommand extends Command {
    /**
     * @return string
     */
    protected function getMigrationPath() {
        return realpath(__DIR__ . '/../../../../../../migrations');
    }
}