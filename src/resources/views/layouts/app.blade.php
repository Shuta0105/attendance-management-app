<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/">
                <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}"
                    alt="ロゴ画像">
            </a>
            <div class="header__nav">
                <a href="/attendance" class="header__nav-link">勤怠</a>
                <a href="/attendance/list" class="header__nav-link">勤怠一覧</a>
                <a href="/stamp_correction_request/list" class="header__nav-link">申請</a>
                <form action="/logout" method="post">
                    @csrf
                    <button class="logout__button">ログアウト</button>
                </form>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @yield('js')
</body>

</html>