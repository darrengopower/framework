<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:19
 */
namespace Notadd\Page\Controllers\Admin;
use Illuminate\Http\Request;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Page\Models\Page;
use Notadd\Page\Requests\PageCreateRequest;
use Notadd\Page\Requests\PageEditRequest;
class PageController extends AbstractAdminController {
    /**
     * @return \Illuminate\Support\Facades\View
     */
    public function create() {
        $page = new Page();
        $this->share('templates', $page->getTemplateList());
        return $this->view('content.page.create');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id, Request $request) {
        $request->isMethod('post') && Page::onlyTrashed()->find($id)->forceDelete();
        return $this->app->make('redirect')->to('admin/page');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $page = Page::find($id);
        $page->delete();
        return $this->app->make('redirect')->back();
    }
    /**
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $page = Page::findOrFail($id);
        $this->share('page', $page);
        $this->share('templates', $page->getTemplateList());
        return $this->view('content.page.edit');
    }
    /**
     * @return mixed
     */
    public function index() {
        $page = Page::whereParentId(0)->orderBy('created_at', 'desc');
        $this->share('count', $page->count());
        $this->share('crumbs', []);
        $this->share('id', 0);
        $this->share('pages', $page->get());
        return $this->view('content.page.list');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function restore($id, Request $request) {
        $request->isMethod('post') && Page::onlyTrashed()->find($id)->restore();
        return $this->app->make('redirect')->to('admin/page');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function show($id) {
        $crumb = [];
        Page::getCrumbMenu($id, $crumb);
        $page = Page::whereParentId($id)->orderBy('created_at', 'desc');
        $this->share('count', $page->count());
        $this->share('crumbs', $crumb);
        $this->share('id', $id);
        $this->share('pages', $page->get());
        return $this->view('content.page.list');
    }
    /**
     * @param PageCreateRequest $request
     * @return mixed
     */
    public function store(PageCreateRequest $request) {
        if($request->input('parent_id')) {
            if(!Page::whereId($request->input('parent_id'))->count()) {
                return $this->app->make('redirect')->back()->withInput()->withErrors('父页面不存在，创建子页面失败！');
            }
        }
        Page::create($request->all());
        return $this->app->make('redirect')->back();
    }
    /**
     * @param PageEditRequest $request
     * @param $id
     * @return mixed
     */
    public function update(PageEditRequest $request, $id) {
        $page = Page::findOrFail($id);
        if($request->hasFile('thumb_image') && $request->file('thumb_image')->isValid()) {
            $file_name = Str::random() . '.' . $request->file('thumb_image')->getClientOriginalExtension();
            $request->file('thumb_image')->move('uploads/pages/thumbs/', $file_name);
            $request->offsetSet('thumb_image', 'uploads/pages/thumbs/' . $file_name);
        }
        $request->files->replace();
        if($page->update($request->all())) {
            return $this->app->make('redirect')->to('admin/page');
        } else {
            return $this->app->make('redirect')->back()->withInput()->withErrors('保存失败！');
        }
    }
}