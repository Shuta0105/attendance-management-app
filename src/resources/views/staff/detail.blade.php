@php
$modelRequest = $modelRequest ?? null;
$requestDetail = $requestDetail ? $requestDetail->payload : [];
@endphp

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/detail.css') }}">
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
        <form action="/attendance/modify/{{ $attendance->id }}" method="post" class="detail-form">
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
                        <input class="table__item-input table__item-input--date"
                            type="text"
                            value="{{ $attendance->work_date->format('Y年')  }}" readonly>
                        <span></span>
                        <input class="table__item-input table__item-input--date"
                            type="text"
                            value="{{ $attendance->work_date->format('n月j日') }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__header">出勤・退勤</th>
                    <td class="table__item">
                        <input class="table__item-input 
                        {{ $modelRequest?->status === '承認待ち' ? 'pending' : '' }}"
                            name="clock_in_at" type="text"
                            value="{{ old(
                                "clock_in_at",
                                !$modelRequest || $modelRequest?->status === '承認済み'  
                                ? optional($attendance->clock_in_at)->format('H:i') 
                                : $requestDetail['clock_in_at'] ?? '' 
                            ) }}"
                            @if (optional($modelRequest)->status === '承認待ち') readonly @endif>
                        <span>~</span>
                        <input class="table__item-input
                        {{ $modelRequest?->status === '承認待ち' ? 'pending' : '' }}"
                            name="clock_out_at" type="text"
                            value="{{ old(
                                "clock_out_at",
                                !$modelRequest || $modelRequest?->status === '承認済み'  
                                ? optional($attendance->clock_out_at)->format('H:i') 
                                : $requestDetail['clock_out_at'] ?? '' 
                            ) }}"
                            @if (optional($modelRequest)->status === '承認待ち') readonly @endif>
                    </td>
                </tr>
                @foreach ($attendance->breakTimes as $break)
                <tr class="table__row">
                    <th class="table__header">休憩</th>
                    <td class="table__item">
                        <input type="hidden" name="breaks[{{ $break->id }}][id]" value="{{ $break->id }}">
                        <input class="table__item-input
                        {{ $modelRequest?->status === '承認待ち' ? 'pending' : '' }}"
                            name="breaks[{{ $break->id }}][break_start_at]"
                            type="text"
                            value="{{ old(
                                "breaks.$break->id.break_start_at",
                                !$modelRequest || $modelRequest?->status === '承認済み'
                                ? optional($break->break_start_at)->format('H:i')
                                : $requestDetail['breaks'][$break->id]['break_start_at'] ?? ''
                            ) }}"
                            @if (optional($modelRequest)->status === '承認待ち') readonly @endif>
                        <span>~</span>
                        <input class="table__item-input
                        {{ $modelRequest?->status === '承認待ち' ? 'pending' : '' }}"
                            name="breaks[{{ $break->id }}][break_end_at]"
                            type="text"
                            value="{{ old(
                                "breaks.$break->id.break_end_at",
                                !$modelRequest || $modelRequest?->status === '承認済み'  
                                ? optional($break->break_end_at)->format('H:i') 
                                : $requestDetail['breaks'][$break->id]['break_end_at'] ?? ''
                            ) }}"
                            @if (optional($modelRequest)->status === '承認待ち') readonly @endif>
                    </td>
                </tr>
                @endforeach
                @if ($requestDetail &&
                !isset($requestDetail['new_breaks'][0]['break_start_at']) &&
                !isset($requestDetail['new_breaks'][0]['break_end_at']))
                @else
                <tr class="table__row">
                    <th class="table__header">休憩2</th>
                    <td class="table__item">
                        <input class="table__item-input
                        {{ $modelRequest?->status === '承認待ち' ? 'pending' : '' }}"
                            name="new_breaks[0][break_start_at]" type="text"
                            value="{{ old(
                                "new_breaks.0.break_start_at",
                                !$modelRequest || $modelRequest?->status === '承認済み'  
                                ? '' 
                                : $requestDetail['new_breaks'][0]['break_start_at'] ?? '' 
                            ) }}"
                            @if (optional($modelRequest)->status === '承認待ち') readonly @endif>
                        <span>~</span>
                        <input class="table__item-input
                        {{ $modelRequest?->status === '承認待ち' ? 'pending' : '' }}"
                            name="new_breaks[0][break_end_at]" type="text"
                            value="{{ old(
                                "new_breaks.0.break_end_at",
                                !$modelRequest || $modelRequest?->status === '承認済み'  
                                ? '' 
                                : $requestDetail['new_breaks'][0]['break_end_at'] ?? '' 
                            ) }}"
                            @if (optional($modelRequest)->status === '承認待ち') readonly @endif>
                    </td>
                </tr>
                @endif
                <tr class="table__row">
                    <th class="table__header">備考</th>
                    <td class="table__item">
                        @if ($modelRequest?->reason)
                        <div>{{ $modelRequest?->reason }}</div>
                        @else
                        <textarea class="table__item-textarea" name="reason" rows="5">{{ old('reason') }}</textarea>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="form__button">
                @if ($modelRequest?->status === '承認待ち')
                <div class="form__button-message">*承認待ちのため修正はできません。</div>
                @else
                <button class="form__button-submit" type="submit">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection