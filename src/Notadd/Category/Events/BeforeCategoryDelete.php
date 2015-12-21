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
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $application;
    /**
     * @var \Notadd\Category\Models\Category
     */
    private $category;
    /**
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Notadd\Category\Models\Category $category
     */
    public function __construct(Application $application, Category $category) {
        $this->application = $application;
        $this->category = $category;
    }
    /**
     * @return \Notadd\Category\Models\Category
     */
    public function getCategory() {
        return $this->category;
    }
}