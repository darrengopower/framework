<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:48
 */
namespace Notadd\Category\Controllers\Admin;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Category\Models\Category;
use Notadd\Category\Requests\CategoryCreateRequest;
use Notadd\Category\Requests\CategoryEditRequest;
class CategoryController extends AbstractAdminController {
    /**
     * 当前Category模型实例
     * @var \Notadd\Category\Models\Category
     */
    public $category;
    /**
     * @var
     */
    public $request;
    /**
     * @var array
     */
    public $templates;
    /**
     * @var array
     */
    public $types;
    /**
     * 构造函数
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Factory $view, Request $request) {
        parent::__construct($view, $request);
        $this->types = Collection::make();
        $this->fireEvent('on.init', false);
    }
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $category = Category::findOrFail($id);
        $category->delete();
        return Redirect::back();
    }
    /**
     * @param $id
     * @return \Illuminate\Support\Facades\View
     */
    public function edit($id) {
        $service = new Service($id);
        $this->category = $service->getCategory();
        $crumbs = [];
        Category::buildCrumb($this->category->parent_id, $crumbs);
        $this->fireEvent('before.edit', false);
        $this->share('category', $this->category);
        $this->share('crumbs', $crumbs);
        $this->share('types', $service->getType());
        return $this->view($service->getTemplate()->get('edit'));
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
        $crumb = [];
        Category::buildCrumb($id, $crumb);
        $categories = Category::whereParentId($id)->get();
        $this->share('categories', $categories);
        $this->share('count', $categories->count());
        $this->share('crumbs', []);
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
        $this->category = Category::findOrFail($id);
        $this->request = $request;
        $this->fireEvent('on.edit', false);
        if($this->category->update($this->request->all())) {
            $this->fireEvent('after.edit', false);
            return Redirect::back();
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}