<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Category\Controllers;
use Notadd\Category\Events\OnCategoryShow;
use Notadd\Category\Models\Category;
use Notadd\Foundation\Routing\Controller;
class CategoryController extends Controller {
    public function index() {
    }
    public function show($id) {
        $category = Category::findOrFail($id);
        $this->app->make('events')->fire(new OnCategoryShow($this->app, $this->view, $category));
        $this->share('category', $category);
        return $this->view($category->getShowTemplate());
    }
}