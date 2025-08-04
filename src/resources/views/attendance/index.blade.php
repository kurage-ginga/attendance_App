@extends('layouts.app')

@section('content')
    <h1 class="title">打刻画面</h1>
    <div class="text-center mt-12">
    @php
        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        $today = now();
    @endphp

    <p>{{ $today->format("Y年n月j日") }}（{{ $weekDays[$today->dayOfWeek] }}）</p>
    <p class="text-2xl mt-2">現在時刻：<span id="current-time"></span></p>

        <div class="mt-6">
            @if ($status === 'before_work')
                <form method="POST" action="{{ route('attendance.start') }}">
                    @csrf
                    <button class="btn btn-primary">出勤</button>
                </form>
            @elseif ($status === 'working')
                <form method="POST" action="{{ route('attendance.end') }}">
                    @csrf
                    <button class="btn btn-dark">退勤</button>
                </form>
                <form method="POST" action="{{ route('break.start') }}">
                    @csrf
                    <button class="btn btn-light ml-4">休憩入</button>
                </form>
            @elseif ($status === 'on_break')
                <form method="POST" action="{{ route('break.end') }}">
                    @csrf
                    <button class="btn btn-light">休憩戻</button>
                </form>
            @elseif ($status === 'after_work')
                <p class="mt-4">お疲れ様でした。</p>
            @endif
        </div>
    </div>

    <script>
        function updateCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}`;
        }

        updateCurrentTime(); // 初回呼び出し
        setInterval(updateCurrentTime, 10000); // 10秒ごとに更新
    </script>
@endsection