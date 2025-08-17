@extends('layouts.admin_app')

@section('content')
<div class="container py-4">
    @php
    use Carbon\Carbon;
    // 今日・範囲計算（Carbonはミュータブルなので copy() を徹底）
    $today = Carbon::today();
    $todayStart = $today->copy()->startOfMonth();
    $start = $currentMonth->copy()->startOfMonth();
    $endOfMonth = $currentMonth->copy()->endOfMonth();

    // 当月は「今日」まで、過去月は「月末」まで
    $loopEnd = $currentMonth->isSameMonth($today) ? $today->copy() : $endOfMonth;

    // 未来月は一切表示しない（ガード）
    if ($currentMonth->gt($todayStart)) {
        $loopEnd = $start->copy()->subDay(); // ループに入らないように
    }

    // 当月より先には進めない（翌月ボタン制御）
    $canGoNext = $currentMonth->lt($todayStart);

    // 日付→レコードの辞書を用意
    $recordsByDate = $records->keyBy(function ($r) {
        return Carbon::parse($r->date)->format('Y-m-d');
    });
@endphp
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0 fw-bold">{{ $user->name }} さんの {{ $currentMonth->format('Y年n月') }} の勤怠</h4>
        <div class="d-none d-md-block">
            <a class="btn btn-outline-secondary btn-sm me-2"
                href="{{ route('admin.attendance.monthly', [$user->id, $currentMonth->copy()->subMonth()->year, $currentMonth->copy()->subMonth()->month]) }}">
                前月
            </a>
            <input type="text" class="form-control form-control-sm d-inline-block text-center" style="width: 110px;" value="{{ $currentMonth->format('Y/m') }}" readonly>
            @if($canGoNext)
                <a class="btn btn-outline-secondary btn-sm ms-2"
                    href="{{ route('admin.attendance.monthly', [$user->id, $currentMonth->copy()->addMonth()->year, $currentMonth->copy()->addMonth()->month]) }}">
                    翌月
                </a>
            @else
                <button class="btn btn-outline-secondary btn-sm ms-2" disabled>翌月</button>
            @endif
        </div>
    </div>

    <div class="d-md-none d-flex align-items-center justify-content-between mb-3">
        <a class="btn btn-outline-secondary btn-sm"
            href="{{ route('admin.attendance.monthly', [$user->id, $currentMonth->copy()->subMonth()->year, $currentMonth->copy()->subMonth()->month]) }}">前月</a>
        <span class="small fw-semibold">{{ $currentMonth->format('Y/m') }}</span>
        @if($canGoNext)
            <a class="btn btn-outline-secondary btn-sm"
                href="{{ route('admin.attendance.monthly', [$user->id, $currentMonth->copy()->addMonth()->year, $currentMonth->copy()->addMonth()->month]) }}">翌月</a>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>翌月</button>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-nowrap text-center">
                            <th style="width: 14%">日付</th>
                            <th style="width: 14%">出勤</th>
                            <th style="width: 14%">退勤</th>
                            <th style="width: 32%">休憩（合計）</th>
                            <th style="width: 14%">合計</th>
                            <th style="width: 12%">詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $hasAnyRow = false; @endphp
                    @for ($d = $start->copy(); $d->lte($loopEnd); $d->addDay())
                        @php
                            $dateKey = $d->format('Y-m-d');
                            $rec = $recordsByDate->get($dateKey);
                            $checkin = $rec? Carbon::make($rec->checkin_at) : null;
                            $checkout = $rec? Carbon::make($rec->checkout_at) : null;
                            // 休憩合計（分）
                            $breakMinutes = 0;
                            if ($rec) {
                                $breakMinutes = $rec->breakTimes->sum(function($b){
                                    if (!$b->start_at || !$b->end_at) return 0;
                                    return Carbon::parse($b->start_at)->diffInMinutes(Carbon::parse($b->end_at));
                                });
                            }
                            // 実働=退勤-出勤-休憩
                            $workMinutes = null;
                            if ($checkin && $checkout) {
                                $total = $checkin->diffInMinutes($checkout);
                                $workMinutes = max(0, $total - $breakMinutes);
                            }
                            $hasAnyRow = true;
                        @endphp
                        <tr class="text-center">
                            <td class="text-nowrap">{{ $d->format('Y-m-d') }}</td>
                            <td>{{ $checkin? $checkin->format('H:i') : '' }}</td>
                            <td>{{ $checkout? $checkout->format('H:i') : '' }}</td>
                            <td>
                                {{ $breakMinutes > 0 ? sprintf('%d:%02d', intdiv($breakMinutes,60), $breakMinutes%60) : '' }}
                            </td>
                            <td>
                                {{ !is_null($workMinutes) ? sprintf('%d:%02d', intdiv($workMinutes,60), $workMinutes%60) : '' }}
                            </td>
                            <td>
                                @if($rec)
                                    <a href="{{ route('admin.attendance.detail', $rec->id) }}" class="btn btn-outline-dark btn-sm">詳細</a>
                                @else
                                    <button class="btn btn-outline-dark btn-sm" disabled>詳細</button>
                                @endif
                            </td>
                        </tr>
                    @endfor

                    @if(!$hasAnyRow)
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">表示できる日付はありません</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end">
            <a href="{{ route('admin.attendance.csv', [$user->id, $currentMonth->year, $currentMonth->month]) }}" class="btn btn-dark btn-sm">CSV出力</a>
        </div>
    </div>
</div>
@endsection