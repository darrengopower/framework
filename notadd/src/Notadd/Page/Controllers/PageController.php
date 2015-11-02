<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Page\Controllers;
use Notadd\Foundation\Routing\Controller;
use Notadd\Page\Models\Page;
class PageController extends Controller {
    public function show($id) {
        $page = Page::findOrFail($id);
        return $this->view($page->template);
    }
}