@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="login__content">
    <div class="login__header">
        <h1>ログイン</h1>
    </div>
    <form action="/login" method="post" class="login__form">
        @csrf
        <input type="hidden" name="login_type" value="user">
        <label class="form__label">メールアドレス</label>
        <input class="form__input" type="text" name="email" value="{{ old('email') }}">
        @error ('email')
        <div class="form__error">
            {{ $message }}
        </div>
        @enderror
        <label class="form__label">パスワード</label>
        <input class="form__input" type="password" name="password" value="{{ old('password') }}">
        @error ('password')
        <div class="form__error">
            {{ $message }}
        </div>
        @enderror
        <button class="form__button" type="submit">ログインする</button>
    </form>
    <a href="/register" class="link_to_register">会員登録はこちら</a>
</div>
@endsection