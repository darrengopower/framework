<?php
/**
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.com
 */
namespace Notadd\Category\Events;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\View\Factory;
use Notadd\Category\Models\Category;
class OnCategoryShow {
    private $app;
    private $view;
    private $category;
    public function __construct(Application $app, Factory $view, Category $category) {
        $this->app = $app;
        $this->view = $view;
        $this->category = $category;
    }
    public function getCategory() {
        return $this->category;
    }
    public function setCategoryShowTemplate($template) {
        $this->category->setShowTemplate($template);
    }
    public function share($key, $value) {
        $this->view->share($key, $value);
    }
}