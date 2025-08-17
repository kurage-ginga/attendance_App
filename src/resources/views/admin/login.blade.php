@extends('layouts.admin_app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h4 text-center mb-4">管理者ログイン</h2>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input id="email" type="email" name="email" class="form-control" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">パスワード</label>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">管理者ログインする</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection