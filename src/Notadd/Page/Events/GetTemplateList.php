<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Page\Events;
use Illuminate\Support\Collection;
/**
 * Class GetTemplateList
 * @package Notadd\Page\Events
 */
class GetTemplateList {
    /**
     * @var Collection
     */
    private $templates;
    /**
     * GetTemplateList constructor.
     * @param \Illuminate\Support\Collection $templates
     */
    public function __construct(Collection $templates) {
        $this->templates = $templates;
    }
    /**
     * @param $key
     * @param $value
     */
    public function register($key, $value) {
        $this->templates->put($key, $value);
    }
}