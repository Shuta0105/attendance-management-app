@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/request-list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div class="list__header">
        <h1>申請一覧</h1>
    </div>
    <div class="list__tags">
        <a href="/stamp_correction_request/list?status=pending"
            class="list__tag {{ $status === 'pending' ? 'active' : '' }}">承認待ち
        </a>
        <a href="/stamp_correction_request/list?status=approved"
            class="list__tag {{ $status === 'approved' ? 'active' : '' }}">承認済み
        </a>
    </div>
    <div class="list__table">
        <table class="table__inner">
            <tr class="table__row">
                <th class="table__header">状態</th>
                <th class="table__header">名前</th>
                <th class="table__header">対象日時</th>
                <th class="table__header">申請理由</th>
                <th class="table__header">申請日時</th>
                <th class="table__header">詳細</th>
            </tr>
            @foreach ($requests as $request)
            <tr class="table__row">
                <td class="table__item">{{ $request->status }}</td>
                <td class="table__item">{{ $request->user->name }}</td>
                <td class="table__item">{{ $request->attendance->work_date->format('Y/m/d') }}</td>
                <td class="table__item">{{ $request->reason }}</td>
                <td class="table__item">{{ $request->requested_at->format('Y/m/d') }}</td>
                <td class="table__item">
                    <a class="detail-link" href="/attendance/detail/{{ $request->attendance->id }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection