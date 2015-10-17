<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-17 20:41
 */
namespace Notadd\Foundation\Extension;
class ExtensionManager {
    protected $config;
    protected $app;
    protected $migrator;
    public function __construct() {
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
    protected function getExtensionsDir() {
        return base_path('../extensions');
    }
}