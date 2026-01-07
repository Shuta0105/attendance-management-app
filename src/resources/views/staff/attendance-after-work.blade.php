<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/staff/attendance-after-work.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/">
                <img src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}"
                    alt="ロゴ画像">
            </a>
            <div class="header__nav">
                <a href="/attendance/list" class="header__nav-link">今月の出勤一覧</a>
                <a href="/stamp_correction_request/list" class="header__nav-link">申請一覧</a>
                <form action="/logout" method="post">
                    @csrf
                    <button class="logout__button">ログアウト</button>
                </form>
            </div>
        </div>
    </header>

    <main>
        <div class="attendance__content">
            <div class="attendance__status">退勤済</div>
            <div class="attendance__date"></div>
            <div class="attendance__time">
                <h1 id="time"></h1>
            </div>
            <div class="attendance-thanks">お疲れ様でした。</div>
        </div>
    </main>

    <script>
        function updateDate() {
            const now = new Date();

            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const date = now.getDate();
            const days = ['日', '月', '火', '水', '木', '金', '土'];
            const day = days[now.getDay()];

            document.querySelector('.attendance__date').textContent =
                `${year}年${month}月${date}日(${day})`;
            document.getElementById('time').textContent =
                new Date().toLocaleTimeString('ja-JP', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        updateDate();

        setInterval(updateDate, 1000);
    </script>
</body>

</html>