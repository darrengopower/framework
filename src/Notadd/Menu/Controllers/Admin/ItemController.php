<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:22
 */
namespace Notadd\Menu\Controllers\Admin;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Menu\Models\Menu;
use Notadd\Menu\Models\MenuGroup;
use Notadd\Menu\Requests\MenuCreateRequest;
class ItemController extends AbstractAdminController {
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return Redirect::back();
    }
    /**
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $crumb = [];
        $item = Menu::find($id);
        Menu::buildCrumb($item->parent_id, $crumb);
        $this->share('item', $item);
        $this->share('crumbs', $crumb);
        return $this->view('menu.item.edit');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function show($id) {
        $crumb = [];
        Menu::buildCrumb($id, $crumb);
        $menu = Menu::findOrFail($id);
        $items = Menu::whereParentId($id)->whereGroupId($menu->group_id)->orderBy('order_id')->get();
        $this->share('count', $items->count());
        $this->share('crumbs', $crumb);
        $this->share('group', MenuGroup::find($menu->group_id));
        $this->share('items', $items);
        $this->share('parent_id', $id);
        return $this->view('menu.item.show');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function status($id) {
        $item = Menu::findOrFail($id);
        $item->update(['enabled' => !$item->enabled]);
        return Redirect::back();
    }
    /**
     * @param $id
     * @return mixed
     */
    public function sort($id) {
        $crumb = [];
        Menu::buildCrumb($id, $crumb);
        $parent = Menu::findOrFail($id);
        $items = Menu::whereParentId($id)->orderBy('order_id')->get();
        $this->share('group', MenuGroup::find($parent->group_id));
        $this->share('crumbs', $crumb);
        $this->share('items', $items);
        $this->share('parent', $parent);
        return $this->view('menu.item.sort');
    }
    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function sorting($id, Request $request) {
        if(is_array($request->get('order')) && $request->get('order')) {
            foreach($request->get('order') as $key => $value) {
                if(Menu::whereParentId($id)->whereId($key)->count()) {
                    $menu = Menu::find($key);
                    $menu->update(['order_id' => $value]);
                }
            }
        }
        return Redirect::back();
    }
    /**
     * @param MenuCreateRequest $request
     * @return mixed
     */
    public function store(MenuCreateRequest $request) {
        $menu = new Menu();
        if($menu->create($request->all())) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
    /**
     * @param MenuCreateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(MenuCreateRequest $request, $id) {
        $menu = Menu::findOrFail($id);
        if($request->hasFile('icon_image') && $request->file('icon_image')->isValid()) {
            $file_name = Str::random() . '.' . $request->file('icon_image')->getClientOriginalExtension();
            $request->file('icon_image')->move('uploads/menus/', $file_name);
            $request->offsetSet('icon_image', 'uploads/menus/' . $file_name);
        }
        $request->files->replace();
        if($menu->update($request->all())) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}