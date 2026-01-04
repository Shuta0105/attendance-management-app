@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/index.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    <form action="/attendance/clock-in" method="post" class="attendance__form">
        @csrf
        <div class="attendance__status">勤務外</div>
        <div class="attendance__date"></div>
        <div class="attendance__time">
            <h1 id="time"></h1>
        </div>
        <button class="attendance__button" type="submit">出勤</button>
    </form>
</div>
@endsection

@section('js')
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
@endsection