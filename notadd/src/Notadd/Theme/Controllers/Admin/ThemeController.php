<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Theme\Controllers\Admin;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Setting\Facades\Setting;
use Notadd\Theme\Facades\Theme;
class ThemeController extends AbstractAdminController {
    /**
     * @return Response
     */
    public function index() {
        $themes = $this->app['theme']->getThemeList();
        $this->share('themes', $themes);
        return $this->view('theme.index');
    }
    /**
     * @param int $id
     * @return Response
     */
    public function show($id) {
    }
    /**
     * @return Response
     */
    public function store() {
    }
    /**
     * @param int $id
     * @return Response
     */
    public function update($id) {
        $themes = $this->app['theme']->getThemeList();
        if($themes->has($id)) {
            if($id != Setting::get('site.theme')) {
                Setting::set('site.theme', $id);
            }
            //Theme::publishAssets($id);
        }
        return Redirect::to('admin/theme');
    }
}