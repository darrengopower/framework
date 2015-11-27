<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 15:13
 */
namespace Notadd\Menu;
use Illuminate\Support\Facades\View;
use Notadd\Menu\Models\Menu;
use Notadd\Menu\Models\MenuGroup;
class Factory {
    /**
     * @param $group_id
     * @return array
     */
    public function build($group_id) {
        $menus = [];
        Menu::buildMenus($group_id, 0, $menus);
        return $menus;
    }
    /**
     * @param $name
     * @param string $template
     * @return mixed
     */
    public function make($name, $template = '') {
        $group = MenuGroup::whereAlias($name)->firstOrFail();
        $menus = $this->build($group->id);
        return View::make($template)->withMenus($menus);
    }
}