<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
                padding: 10px;
                font-size: 18px;
            }

            body.success {
                color: green;
            }

            body.fail {
                color: red;
            }

            a {
                display: inline-block;
                margin-top: 15px;
                color: #3d3d52;
                text-decoration: none;
            }

            a:hover {
                color: #1f1f31;
                text-decoration: underline;
            }
        </style>
    </head>

    <body class="@yield('status')">
        <div>@yield('message')</div>

        <div>
            <a href="@yield('home_url', url('/'))">@yield('home_button', 'Return home')</a>
        </div>
    </body>
</html>
