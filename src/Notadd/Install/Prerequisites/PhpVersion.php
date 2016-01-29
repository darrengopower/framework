<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 18:07
 */
namespace Notadd\Install\Prerequisites;
/**
 * Class PhpVersion
 * @package Notadd\Install\Prerequisites
 */
class PhpVersion extends Prerequisite {
    /**
     * @return void
     */
    public function check() {
        if(version_compare(PHP_VERSION, '5.5.9', '<')) {
            $this->errors[] = [
                'message' => 'PHP版本必须高于或等于5.5.9',
                'detail' => '当前PHP版本为' . PHP_VERSION . '。联系您的主机提供商升级到最新版本。'
            ];
        }
    }
}