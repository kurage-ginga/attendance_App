@extends('layouts.app')

<!-- タイトル -->

<!-- CSS読み込み -->

<!-- 本体 -->
@section('content')
    <form action="/register" method="POST" >
        @csrf
        <h1 class="page__title">会員登録</h1>

        <label for="name" class="entry__name">氏名</label>
        <input type="text" name="name" id="name" class="input" value="{{ old('name') }}">

        <label for="mail" class="entry__name">メールアドレス</label>
        <input type="email" name="email" id="mail" class="input" value="{{ old('email') }}">

        <label for="password" class="entry__name">パスワード</label>
        <input type="password" name="password" id="password" class="input">

        <label for="password_confirm" class="entry__name">パスワード（確認）</label>
        <input type="password" name="password_confirmation" id="password_confirm" class="input">

        <button class="btn btn--big">登録する</button>
    </form>

        <a href="/login" class="link">ログインはこちら</a>
</div>
@endsection