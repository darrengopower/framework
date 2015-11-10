<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Category\Controllers;
use Notadd\Category\Category;
use Notadd\Category\Events\OnCategoryShow;
use Notadd\Foundation\Routing\Controller;
class CategoryController extends Controller {
    public function index() {
    }
    public function show($id) {
        $category = new Category($id);
        $this->app->make('events')->fire(new OnCategoryShow($this->app, $this->view, $category->getModel()));
        $this->share('category', $category->getModel());
        $this->share('name', $category->getName());
        $this->share('list', $category->getList());
        $this->share('relations', $category->getRelationCategoryList());
        return $this->view($category->getShowTemplate());
    }
}