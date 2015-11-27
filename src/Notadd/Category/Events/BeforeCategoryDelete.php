<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-6 16:17:27
 */
namespace Notadd\Category\Events;
use Illuminate\Contracts\Foundation\Application;
use Notadd\Category\Models\Category;
class BeforeCategoryDelete {
    private $app;
    private $category;
    public function __construct(Application $app, Category $category) {
        $this->app = $app;
        $this->category = $category;
    }
    public function getCategory() {
        return $this->category;
    }
}