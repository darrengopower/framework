<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-01 00:05
 */
namespace Notadd\Category\Events;
use Illuminate\Support\Collection;
use Notadd\Category\Models\Category;
class GetCategoryAdminTemplates {
    /**
     * @var Category
     */
    private $category;
    /**
     * @var Collection
     */
    private $templates;
    /**
     * @param Category $category
     * @param Collection $templates
     */
    public function __construct(Category $category, Collection $templates) {
        $this->category = $category;
        $this->templates = $templates;
    }
    /**
     * @param $key
     * @param $value
     */
    public function register($key, $value) {
        $this->templates->put($key, $value);
    }
}