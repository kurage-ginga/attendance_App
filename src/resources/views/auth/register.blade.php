@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h1 class="h4 fw-bold text-center">会員登録</h1>
                </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('register') }}" method="POST" novalidate>
                        @csrf

            {{-- 氏名 --}}
                        <div class="mb-4">
                            <label for="name" class="form-label">氏名</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name') }}" autocomplete="name" />
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

            {{-- メールアドレス --}}
                        <div class="mb-4">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email" />
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

            {{-- パスワード --}}
                        <div class="mb-4">
                            <label for="password" class="form-label">パスワード</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg @error('password') is-invalid @enderror" autocomplete="new-password" />
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

            {{-- パスワード（確認） --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">パスワード（確認）</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" autocomplete="new-password" />
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">登録する</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-white border-0 text-center pb-4">
                    <a href="{{ route('login') }}" class="link-secondary text-decoration-underline">ログインはこちら</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection