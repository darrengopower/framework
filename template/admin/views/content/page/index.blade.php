@extends('admin::layouts')
@section('title')页面管理@endsection
@section('content')
<div class="page clearfix">
    <ol class="breadcrumb breadcrumb-small">
        <li>后台首页</li>
        <li><a href="{{ url('admin/page') }}">页面管理</a></li>
    </ol>
    <div class="page-wrap">
        <div class="row">
            @foreach ($pages as $page)
            <div class="col-md-4">
                <div class="panel panel-lined clearfix mb15">
                    <div class="panel-heading mb20">
                        <strong class="right">[{{ $page->alias }}]</strong>
                        <i>{{ $page->title }}</i>
                    </div>
                    <div class="form-horizontal col-md-12">
                        <div class="form-group">
                            <div class="col-md-12">
                                <img src="{{ isset($page->thumb_image) ? asset($page->thumb_image) : 'http://img.ithome.com/newsuploadfiles/2015/5/20150514_140710_220.jpg' }}" class="img-responsive">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 text-center">
                                <form action="{{ URL('admin/page/'.$page->id) }}" method="post">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="btn-group">
                                        <!--<a class="btn btn-primary btn-sm" href="{{ url('admin/page/' . $page->id) }}">
                                            <i class="fa fa-search-plus"></i>查看
                                        </a>-->
                                        <a class="btn btn-success btn-sm" href="{{ url('admin/page/' . $page->id . '/edit') }}">
                                            <i class="fa fa-edit"></i>编辑
                                        </a>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash-o"></i>删除
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-lined clearfix mb15">
                    <div class="panel-heading mb20"><span class="right">共有{{ $count }}个菜单分组</span><i><strong>创建新页面</strong></i></div>
                    <div class="form-horizontal col-md-12">
                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-8">
                                <a href="{{ url('admin/page/create') }}" class="btn btn-primary right">添加新页面</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection