<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:12
 */
namespace Notadd\Article\Controllers\Admin;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Article\Models\Article;
use Notadd\Article\Models\ArticleRecommend;
use Notadd\Category\Models\Category;
class ArticleController extends AbstractAdminController {
    /**
     * @var Article
     */
    public $article;
    /**
     * @var Category
     */
    public $category;
    public $request;
    /**
     * @var Collection
     */
    public $templates;
    public function __construct(Factory $view, Request $request) {
        parent::__construct($view, $request);
        $this->templates = Collection::make();
        $this->templates->put('create', 'admin::content.article.create');
        $this->templates->put('edit', 'admin::content.article.edit');
        $this->templates->put('list', 'admin::content.article.list');
    }
    /**
     * @return \Illuminate\Support\Facades\View
     */
    public function create() {
        $category_id = Input::get('category');
        if(Category::whereEnabled(true)->whereId($category_id)->count()) {
            $this->category = Category::whereEnabled(true)->whereId($category_id)->firstOrFail();
            $this->fireEvent('before.create', false);
            $this->share('category', $this->category);
            return $this->view($this->templates->get('create'));
        } else {
            return Redirect::to('admin/category');
        }
    }
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id, Request $request) {
        $request->isMethod('post') && Article::onlyTrashed()->find($id)->forceDelete();
        return Redirect::to('admin/article');
    }
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id) {
        $article = Article::find($id);
        $article->delete();
        ArticleRecommend::whereArticleId($id)->delete();
        return Redirect::to('admin/article');
    }
    /**
     * @param $id
     * @return \Illuminate\Support\Facades\View
     */
    public function edit($id) {
        $this->article = Article::findOrFail($id);
        $this->category = Category::findOrFail($this->article->category_id);
        $this->fireEvent('before.edit', false);
        if($this->category->type == 'western.information') {
            $recommends = Config::get('page.recommends');
            $recommendeds = ArticleRecommend::whereArticleId($id)->lists('position');
            foreach($recommends as $key => $value) {
                $recommends[$key]['has'] = $recommendeds->contains($key);
            }
            $this->share('recommends', $recommends);
        }
        $this->share('article', $this->article);
        $this->share('category', $this->category);
        return $this->view($this->templates->get('edit'));
    }
    /**
     * @return mixed
     */
    public function index() {
        $articles = Article::with('category')->latest()->paginate(30);
        $articles->setPath(App::make('request')->url());
        $this->share('articles', $articles);
        $this->share('category_id', 0);
        $this->share('crumbs', []);
        $this->share('count', Article::count());
        return $this->view($this->templates->get('list'));
    }
    /**
     * @param $id
     * @return mixed
     */
    public function restore($id, Request $request) {
        $request->isMethod('post') && Article::onlyTrashed()->find($id)->restore();
        return Redirect::to('admin/article');
    }
    public function show($id) {
        $crumb = [];
        Category::buildCrumb($id, $crumb);
        $articles = Article::with('category')->whereCategoryId($id)->orderBy('created_at', 'desc')->paginate(30);
        $articles->setPath(App::make('request')->url());
        $this->share('articles', $articles);
        $this->share('category_id', $id);
        $this->share('crumbs', $crumb);
        $this->share('count', Article::count());
        return $this->view($this->templates->get('list'));
    }
    /**
     * @param ArticleCreateRequest $request
     * @return mixed
     */
    public function store(ArticleCreateRequest $request) {
        $this->article = new Article();
        $this->category = Category::findOrFail($request->get('category_id'));
        $this->request = $request;
        $this->request->offsetSet('user_id', Auth::user()->id);
        $this->request->offsetSet('created_at', new Carbon());
        $this->fireEvent('on.create', false);
        $this->article = $this->article->create($this->request->all());
        $this->fireEvent('after.create', false);
        return Redirect::to('admin/article');
    }
    /**
     * @param ArticleEditRequest $request
     * @param $id
     * @return mixed
     */
    public function update(ArticleEditRequest $request, $id) {
        $this->article = Article::findOrFail($id);
        $this->category = Category::findOrFail($this->article->category_id);
        $this->request = $request;
        $this->request->offsetSet('user_id', Auth::user()->id);
        $this->request->offsetSet('created_at', new Carbon($this->request->offsetGet('created_at')));
        $this->fireEvent('on.edit', false);
        if($this->article->update($request->all())) {
            $this->fireEvent('after.edit', false);
            return Redirect::to('admin/article');
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}