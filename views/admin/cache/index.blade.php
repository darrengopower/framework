@extends('admin::layouts')
@section('title')缓存管理@endsection
@section('content')
    <div class="page clearfix">
        <ol class="breadcrumb breadcrumb-small">
            <li>后台首页</li>
            <li class="active"><a href="{{ url('admin/cache')}}">缓存管理</a></li>
        </ol>
        <div class="page-wrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-lined clearfix mb30">
                        <div class="panel-heading mb20"><i>缓存管理</i></div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="col-md-3"></td>
                                    <th class="col-md-3">类型</td>
                                    <th class="col-md-3">操作</td>
                                    <th class="col-md-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td>
                                        <strong>清除全局缓存</strong>
                                    </td>
                                    <td>
                                        <form action="{{ url('admin/cache') }}" method="post">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash-o"></i>清除缓存
                                            </button>
                                        </form>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <strong>清除模板缓存</strong>
                                    </td>
                                    <td>
                                        <form action="{{ url('admin/cache/view') }}" method="post">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash-o"></i>清除缓存
                                            </button>
                                        </form>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <strong>清除前端静态资源缓存</strong>
                                    </td>
                                    <td>
                                        <form action="{{ url('admin/cache/static') }}" method="post">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash-o"></i>清除缓存
                                            </button>
                                        </form>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection