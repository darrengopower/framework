<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Theme\Controllers\Admin;
use Notadd\Admin\Controllers\AbstractAdminController;
class ThemeController extends AbstractAdminController {
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index() {
        $themes = $this->app->make('theme')->getThemeList();
        $this->share('themes', $themes);
        return $this->view('theme.index');
    }
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id) {
        $themes = $this->app->make('theme')->getThemeList();
        if($themes->has($id)) {
            if($id != $this->app->make('setting')->get('site.theme')) {
                $this->app->make('setting')->set('site.theme', $id);
            }
            $this->app->make('theme')->publishAssets();
        }
        return $this->redirect->to('admin/theme');
    }
}