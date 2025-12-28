@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <div class="register__header">
        <h1>会員登録</h1>
    </div>
    <form action="" class="register__form">
        <label class="form__label">名前</label>
        <input class="form__input" type="text" name="name" value="{{ old('name') }}">
        <label class="form__label">メールアドレス</label>
        <input class="form__input" type="text" name="email" value="{{ old('email') }}">
        <label class="form__label">パスワード</label>
        <input class="form__input" type="password" name="password" value="{{ old('password') }}">
        <label class="form__label">パスワード確認</label>
        <input class="form__input" type="password" name="password_confirmation" value="{{ old('password_confirmation') }}">
        <button class="form__button" type="submit">登録する</button>
    </form>
    <a href="/login" class="link_to_login">ログインはこちら</a>
</div>
@endsection