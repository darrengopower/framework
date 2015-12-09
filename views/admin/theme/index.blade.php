@extends('admin::layouts')
@section('title')主题管理@endsection
@section('content')
    <div class="page clearfix">
        <ol class="breadcrumb breadcrumb-small">
            <li>后台首页</li>
            <li class="active"><a href="{{ url('admin/theme')}}">主题管理</a></li>
        </ol>
        <div class="page-wrap">
            <div class="row">
                @foreach($themes as $theme)
                    <div class="col-md-4">
                        <div class="panel panel-lined clearfix mb15">
                            <div class="panel-heading mb20">
                                <strong class="right">[{{ $theme->getAlias() }}]</strong><i>{{ $theme->getTitle() }}</i>
                            </div>
                            <div class="form-horizontal col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <img src="http://img.ithome.com/newsuploadfiles/2015/5/20150514_140710_220.jpg" class="img-responsive">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 text-center">
                                        @if($theme->isDefault())
                                            <form action="{{ url('admin/theme/' . $theme->getAlias()) }}" method="post">
                                                <input type="hidden" name="_method" value="put">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="btn-group">
                                                    <button class="btn btn-info btn-sm">
                                                        <i class="fa fa-check"></i>更新模板缓存
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <form action="{{ url('admin/theme/' . $theme->getAlias()) }}" method="post">
                                                <input type="hidden" name="_method" value="put">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="btn-group">
                                                    <button class="btn btn-info btn-sm">
                                                        <i class="fa fa-circle-o"></i>设为默认主题
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection