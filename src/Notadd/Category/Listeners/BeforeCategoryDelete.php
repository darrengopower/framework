<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-6 16:25:01
 */
namespace Notadd\Category\Listeners;
use Illuminate\Contracts\Events\Dispatcher;
use Notadd\Article\Models\Article;
use Notadd\Category\Events\BeforeCategoryDelete as BeforeCategoryDeleteEvent;
/**
 * Class BeforeCategoryDelete
 * @package Notadd\Category\Listeners
 */
class BeforeCategoryDelete {
    /**
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function subscribe(Dispatcher $dispatcher) {
        $dispatcher->listen(BeforeCategoryDeleteEvent::class, [$this, 'onDelete']);
    }
    /**
     * @param \Notadd\Category\Events\BeforeCategoryDelete $event
     */
    public function onDelete(BeforeCategoryDeleteEvent $event) {
        $category = $event->getCategory();
        Article::whereCategoryId($category->id)->delete();
    }
}