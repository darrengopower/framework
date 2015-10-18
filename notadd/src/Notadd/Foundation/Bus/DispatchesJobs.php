<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Bus;
use ArrayAccess;
trait DispatchesJobs {
    /**
     * @param  mixed $job
     * @return mixed
     */
    protected function dispatch($job) {
        return app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
    /**
     * @param  mixed $job
     * @param  array $array
     * @return mixed
     */
    protected function dispatchFromArray($job, array $array) {
        return app('Illuminate\Contracts\Bus\Dispatcher')->dispatchFromArray($job, $array);
    }
    /**
     * @param  mixed $job
     * @param  \ArrayAccess $source
     * @param  array $extras
     * @return mixed
     */
    protected function dispatchFrom($job, ArrayAccess $source, $extras = []) {
        return app('Illuminate\Contracts\Bus\Dispatcher')->dispatchFrom($job, $source, $extras);
    }
}