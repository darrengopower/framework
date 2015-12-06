<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 13:47
 */
namespace Notadd\Foundation\Database\Eloquent;
interface ScopeInterface {
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
    /**
     * @param \Notadd\Foundation\Database\Eloquent\Builder $builder
     * @param \Notadd\Foundation\Database\Eloquent\Model $model
     * @return void
     */
    public function remove(Builder $builder, Model $model);
}