<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <img src="{{ asset('storage/images/logo.svg') }}" alt="COACHTECHロゴ" class="login__logo">
            @yield('link')
        </header>
        <main>
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>