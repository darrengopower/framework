<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:45
 */
namespace Notadd\Foundation\Database\Eloquent;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Contracts\Queue\EntityResolver as EntityResolverContract;
class QueueEntityResolver implements EntityResolverContract {
    /**
     * @param string $type
     * @param mixed $id
     * @return mixed
     */
    public function resolve($type, $id) {
        $instance = (new $type)->find($id);
        if($instance) {
            return $instance;
        }
        throw new EntityNotFoundException($type, $id);
    }
}