<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 16:00
 */
namespace Notadd\Foundation\Console;
class ClearCompiledCommand extends Command {
    protected $name = 'clear-compiled';
    protected $description = 'Remove the compiled class file';
    public function fire() {
        $compiledPath = $this->notadd->getCachedCompilePath();
        $servicesPath = $this->notadd->getCachedServicesPath();
        if(file_exists($compiledPath)) {
            @unlink($compiledPath);
        }
        if(file_exists($servicesPath)) {
            @unlink($servicesPath);
        }
    }
}