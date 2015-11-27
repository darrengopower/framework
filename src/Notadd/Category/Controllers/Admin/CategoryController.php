<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:48
 */
namespace Notadd\Category\Controllers\Admin;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Category\Events\BeforeCategoryDelete;
use Notadd\Category\Models\Category;
use Notadd\Category\Requests\CategoryCreateRequest;
use Notadd\Category\Requests\CategoryEditRequest;
class CategoryController extends AbstractAdminController {
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $category = Category::findOrFail($id);
        $this->app->make('events')->fire(new BeforeCategoryDelete($this->app, $category));
        $category->delete();
        return Redirect::back();
    }
    /**
     * @param $id
     * @return \Illuminate\Support\Facades\View
     */
    public function edit($id) {
        $category = Category::findOrFail($id);
        $crumbs = [];
        Category::buildCrumb($category->parent_id, $crumbs);
        $this->share('category', $category);
        $this->share('crumbs', $crumbs);
        $this->share('types', $category->getTypes());
        return $this->view($category->getAdminTemplate()->get('edit'));
    }
    /**
     * @return mixed
     */
    public function index() {
        $categories = Category::whereParentId('0')->get();
        $this->share('categories', $categories);
        $this->share('count', $categories->count());
        $this->share('crumbs', []);
        $this->share('id', 0);
        return $this->view('content.category.show');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function show($id) {
        $crumbs = [];
        Category::buildCrumb($id, $crumbs);
        $categories = Category::whereParentId($id)->get();
        $this->share('categories', $categories);
        $this->share('count', $categories->count());
        $this->share('crumbs', $crumbs);
        $this->share('id', $id);
        return $this->view('content.category.show');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function status($id) {
        $category = Category::findOrFail($id);
        $category->update(['enabled' => !$category->enabled]);
        return Redirect::back();
    }
    /**
     * @param CategoryCreateRequest $request
     * @return mixed
     */
    public function store(CategoryCreateRequest $request) {
        $category = new Category();
        if($category->create($request->all())) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
    /**
     * @param CategoryEditRequest $request
     * @param $id
     * @return mixed
     */
    public function update(CategoryEditRequest $request, $id) {
        $category = Category::findOrFail($id);
        if($category->update($request->all())) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}