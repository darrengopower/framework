@extends('admin::layouts')
@section('title')创建新页面 - 页面管理@endsection
@section('content')
<div class="page clearfix">
    <ol class="breadcrumb breadcrumb-small">
        <li>后台首页</li>
        <li><a href="{{ url('admin/page') }}">页面管理</a></li>
        <li><a href="{{ url('admin/page/create') }}">创建页面</a></li>
    </ol>
    <div class="page-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-lined clearfix mb30">
                    <div class="panel-heading mb20"><i>创建页面</i></div>
                    <div class="col-md-4 col-md-offset-4 mb5">
                        @if (count($errors) > 0)
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <p><strong>{{ $error }}</strong></p>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    <form class="form-horizontal col-md-12" action="{{ url('admin/page') }}" autocomplete="off" enctype="multipart/form-data" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="col-md-4 control-label">标题</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="title" value="{{ Request::old('title') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">上传缩略图</label>
                            <div class="col-md-4">
                                <span class="btn btn-success btn-file">
                                    <i class="fa fa-image"></i>
                                    <span>上传图片</span>
                                    <input type="file" data-toggle="upload-image" data-target="thumb-image" name="thumb_image">
                                </span>
                                <div id="thumb-image" class="image-preview" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">静态化名称</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="alias" value="{{ Request::old('alias') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">二级域名</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">是否开启</label>
                            <div class="col-md-4">
                                @if (Request::old('enabled'))
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-primary btn-sm active"><input name="enabled" type="radio" value="1" checked>开启</label>
                                    <label class="btn btn-primary btn-sm"><input name="enabled" type="radio" value="0">关闭</label>
                                </div>
                                @else
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-primary btn-sm"><input name="enabled" type="radio" value="1" checked>开启</label>
                                    <label class="btn btn-primary btn-sm active"><input name="enabled" type="radio" value="0">关闭</label>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">标签</label>
                            <div class="col-md-4">
                                <textarea class="form-control" name="keyword" rows="3">{{ Request::old('keyword') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">摘要</label>
                            <div class="col-md-4">
                                <textarea class="form-control" name="description" rows="5">{{ Request::old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">浏览次数</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">创建日期</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">修改日期</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-4">
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
@section('admin-js')
    <script src="{{ asset('themes/admin/js/jquery.uploadPreview.js') }}"></script>
@endsection