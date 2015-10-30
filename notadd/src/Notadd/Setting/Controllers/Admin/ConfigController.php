<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 11:21
 */
namespace Notadd\Setting\Controllers\Admin;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Setting\Facades\Setting;
use Notadd\Setting\Requests\SeoRequest;
use Notadd\Setting\Requests\SiteRequest;
class ConfigController extends AbstractAdminController {
    /**
     * @return mixed
     */
    public function getSite() {
        $this->share('title', Setting::get('site.title'));
        $this->share('domain', Setting::get('site.domain'));
        $this->share('beian', Setting::get('site.beian'));
        $this->share('email', Setting::get('site.email'));
        $this->share('statistics', Setting::get('site.statistics'));
        $this->share('copyright', Setting::get('site.copyright'));
        $this->share('company', Setting::get('site.company'));
        $this->share('message', Session::get('message'));
        return $this->view('config.site');
    }
    /**
     * @param SiteRequest $request
     * @return mixed
     */
    public function postSite(SiteRequest $request) {
        Setting::set('site.title', $request->get('title'));
        Setting::set('site.domain', $request->get('domain'));
        Setting::set('site.beian', $request->get('beian'));
        Setting::set('site.email', $request->get('email'));
        Setting::set('site.statistics', $request->get('statistics'));
        Setting::set('site.copyright', $request->get('copyright'));
        Setting::set('site.company', $request->get('company'));
        return Redirect::to('admin/site')->withMessage('更新站点信息成功');
    }
    /**
     * @return mixed
     */
    public function getSeo() {
        $this->share('title', Setting::get('seo.title'));
        $this->share('keyword', Setting::get('seo.keyword'));
        $this->share('description', Setting::get('seo.description'));
        $this->share('message', Session::get('message'));
        return $this->view('config.seo');
    }
    /**
     * @param SeoRequest $request
     * @return mixed
     */
    public function postSeo(SeoRequest $request) {
        Setting::set('seo.title', $request->get('title'));
        Setting::set('seo.keyword', $request->get('keyword'));
        Setting::set('seo.description', $request->get('description'));
        $this->share('message', '更新SEO设置成功');
        return Redirect::to('admin/seo');
    }
}