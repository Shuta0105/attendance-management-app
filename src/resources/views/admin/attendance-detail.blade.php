@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-detail.css') }}">
@endsection

@section('content')
<div class="detail__content">
    <div class="detail__header">
        <h1>勤怠詳細</h1>
    </div>
    @if ($errors->any())
    <div class="form__error">
        {{ $errors->first() }}
    </div>
    @endif
    <div class="detail__table">
        <form action="/admin/modify/{{ $attendance->id }}" method="post" class="detail-form">
            @csrf
            <table class="table__inner">
                <tr class="table__row">
                    <th class="table__header">名前</th>
                    <td class="table__item">
                        <input class="table__item-input table__item-input--name" type="text" value="{{ $attendance->user->name }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__header">日付</th>
                    <td class="table__item">
                        <input class="table__item-input table__item-input--date" type="text" value="{{ $attendance->work_date->format('Y年') }}" readonly>
                        <span></span>
                        <input class="table__item-input table__item-input--date" type="text" value="{{ $attendance->work_date->format('n月j日') }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__header">出勤・退勤</th>
                    <td class="table__item">
                        <input class="table__item-input" name="clock_in_at" type="text"
                            value="{{ old( "clock_in_at", $attendance->clock_in_at->format('H:i') ) }}">
                        <span>~</span>
                        <input class="table__item-input" name="clock_out_at" type="text"
                            value="{{ old( "clock_out_at", optional($attendance->clock_out_at)->format('H:i') ) }}">
                    </td>
                </tr>
                @foreach ($attendance->breakTimes as $index => $break)
                <tr class="table__row">
                    <th class="table__header">休憩</th>
                    <td class="table__item">
                        <input type="hidden" name="breaks[{{ $index }}][id]" value="{{ $break->id }}">
                        <input class="table__item-input" name="breaks[{{ $index }}][break_start_at]" type="text"
                            value="{{ old( "breaks.$index.break_start_at", optional($break->break_start_at)->format('H:i') ) }}">
                        <span>~</span>
                        <input class="table__item-input" name="breaks[{{ $index }}][break_end_at]" type="text"
                            value="{{ old( "breaks.$index.break_end_at", optional($break->break_end_at)->format('H:i') ) }}">
                    </td>
                </tr>
                @endforeach
                <tr class="table__row">
                    <th class="table__header">休憩2</th>
                    <td class="table__item">
                        <input class="table__item-input" name="new_breaks[0][break_start_at]" type="text"
                            value="{{ old('new_breaks.0.break_start_at') }}">
                        <span>~</span>
                        <input class="table__item-input" name="new_breaks[0][break_end_at]" type="text"
                            value="{{ old('new_breaks.0.break_end_at') }}">
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__header">備考</th>
                    <td class="table__item">
                        <textarea class="table__item-textarea" name="reason" rows="5">{{ old('reason') }}</textarea>
                    </td>
                </tr>
            </table>
            <div class="form__button">
                <button class="form__button-submit" type="submit">修正</button>
            </div>
        </form>
    </div>
</div>
@endsection('content')