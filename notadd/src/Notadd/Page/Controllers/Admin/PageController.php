<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:19
 */
namespace Notadd\Page\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
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
        return $this->app['redirect']->to('admin/page');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $page = Page::find($id);
        $page->delete();
        return Redirect::to('admin/page');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $this->share('page', Page::find($id));
        return $this->view('content.page.edit');
    }
    /**
     * @return mixed
     */
    public function index() {
        $this->share('pages', Page::latest()->paginate(30));
        $this->share('count', Page::count());
        return $this->view('content.page.index');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function restore($id, Request $request) {
        $request->isMethod('post') && Page::onlyTrashed()->find($id)->restore();
        return $this->app['redirect']->to('admin/page');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function show($id) {
        $this->app['session']->put('page.id.for.call.create', $id);
        $this->share('page', Page::findOrFail($id));
        return $this->view('admin.content.page.show');
    }
    /**
     * @param PageCreateRequest $request
     * @return mixed
     */
    public function store(PageCreateRequest $request) {
        $page = new Page();
        if($request->hasFile('thumb_image') && $request->file('thumb_image')->isValid()) {
            $file_name = Str::random() . '.' . $request->file('thumb_image')->getClientOriginalExtension();
            $request->file('thumb_image')->move('uploads/pages/thumbs/', $file_name);
            $request->offsetSet('thumb_image', 'uploads/pages/thumbs/' . $file_name);
        }
        $request->files->replace();
        if($page->create($request->all())) {
            return $this->app['redirect']->to('admin/page');
        } else {
            return $this->app['redirect']->back()->withInput()->withErrors('保存失败！');
        }
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
            return $this->app['redirect']->to('admin/page');
        } else {
            return $this->app['redirect']->back()->withInput()->withErrors('保存失败！');
        }
    }
}