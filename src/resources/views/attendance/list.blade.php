@extends('layouts.app')

@section('content')

    <h1 class="text-xl font-bold mb-6">勤怠一覧</h1>

    @php
        use Carbon\Carbon;

        // コントローラから `currentMonth` が "Y-m" 形式で渡ってくる想定
        $month      = Carbon::parse($currentMonth . '-01');
        $prevMonth  = $month->copy()->subMonth()->format('Y-m');
        $nextMonth  = $month->copy()->addMonth()->format('Y-m');

        // 当月の場合は今日まで、それ以外の月は月末まで表示
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth   = $month->copy()->endOfMonth();
        $today        = Carbon::today();
        $showUntil    = $month->isSameMonth($today) ? $today : $endOfMonth; // 未来日は表示しない

        // 既存レコードを日付キーのマップに（検索コスト削減）
        $recordsByDate = collect($attendanceRecords)->keyBy(function ($r) {
            return Carbon::parse($r->date)->format('Y-m-d');
        });

        $wdays = ['日', '月', '火', '水', '木', '金', '土'];
    @endphp

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="text-blue-500 hover:underline">&lt; 前月</a>
        <span class="text-lg font-semibold">{{ $month->format('Y年n月') }}</span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="text-blue-500 hover:underline">翌月 &gt;</a>
    </div>

    <table class="table-auto w-full text-center border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">日付</th>
                <th class="border px-4 py-2">出勤</th>
                <th class="border px-4 py-2">退勤</th>
                <th class="border px-4 py-2">休憩</th>
                <th class="border px-4 py-2">合計</th>
                <th class="border px-4 py-2">詳細</th>
            </tr>
        </thead>
        <tbody>
            @for ($day = $startOfMonth->copy(); $day->lte($showUntil); $day->addDay())
                @php
                    $key     = $day->format('Y-m-d');
                    $record  = $recordsByDate->get($key);

                    $checkin  = $record && $record->checkin_at ? Carbon::parse($record->checkin_at) : null;
                    $checkout = $record && $record->checkout_at ? Carbon::parse($record->checkout_at) : null;

                    $breakMinutes = 0;
                    if ($record && $record->relationLoaded('breakTimes')) {
                        $breakMinutes = $record->breakTimes->sum(function ($b) {
                            return ($b->start_at && $b->end_at)
                                ? Carbon::parse($b->start_at)->diffInMinutes(Carbon::parse($b->end_at))
                                : 0;
                        });
                    } elseif ($record) {
                        // 念のため遅延ロードにも対応
                        $breakMinutes = $record->breakTimes()->get()->sum(function ($b) {
                            return ($b->start_at && $b->end_at)
                                ? Carbon::parse($b->start_at)->diffInMinutes(Carbon::parse($b->end_at))
                                : 0;
                        });
                    }

                    $workMinutes = ($checkin && $checkout)
                        ? $checkin->diffInMinutes($checkout) - $breakMinutes
                        : 0;

                    $breakText = $breakMinutes > 0
                        ? floor($breakMinutes / 60) . 'h ' . ($breakMinutes % 60) . 'm'
                        : '';

                    $workText = $workMinutes > 0
                        ? floor($workMinutes / 60) . 'h ' . ($workMinutes % 60) . 'm'
                        : '';
                @endphp
                <tr>
                    <td class="border px-4 py-2">
                        {{ $day->format('m/d') }}({{ $wdays[$day->dayOfWeek] }})
                    </td>
                    <td class="border px-4 py-2">{{ $checkin ? $checkin->format('H:i') : '' }}</td>
                    <td class="border px-4 py-2">{{ $checkout ? $checkout->format('H:i') : '' }}</td>
                    <td class="border px-4 py-2">{{ $breakText }}</td>
                    <td class="border px-4 py-2">{{ $workText }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('attendance.detail', ['date' => $key]) }}" class="text-blue-500 hover:underline">詳細</a>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
@endsection