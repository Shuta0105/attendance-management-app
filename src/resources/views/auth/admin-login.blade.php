@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/admin-login.css') }}">
@endsection

@section('content')
<div class="admin-login__content">
    <div class="admin-login__header">
        <h1>管理者ログイン</h1>
    </div>
    <form action="" class="admin-login__form">
        <label class="form__label">メールアドレス</label>
        <input class="form__input" type="text" name="email" value="{{ old('email') }}">
        <label class="form__label">パスワード</label>
        <input class="form__input" type="password" name="password" value="{{ old('password') }}">
        <button class="form__button" type="submit">管理者ログインする</button>
    </form>
</div>
@endsection