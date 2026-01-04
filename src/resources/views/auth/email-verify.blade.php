@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/email-verify.css') }}">
@endsection

@section('content')
<div class="verify__content">
    <div class="verify__message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </div>
    <div class="verify__button">
        <a href="https://mailtrap.io/inboxes" class="verify__button-submit" target="_blank">
            認証はこちらから
        </a>
    </div>
    <div class="verify__send">
        <form action="/email/verification-notification" method="post">
            @csrf
            <button class="verify__send-submit">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection