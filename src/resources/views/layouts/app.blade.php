<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
</head>
<body>

    <header>
        <nav>
            <ul style="display: flex; gap: 10px; list-style: none; padding: 0;">
                <li><a href="{{ route('attendance') }}">打刻</a></li>
                <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
                <li><a href="{{ route('attendance.requests') }}">申請一覧</a></li>
                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a></li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </header>

    @yield('content')

</body>
</html>