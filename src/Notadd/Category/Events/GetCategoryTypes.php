<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-31 23:50
 */
namespace Notadd\Category\Events;
use Illuminate\Support\Collection;
use Notadd\Category\Models\Category;
/**
 * Class GetCategoryTypes
 * @package Notadd\Category\Events
 */
class GetCategoryTypes {
    /**
     * @var \Notadd\Category\Models\Category
     */
    private $category;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $types;
    /**
     * GetCategoryTypes constructor.
     * @param \Notadd\Category\Models\Category $category
     * @param \Illuminate\Support\Collection $types
     */
    public function __construct(Category $category, Collection $types) {
        $this->category = $category;
        $this->types = $types;
    }
    /**
     * @param $key
     * @param $value
     */
    public function register($key, $value) {
        $this->types->put($key, $value);
    }
}