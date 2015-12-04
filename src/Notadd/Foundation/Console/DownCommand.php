<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 16:12
 */
namespace Notadd\Foundation\Console;
class DownCommand extends Command {
    protected $name = 'down';
    protected $description = 'Put the application into maintenance mode';
    public function fire() {
        touch($this->notadd->storagePath() . '/framework/down');
        $this->comment('Application is now in maintenance mode.');
    }
}