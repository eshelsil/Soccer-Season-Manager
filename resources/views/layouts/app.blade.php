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
        
        @yield('menu')

        <div class="ml-5 mr-5 mt-3">
        {{-- <div class="ml-5 mr-5 mt-3" ng-controller="@yield('controller')"> --}}
            @yield('content')
        </div>
    </body>
</html>