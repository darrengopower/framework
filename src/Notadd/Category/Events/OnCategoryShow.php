<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Category\Events;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Notadd\Category\Models\Category;
/**
 * Class OnCategoryShow
 * @package Notadd\Category\Events
 */
class OnCategoryShow {
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $application;
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private $view;
    /**
     * @var \Notadd\Category\Models\Category
     */
    private $category;
    /**
     * OnCategoryShow constructor.
     * @param \Illuminate\Contracts\Foundation\Application $application
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Notadd\Category\Models\Category $category
     */
    public function __construct(Application $application, Factory $view, Category $category) {
        $this->application = $application;
        $this->view = $view;
        $this->category = $category;
    }
    /**
     * @return \Notadd\Category\Models\Category
     */
    public function getCategory() {
        return $this->category;
    }
    /**
     * @param $template
     */
    public function setCategoryShowTemplate($template) {
        $this->category->setShowTemplate($template);
    }
    /**
     * @param $key
     * @param $value
     */
    public function share($key, $value) {
        $this->view->share($key, $value);
    }
}