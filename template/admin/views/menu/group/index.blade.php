@extends('admin::layouts')
@section('title')菜单管理@endsection
@section('content')
<div class="page clearfix">
    <ol class="breadcrumb breadcrumb-small">
        <li>后台首页</li>
        <li class="active"><a href="{{ url('admin/menu')}}">菜单管理</a></li>
    </ol>
    <div class="page-wrap">
        <div class="row">
            @foreach($groups as $group)
            <div class="col-md-4">
                <div class="panel panel-lined clearfix mb15">
                    <div class="panel-heading mb20"><strong class="right">[{{ $group->alias }}]</strong><i>{{ $group->title }}</i></div>
                    <div class="form-horizontal col-md-12">
                        <div class="form-group">
                            <div class="col-md-12 text-center">
                                <form action="{{ url('admin/menu/' . $group->id) }}" method="post">
                                    <input name="_method" type="hidden" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="btn-group">
                                        <a class="btn btn-primary btn-sm" href="{{ url('admin/menu/' . $group->id) }}">
                                            <i class="fa fa-search-plus"></i>菜单管理
                                        </a>
                                        <a class="btn btn-success btn-sm" href="{{ url('admin/menu/' . $group->id . '/edit') }}">
                                            <i class="fa fa-edit"></i>编辑分组
                                        </a>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash-o"></i>删除分组
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
                @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p><strong>{{ $error }}</strong></p>
                </div>
                @endforeach
                @endif
                <div class="panel panel-lined clearfix mb15">
                    <div class="panel-heading mb20"><span class="right">共有{{ $count }}个菜单分组</span><i><strong>创建新分组</strong></i></div>
                    <form class="form-horizontal col-md-12" action="{{ url('admin/menu') }}" autocomplete="off" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="col-md-4 control-label">分组名称</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control"  name="title" placeholder="输入分组名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">分组别名</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="alias" placeholder="输入分组别名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-8">
                                <button class="btn btn-primary right" type="submit">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection