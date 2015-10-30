<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 10:58
 */
namespace Notadd\Admin\Controllers;
class AdminController extends AbstractAdminController {
    /**
     * @return \Illuminate\Support\Facades\View
     */
    public function init() {
        $this->share('article_count', 0);
        $this->share('upload_max_filesize', $this->show('upload_max_filesize'));
        $this->share('post_max_size', $this->show('post_max_size'));
        return $this->view('index');
    }
    /**
     * @param $value
     * @return string
     */
    protected function show($value) {
        switch($result = get_cfg_var($value)) {
            case 0:
                return '<font color="red">×</font>';
            case 1:
                return '<font color="green">√</font>';
            default:
                return $result;
        }
    }
}