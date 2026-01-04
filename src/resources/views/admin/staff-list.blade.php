@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff-list.css') }}">
@endsection

@section('content')
<div class="list__content">
    <div class="list__header">
        <h1>スタッフ一覧</h1>
    </div>
    <div class="list__table">
        <table class="table__inner">
            <tr class="table__row">
                <th class="table__header">名前</th>
                <th class="table__header">メールアドレス</th>
                <th class="table__header">月次勤怠</th>
            </tr>
            @foreach ($staffs as $staff)
            <tr class="table__row">
                <td class="table__item">{{ $staff->name }}</td>
                <td class="table__item">{{ $staff->email }}</td>
                <td class="table__item">
                    <a class="detail-link" href="/admin/attendance/staff/{{ $staff->id }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection