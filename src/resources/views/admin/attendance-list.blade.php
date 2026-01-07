@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div class="list__header">
        <h1 id="date-title"></h1>
    </div>
    <div class="list__calendar">
        <button id="prev"><span class="arrow-button">←</span> 前日</button>
        <div class="month-label">
            <i class="fa-regular fa-calendar"></i>
            <span id="month"></span>
        </div>
        <button id="next">翌日 <span class="arrow-button">→</span></button>
    </div>
    <div class="list__table">
        <table class="table__inner">
            <tr class="table__row">
                <th class="table__header">名前</th>
                <th class="table__header">出勤</th>
                <th class="table__header">退勤</th>
                <th class="table__header">休憩</th>
                <th class="table__header">合計</th>
                <th class="table__header">詳細</th>
            </tr>
            @foreach ($attendances as $attendance)
            @php
            $breakMinutes = $attendance->totalBreakMinutes();
            $workMinutes = $attendance->totalWorkMinutes();
            @endphp
            <tr class="table__row">
                <td class="table__item">{{ $attendance->user->name }}</td>
                <td class="table__item">{{ $attendance->clock_in_at->format('H:i') }}</td>
                <td class="table__item">{{ optional($attendance->clock_out_at)->format('H:i') ?? '' }}</td>
                <td class="table__item">{{ sprintf('%02d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60) }}</td>
                <td class="table__item">{{ sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60) }}</td>
                <td class="table__item">
                    <a class="detail-link" href="/admin/attendance/{{ $attendance->id }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
    let currentDate = new Date("{{ $date }}");

    const title = document.getElementById('date-title');
    const monthLabel = document.getElementById('month');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');

    function renderDate() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;
        const day = currentDate.getDate();

        title.textContent = `${year}年${month}月${day}日の勤怠`;
        monthLabel.textContent = `${year}年${month}月${day}日`;
    }

    function moveDate(diff) {
        currentDate.setDate(currentDate.getDate() + diff);

        const yyyy = currentDate.getFullYear();
        const mm = String(currentDate.getMonth() + 1).padStart(2, '0');
        const dd = String(currentDate.getDate()).padStart(2, '0');

        window.location.href =
            `/admin/attendance/list?date=${yyyy}-${mm}-${dd}`;
    }

    prevBtn.onclick = () => moveDate(-1);
    nextBtn.onclick = () => moveDate(1);

    renderDate();
</script>
@endsection