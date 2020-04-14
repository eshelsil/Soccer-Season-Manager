<!DOCTYPE html>
<html>
    <head>
        <title>2020 - @yield('title')</title>
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
        <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
        <script src="{{ asset('/js/app.js') }}"></script>
        @section('script')
        @show
    </head>
    <body>
        <div class="text-center container-fluid p-4 bg-primary text-white">
            <p class="h1 m-0">Season 2020</p>
        </div>
        
        @yield('menu')

        <div class="ml-5 mr-5 mt-3">
            @yield('content')
        </div>
    </body>
</html>