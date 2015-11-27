<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 20:41
 */
namespace Notadd\Foundation\Extension;
use Illuminate\Contracts\Foundation\Application;
class ExtensionManager {
    protected $app;
    protected $config;
    protected $migrator;
    public function __construct(Application $app) {
        $this->app = $app;
    }
    public function getInfo() {
    }
    public function enable($extension) {
    }
    public function disable($extension) {
    }
    public function uninstall($extension) {
    }
    public function migrate($extension, $up = true) {
    }
    public function getMigrator() {
        return $this->migrator;
    }
    protected function getEnabled() {
    }
    protected function setEnabled(array $enabled) {
    }
    public function isEnabled($extension) {
    }
    protected function load($extension) {
    }
    public function getExtensionsDir() {
        return realpath(base_path('../extensions'));
    }
}