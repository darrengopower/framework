<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 18:08
 */
namespace Notadd\Install\Prerequisites;
class WritablePaths extends Prerequisite {
    /**
     * @return void
     */
    public function check() {
        $paths = [
            public_path(),
            public_path('assets'),
            base_path('../extensions'),
            storage_path()
        ];
        foreach($paths as $path) {
            if(!is_writable($path)) {
                $this->errors[] = [
                    'message' => '目录[' . realpath($path) . ']不可写。',
                    'detail' => '请将该目录' . ($path !== public_path() ? '及其子目录或内容' : '') . '的读写权限设置为0775。'
                ];
            }
        }
    }
}