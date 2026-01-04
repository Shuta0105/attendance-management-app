@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <div class="register__header">
        <h1>会員登録</h1>
    </div>
    <form action="/register" method="post" class="register__form">
        @csrf
        <label class="form__label">名前</label>
        <input class="form__input" type="text" name="name" value="{{ old('name') }}">
        @error ('name')
        <div class="form__error">
            {{ $message }}
        </div>
        @enderror  
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
        <label class="form__label">パスワード確認</label>
        <input class="form__input" type="password" name="password_confirmation" value="{{ old('password_confirmation') }}">
        <button class="form__button" type="submit">登録する</button>
    </form>
    <a href="/login" class="link_to_login">ログインはこちら</a>
</div>
@endsection