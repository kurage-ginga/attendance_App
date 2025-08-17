<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ページ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
        @php
        $isLogin = request()->routeIs('admin.login');
        function adminNavActive($name) {
            return request()->routeIs($name) ? 'underline font-semibold' : '';
        }
    @endphp
    <header>
        @if($isLogin)
        <nav class="navbar navbar-dark bg-dark justify-content-center">
            <a class="navbar-brand" href="{{ route('admin.attendance.index') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" height="30">
            </a>

        </nav>
        @else
        {{-- After login: responsive navbar with links on the right --}}
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('admin.attendance.index') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" height="30">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.attendance.index') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">勤怠一覧</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.staff.index') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">スタッフ一覧</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.attendance.approvals') ? 'active' : '' }}" href="{{ route('admin.attendance.approvals') }}">申請一覧</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link p-0">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @endif
    </header>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-bold mb-2">入力内容を確認してください</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</html>