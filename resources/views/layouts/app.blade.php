<!DOCTYPE html>
<html>
    <head>
        <title>2020 - @yield('title')</title>
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
        <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
    </head>
    <body>
        <div class="text-center container-fluid p-4 bg-primary text-white">
            <p class="h1 m-0">Season 2020</p>
        </div>
        
        @section('menu')
        @show
        
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>