<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:17
 */
namespace Notadd\Menu\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Menu\Models\Menu;
use Notadd\Menu\Models\MenuGroup;
use Notadd\Menu\Requests\MenuGroupCreateRequest;
use Notadd\Menu\Requests\MenuGroupEditRequest;
class GroupController extends AbstractAdminController {
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $group = MenuGroup::find($id);
        $group->delete();
        return Redirect::to('admin/menu');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $this->share('group', MenuGroup::findOrFail($id));
        return $this->view('menu.group.edit');
    }
    /**
     * @return mixed
     */
    public function index() {
        $groups = MenuGroup::all();
        $this->share('count', $groups->count());
        $this->share('groups', $groups);
        return $this->view('menu.group.index');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function show($id) {
        $items = Menu::whereParentId(0)->whereGroupId($id)->orderBy('order_id')->get();
        $this->share('count', $items->count());
        $this->share('group', MenuGroup::find($id));
        $this->share('items', $items);
        $this->share('parent_id', 0);
        return $this->view('menu.group.show');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function sort($id) {
        $items = Menu::whereParentId(0)->whereGroupId($id)->orderBy('order_id')->get();
        $this->share('group', MenuGroup::find($id));
        $this->share('items', $items);
        return $this->view('menu.group.sort');
    }
    /**
     * @param                        $id
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function sorting($id, Request $request) {
        if(is_array($request->get('order')) && $request->get('order')) {
            foreach($request->get('order') as $key=>$value) {
                if(Menu::whereGroupId($id)->whereId($key)->count()) {
                    $menu = Menu::find($key);
                    $menu->update(['order_id' => $value]);
                }
            }
        }
        return Redirect::back();
    }
    /**
     * @param MenuGroupCreateRequest $request
     * @return mixed
     */
    public function store(MenuGroupCreateRequest $request) {
        $group = new MenuGroup();
        if($group->create($request->all())) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
    /**
     * @param MenuGroupEditRequest $request
     * @param $id
     * @return mixed
     */
    public function update(MenuGroupEditRequest $request, $id) {
        $group = MenuGroup::findOrFail($id);
        if($group->update($request->all())) {
            return Redirect::to("admin/menu");
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}