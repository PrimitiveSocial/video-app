<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="antialiased"> <!-- bg-gradient-to-b from-slate-800 to-slate-600 -->
        <div id="app">
            <homepage></homepage>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
