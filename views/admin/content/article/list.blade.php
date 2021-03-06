@extends('admin::layouts')
@section('title')文章管理@endsection
@section('content')
    <div class="page clearfix">
        <ol class="breadcrumb breadcrumb-small">
            <li>后台首页</li>
            <li><a href="{{ url('admin/article') }}">文章管理</a></li>
            @if($crumbs)
                @foreach($crumbs as $crumb)
                    <li><a href="{{ url('admin/article/' . $crumb->id) }}">{{ $crumb->title }}</a></li>
                @endforeach
            @else
                <li><a href="{{ url('admin/article') }}">所有文章</a></li>
            @endif
        </ol>
        <div class="page-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-lined clearfix mb30">
                        <div class="panel-heading mb20"><i>文章管理</i></div>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="col-md-5">文章标题
                                </td>
                                <th class="col-md-2">所属栏目
                                </td>
                                <th class="col-md-2">创建时间
                                </td>
                                <th class="col-md-3">操作
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($articles as $article)
                                <tr>
                                    <td>
                                        <a href="{{ url('article/' . $article->id) }}" target="_blank"><strong>{{ $article->title }}</strong></a>
                                    </td>
                                    <td>
                                        <a href="{{ url('admin/article/' . $article->category->id) }}">{{ $article->category->title }}</a>
                                    </td>
                                    <td>{{ $article->created_at }}</td>
                                    <td>
                                        <form action="{{ URL('admin/article/'.$article->id) }}" method="POST" style="display: inline;">
                                            <input name="_method" type="hidden" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="btn-group">
                                                <a href="{{ url('article/' . $article->id) }}" class="btn btn-primary btn-xs" target="_blank">
                                                    <i class="fa fa-search-plus"></i>查看 </a>
                                                <a href="{{ url('admin/article/'. $article->id . '/edit') }}" class="btn btn-success btn-xs">
                                                    <i class="fa fa-edit"></i>编辑 </a>
                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    <i class="fa fa-trash-o"></i>删除
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="panel-footer clearfix">
                            <nav class="right">
                                @if($category_id > 0)
                                    {!! $articles->appends(['category' => $category_id])->render() !!}
                                @else
                                    {!! $articles->render() !!}
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection