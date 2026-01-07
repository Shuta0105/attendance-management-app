@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div class="list__header">
        <h1>勤怠一覧</h1>
    </div>
    <div class="list__calendar">
        <button id="prev"><span class="arrow-button">←</span> 前月</button>
        <div class="month-label">
            <i class="fa-regular fa-calendar"></i>
            <span id="month"></span>
        </div>
        <button id="next">翌月 <span class="arrow-button">→</span></button>
    </div>
    <div class="list__table">
        <table class="table__inner">
            <tr class="table__row">
                <th class="table__header">日付</th>
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
                <td class="table__item">{{ $attendance->work_date->locale('ja')->isoFormat('MM/DD(ddd)') }}</td>
                <td class="table__item">{{ $attendance->clock_in_at->format('H:i') }}</td>
                <td class="table__item">{{ optional($attendance->clock_out_at)->format('H:i') ?? '' }}</td>
                <td class="table__item">{{ sprintf('%02d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60) }}</td>
                <td class="table__item">{{ sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60) }}</td>
                <td class="table__item">
                    <a class="detail-link" href='/attendance/detail/{{ $attendance->id }}'>詳細</a>
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

    const monthLabel = document.getElementById('month');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');

    function renderMonth() {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');

        monthLabel.textContent = `${year}/${month}`;
    }

    function moveMonth(diff) {
        currentDate.setMonth(currentDate.getMonth() + diff);

        const yyyy = currentDate.getFullYear();
        const mm = String(currentDate.getMonth() + 1).padStart(2, '0');

        window.location.href =
            `/attendance/list?date=${yyyy}-${mm}`;
    }

    prevBtn.onclick = () => moveMonth(-1);
    nextBtn.onclick = () => moveMonth(1);

    renderMonth();
</script>
@endsection