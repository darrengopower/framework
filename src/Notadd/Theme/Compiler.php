<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-21 15:55
 */
namespace Notadd\Theme;
use Illuminate\Contracts\Foundation\Application;
use Leafo\ScssPhp\Compiler as SassCompiler;
use Less_Parser as LessCompiler;
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
     * @param \Illuminate\Contracts\Foundation\Application $application
     */
    public function __construct(Application $application) {
        $this->js = '';
        $this->less = new LessCompiler([
            'cache_dir' => $application->storagePath() . DIRECTORY_SEPARATOR . 'less',
            'compress' => true,
            'strictMath' => true
        ]);
        $this->sass = new SassCompiler();
    }
    /**
     * @param string $file
     * @return string
     */
    public function compileJs($file) {
        return '';
    }
    /**
     * @param \Illuminate\Support\Collection $files
     * @return string
     */
    public function compileLess($files) {
        $files->each(function($value) {
            $this->less->parseFile($value);
        });
        return $this->less->getCss();
    }
}