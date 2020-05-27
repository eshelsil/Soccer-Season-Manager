<!DOCTYPE html>
<html ng-app="app">
    <head>
        <title>2020 - @yield('title')</title>
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
        <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.9/angular.min.js"></script>
        <script src="{{ asset('/js/app.js') }}"></script>
        @section('script')
        @show
    </head>
    <body>
        @csrf
        <div class="text-center container-fluid p-4 bg-primary text-white">
            <p class="h1 m-0">Season 2020</p>
        </div>
        
        @guest
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav m-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <input type="hidden" name="_api_token" value="" autocomplete="off">
        @else
            <input type="hidden" name="_api_token" value="{{ Auth::user()->api_token }}" autocomplete="off">
            <div hidden ng-init="active_user_id = {{ Auth::user()->id }}"></div>
        @endguest
        
        @yield('menu')

        <div class="ml-5 mr-5 mt-3">
            @yield('content')
        </div>
    </body>
</html>