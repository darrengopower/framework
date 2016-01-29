<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 18:05
 */
namespace Notadd\Install\Prerequisites;
/**
 * Class PhpExtensions
 * @package Notadd\Install\Prerequisites
 */
class PhpExtensions extends Prerequisite {
    /**
     * @return void
     */
    public function check() {
        foreach([
                    'mbstring',
                    'pdo_mysql',
                    'openssl',
                    'json',
                    'gd',
                    'dom',
                ] as $extension) {
            if(!extension_loaded($extension)) {
                $this->errors[] = [
                    'message' => "必须安装PHP扩展[{$extension}]。",
                ];
            }
        }
    }
}