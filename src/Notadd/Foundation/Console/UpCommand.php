<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 16:25
 */
namespace Notadd\Foundation\Console;
class UpCommand extends Command {
    protected $name = 'up';
    protected $description = 'Bring the application out of maintenance mode';
    public function fire() {
        @unlink($this->notadd->storagePath() . '/framework/down');
        $this->info('Application is now live.');
    }
}