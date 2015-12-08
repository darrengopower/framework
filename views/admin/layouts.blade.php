<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>@yield('title') - 后台 - {{ Setting::get('site.company', 'iBenchu CMS')  }}内容管理系统</title>
        <meta name="author" content="iBenchu.net">
        <meta name="keywords" content="iBenchu">
        <meta name="description" content="iBenchu CMS内容管理系统">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800">
        <link rel="stylesheet" href="{{ asset('themes/admin/css/font-awesome.css') }}">
        <link rel="stylesheet" href="{{ asset('themes/admin/css/bootstrap.min.css') }}">
        @yield('admin-css')
        <link rel="stylesheet" href="{{ asset('themes/admin/css/main.css') }}">
        <script src="{{ asset('themes/admin/js/matchMedia.js') }}"></script>
    </head>
    <body class="app {{ $admin_theme }}">
        <header class="site-head clearfix" id="site-head">
            <div class="nav-head">
                <a href="{{ url('admin') }}" class="site-logo"><span>{{ Setting::get('site.company', 'iBenchu CMS')  }}</span>&nbsp;内容管理系统</a>
                <span class="nav-trigger fa fa-outdent hidden-xs" data-toggle="nav-min"></span>
                <span class="nav-trigger fa fa-navicon visible-xs" data-toggle="off-canvas"></span>
            </div>
            <div class="head-wrap clearfix">
                <ul class="list-unstyled navbar-right">
                    <li>
                        <a href="{{ url() }}" target="_blank">
                            <i class="fa fa-external-link"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="sidebar">
                            <i class="fa fa-tasks"></i>
                        </a>
                        <div class="floating-sidebar">
                            <div class="ongoing-tasks">
                                <h3 class="small title mb30">Ongoing Tasks</h3>
                                <ul class="list-unstyled mb15 clearfix">
                                    <li>
                                        <div class="clearfix mb10">
                                            <small class="left">App Upload</small>
                                            <small class="right">80%</small>
                                        </div>
                                    <progressbar value="80" class="progress-xxs" type="success"></progressbar>
                                    </li>
                                    <li>
                                        <div class="clearfix mb10">
                                            <small class="left">Creating Assets</small>
                                            <small class="right">50%</small>
                                        </div>
                                    <progressbar value="50" class="progress-xxs" type="danger"></progressbar>
                                    </li>
                                    <li>
                                        <div class="clearfix mb10">
                                            <small class="left">New UI 2.0</small>
                                            <small class="right">90%</small>
                                        </div>
                                    <progressbar value="90" class="progress-xxs" type="warning"></progressbar>
                                    </li>
                                </ul>
                            </div>
                            <div class="stats">
                                <h3 class="small title mb15">Transaction</h3>
                                <ul class="list-unstyled clearfix mb15">
                                    <li class="clearfix">
                                        <i class="fa fa-paypal left bg-primary"></i>
                                        <div class="info">
                                            <strong>Send to Elli at 4:00 pm</strong>
                                            <span>$3000</span>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <i class="fa fa-bitcoin left bg-warning"></i>
                                        <div class="info">
                                            <strong>Received from Salman at 12:00 pm</strong>
                                            <span>B 35000</span>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <i class="fa fa-gittip left bg-info"></i>
                                        <div class="info">
                                            <strong>Donate to gittip</strong>
                                            <span>$500</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" dropdown>
                        <a href class="user-profile dropdown-toggle" dropdown-toggle>
                            <img src="{{ asset('uploads/image/20150513/1431511494149062.jpg') }}" alt="admin-pic">
                        </a>
                        <div class="panel panel-default dropdown-menu">
                            <div class="panel-body">
                                <figure class="photo left">
                                    <img src="{{ asset('uploads/image/20150513/1431511494149062.jpg') }}" alt="admin-pic">
                                    <a href="j:;">Photo</a>
                                </figure>
                                <div class="profile-info right">
                                    <p class="user-name">Bryan R.</p>
                                    <p>bryan.r@gmail.com</p>
                                    <a href="j:;" class="btn btn-info btn-xs">See Profile</a>
                                </div>
                            </div>
                            <div class="panel-footer clearfix">
                                <a href="j:;" class="btn btn-default btn-sm left">Account</a>
                                <a href="#/pages/lock-screen" class="btn btn-info btn-sm right">Sign Out</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </header>
        <div class="main-container clearfix">
            <aside class="nav-wrap" id="site-nav" data-toggle="scrollbar">
                <div class="form-search">
                    <form action="{{ url('admin/search') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="search" class="form-control" name="keyword" placeholder="输入搜索关键词">
                        <button type="submit" class="fa fa-search"></button>
                    </form>
                </div>
                <nav class="site-nav clearfix" role="navigation" data-toggle="nav-accordion">
                    @foreach(Config::get('admin') as $top)
                    <div class="nav-title panel-heading"><i>{{ $top['title'] }}</i></div>
                    @if($top['sub'])
                    <ul class="list-unstyled nav-list">
                        @foreach($top['sub'] as $one)
                        @if(isset($one['sub']))
                        <li class="{{ $one['active'] }}">
                            <a href="javascript:;"><i class="fa {{ $one['icon'] }} icon"></i><span class="text">{{ $one['title'] }}</span><i class="arrow fa fa-angle-right right"></i></a>
                            <ul class="inner-drop list-unstyled">
                                @foreach($one['sub'] as $two)
                                    <li class="{{ (Request::is($two['active']) ? 'active' : '') }}"><a href="{{ url($two['url']) }}">{{ $two['title'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                        <li class="{{ $one['active'] }}">
                            <a href="{{ url($one['url']) }}"><i class="fa {{ $one['icon'] }} icon"></i><span class="text">{{ $one['title'] }}</span></a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    @endif
                    @endforeach
                </nav>
                <div class="theme-settings clearfix">
                    <div class="panel-heading"><i>主题切换</i></div>
                    <ul class="list-unstyled clearfix">
                        @if($admin_theme == 'theme-zero')
                        <li class="active"><a href="javascript:;" data-toggle="theme" data-theme="theme-zero"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @else
                        <li><a href="javascript:;" data-toggle="theme" data-theme="theme-zero"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @endif
                        @if($admin_theme == 'theme-one')
                        <li class="active"><a href="javascript:;" data-toggle="theme" data-theme="theme-one"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @else
                        <li><a href="javascript:;" data-toggle="theme" data-theme="theme-one"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @endif
                        @if($admin_theme == 'theme-two')
                        <li class="active"><a href="javascript:;" data-toggle="theme" data-theme="theme-two"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @else
                        <li><a href="javascript:;" data-toggle="theme" data-theme="theme-two"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @endif
                        @if($admin_theme == 'theme-three')
                        <li class="active"><a href="javascript:;" data-toggle="theme" data-theme="theme-three"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @else
                        <li><a href="javascript:;" data-toggle="theme" data-theme="theme-three"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @endif
                        @if($admin_theme == 'theme-four')
                        <li class="active"><a href="javascript:;" data-toggle="theme" data-theme="theme-four"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @else
                        <li><a href="javascript:;" data-toggle="theme" data-theme="theme-four"><span class="side-top"></span><span class="header"></span><span class="side-rest"></span></a></li>
                        @endif
                    </ul>
                </div>
            </aside>
            <div class="content-container" id="content">@yield('content')</div>
            <footer id="site-foot" class="site-foot clearfix">
                <p class="left">&copy; Copyright 2015 <strong>iBenchu.org</strong>, All rights reserved.</p>
                <p class="right">{{ Config::get('app.version') }}</p>
            </footer>
        </div>
        <script src="{{ asset('themes/admin/js/jquery-2.1.3.min.js') }}"></script>
        <script src="{{ asset('themes/admin/js/perfect-scrollbar.jquery.min.js') }}"></script>
        <script src="{{ asset('themes/admin/js/bootstrap.min.js') }}"></script>
        @yield('admin-js')
        <script src="{{ asset('themes/admin/js/app.js') }}"></script>
    </body>
</html>