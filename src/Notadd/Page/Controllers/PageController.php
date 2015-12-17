<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Page\Controllers;
use Notadd\Foundation\Routing\Controller;
use Notadd\Page\Events\OnPageShow;
use Notadd\Page\Page;
class PageController extends Controller {
    public function show($id) {
        $page = new Page($id);
        $this->events->fire(new OnPageShow($this->app, $this->view, $page));
        $template = $page->getTemplate();
        $template || $template = 'default::page.default';
        $this->view->exists($template) || $template = 'default::page.default';
        if($this->setting->get('site.home') !== 'page_' . $id) {
            $this->seo->setTitleMeta($page->getTitle() . ' - {sitename}');
        }
        $this->seo->setDescriptionMeta($page->getDescription());
        $this->seo->setKeywordsMeta($page->getKeywords());
        $this->share('content', $page->getContent());
        $this->share('logo', file_get_contents(realpath($this->app->frameworkPath() . '/views/install') . DIRECTORY_SEPARATOR . 'logo.svg'));
        $this->share('page', $page);
        $this->share('title', $page->getTitle());
        $this->share('subPages', $page->getSubPages());
        return $this->view($template);
    }
}