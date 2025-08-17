@extends('layouts.app')

@section('content')
    @php
        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
        $today = now();
        $statusBadgeMap = [
            'before_work' => '出勤前',
            'working' => '勤務中',
            'on_break' => '休憩中',
            'after_work' => '退勤済',
        ];
        $badgeText = $statusBadgeMap[$status] ?? '';
    @endphp

    <div class="container py-5">
        <div class="mx-auto" style="max-width: 720px;">
            <div class="d-flex align-items-center mb-4">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" style="height: 28px;">
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    @if($badgeText)
                        <span class="badge rounded-pill bg-secondary mb-3 px-3 py-2">{{ $badgeText }}</span>
                    @endif

                    <p class="mb-1 text-muted">{{ $today->format('Y年n月j日') }}（{{ $weekDays[$today->dayOfWeek] }}）</p>
                    <div class="display-3 fw-bold mb-4" id="current-time">--:--</div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        @if ($status === 'before_work')
                            <form method="POST" action="{{ route('attendance.start') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg px-4">出勤</button>
                            </form>
                        @elseif ($status === 'working')
                            <form method="POST" action="{{ route('attendance.end') }}">
                                @csrf
                                <button type="submit" class="btn btn-dark btn-lg px-4">退勤</button>
                            </form>
                            <form method="POST" action="{{ route('break.start') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-lg px-4">休憩入</button>
                            </form>
                        @elseif ($status === 'on_break')
                            <form method="POST" action="{{ route('break.end') }}">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-lg px-4">休憩戻</button>
                            </form>
                        @elseif ($status === 'after_work')
                            <p class="mt-2 mb-0 text-muted">お疲れ様でした。</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}`;
        }
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
    </script>
@endsection