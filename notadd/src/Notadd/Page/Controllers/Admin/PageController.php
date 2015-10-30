<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 17:19
 */
namespace Notadd\Page\Controllers\Admin;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Notadd\Page\Models\Page;
use Notadd\Page\Requests\PageCreateRequest;
use Notadd\Page\Requests\PageEditRequest;
class PageController extends AbstractAdminController {
    /**
     * @return \Illuminate\Support\Facades\View
     */
    public function create() {
        return $this->view('content.page.create');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id) {
        Request::isMethod('post') && Page::onlyTrashed()->find($id)->forceDelete();
        return Redirect::to('admin/page');
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
        return $this->view('content.page.edit')->withPage(Page::find($id));
    }
    /**
     * @return mixed
     */
    public function index() {
        return $this->view('content.page.index')->withPages(Page::latest()->paginate(30))->withCount(Page::count());
    }
    /**
     * @param $id
     * @return mixed
     */
    public function restore($id) {
        Request::isMethod('post') && Page::onlyTrashed()->find($id)->restore();
        return Redirect::to('admin/page');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function show($id) {
        Session::put('page.id.for.call.create', $id);
        return $this->view('admin.content.page.show')->withPage(Page::findOrFail($id));
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
            return Redirect::to('admin/page');
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
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
            return Redirect::to('admin/page');
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}