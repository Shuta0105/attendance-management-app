<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/auth.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}"
                alt="ロゴ画像">
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>