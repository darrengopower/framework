<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-21 15:55
 */
namespace Notadd\Theme;
use Leafo\ScssPhp\Compiler as SassCompiler;
/**
 * Class Compiler
 * @package Notadd\Theme
 */
class Compiler {
    /**
     * @var string
     */
    protected $js;
    /**
     * @var string
     */
    protected $less;
    /**
     * @var string
     */
    protected $sass;
    /**
     * Compiler constructor.
     */
    public function __construct() {
        $this->js = '';
        $this->less = '';
        $this->sass = new SassCompiler();
    }
    /**
     * @param string $file
     * @return string
     */
    public function compileCss($file) {
        return '';
    }
    /**
     * @param string $file
     * @return string
     */
    public function compileJs($file) {
        return '';
    }
    /**
     * @param string $file
     * @return string
     */
    public function compileLess($file) {
        return $this->less->compileFile($file);
    }
    /**
     * @param string $file
     * @return string
     */
    public function compileSass($file) {
        return '';
    }
}