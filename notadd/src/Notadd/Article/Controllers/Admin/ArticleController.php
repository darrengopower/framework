<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-30 16:12
 */
namespace Notadd\Article\Controllers\Admin;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Notadd\Admin\Controllers\AbstractAdminController;
use Notadd\Article\Models\Article;
use Notadd\Article\Models\ArticleRecommend;
use Notadd\Article\Requests\ArticleCreateRequest;
use Notadd\Article\Requests\ArticleEditRequest;
use Notadd\Category\Models\Category;
class ArticleController extends AbstractAdminController {
    /**
     * @return \Illuminate\Support\Facades\View
     */
    public function create(Request $request) {
        if(Category::whereEnabled(true)->whereId($request->input('category'))->count()) {
            $category = Category::whereEnabled(true)->whereId($request->input('category'))->firstOrFail();
            $this->share('category', $category);
            return $this->view($category->getArticleTemplate('create'));
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
        $article = Article::findOrFail($id);
        $category = Category::findOrFail($article->category_id);
        $this->share('article', $article);
        $this->share('category', $category);
        return $this->view($category->getArticleTemplate('edit'));
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
        return $this->view('admin::content.article.list');
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
        return $this->view('admin::content.article.list');
    }
    /**
     * @param ArticleCreateRequest $request
     * @return mixed
     */
    public function store(ArticleCreateRequest $request) {
        $article = new Article();
        $request->offsetSet('user_id', Auth::user()->id);
        $request->offsetSet('created_at', new Carbon());
        $article->create($request->all());
        return Redirect::to('admin/article');
    }
    /**
     * @param ArticleEditRequest $request
     * @param $id
     * @return mixed
     */
    public function update(ArticleEditRequest $request, $id) {
        $article = Article::findOrFail($id);
        $request->offsetSet('user_id', Auth::user()->id);
        $request->offsetSet('created_at', new Carbon($request->offsetGet('created_at')));
        if($article->update($request->all())) {
            return Redirect::to('admin/article');
        } else {
            return Redirect::back()->withInput()->withErrors('保存失败！');
        }
    }
}