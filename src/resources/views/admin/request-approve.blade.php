@php
$requestDetail = $requestDetail->payload;
@endphp
@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request-approve.css') }}">
@endsection

@section('content')
<div class="detail__content">
    <div class="detail__header">
        <h1>勤怠詳細</h1>
    </div>
    <div class="detail__table">
        <form action="/stamp_correction_request/approve/{{ $request->id }}" method="post" class="detail-form">
            @csrf
            <table class="table__inner">
                <tr class="table__row">
                    <th class="table__header">名前</th>
                    <td class="table__item">
                        <input class="table__item-input" type="text" value="{{ $request->user->name }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__header">日付</th>
                    <td class="table__item">
                        <input class="table__item-input" type="text" value="{{ $request->attendance->work_date->format('Y年') }}" readonly>
                        <span></span>
                        <input class="table__item-input" type="text" value="{{ $request->attendance->work_date->format('n月j日') }}" readonly>
                    </td>
                </tr>
                <tr class="table__row">
                    <th class="table__header">出勤・退勤</th>
                    <td class="table__item">
                        <input class="table__item-input" type="text" value="{{ $requestDetail['clock_in_at'] }}" readonly>
                        <span>~</span>
                        <input class="table__item-input" type="text" value="{{ $requestDetail['clock_out_at'] }}" readonly>
                    </td>
                </tr>
                @isset($requestDetail['breaks'])
                @foreach ($requestDetail['breaks'] as $break)
                <tr class="table__row">
                    <th class="table__header">休憩</th>
                    <td class="table__item">
                        <input class="table__item-input" type="text" value="{{ $break['break_start_at'] }}" readonly>
                        <span>~</span>
                        <input class="table__item-input" type="text" value="{{ $break['break_end_at'] }}" readonly>
                    </td>
                </tr>
                @endforeach
                @endisset

                @if (
                !empty($requestDetail['new_breaks']) &&
                !empty($requestDetail['new_breaks'][0]['break_start_at']) &&
                !empty($requestDetail['new_breaks'][0]['break_end_at'])
                )
                <tr class="table__row">
                    <th class="table__header">休憩2</th>
                    <td class="table__item">
                        <input class="table__item-input" type="text" value="{{ $requestDetail['new_breaks'][0]['break_start_at'] }}" readonly>
                        <span>~</span>
                        <input class="table__item-input" type="text" value="{{ $requestDetail['new_breaks'][0]['break_end_at'] }}" readonly>
                    </td>
                </tr>
                @endif
                <tr class="table__row">
                    <th class="table__header">備考</th>
                    <td class="table__item">
                        <div>{{ $request->reason }}</div>
                    </td>
                </tr>
            </table>
            <div class="form__button">
                @if ($request->status === '承認待ち')
                <button class="form__button-submit" type="submit">承認</button>
                @elseif ($request->status === '承認済み')
                <button class="form__button-message" type="button">承認済み</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection