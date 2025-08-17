@extends('layouts.admin_app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h5 mb-0">{{ $date->format('Y年n月j日の勤怠') }}</h1>
        <div class="btn-group" role="group" aria-label="date-nav">
            <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">前日</a>
            <span class="btn btn-outline-secondary disabled text-body-emphasis bg-white">{{ $date->format('Y/m/d') }}</span>
            <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">翌日</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width:18%">名前</th>
                    <th scope="col" style="width:14%">出勤</th>
                    <th scope="col" style="width:14%">退勤</th>
                    <th scope="col" style="width:14%">休憩</th>
                    <th scope="col" style="width:18%">合計</th>
                    <th scope="col" style="width:12%">詳細</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($records as $record)
                @php
                    // 休憩合計（両端があるレコードのみ積算）
                    $breakTotalSeconds = $record->breakTimes->reduce(function($carry, $break){
                        if ($break->start_at && $break->end_at) {
                            return $carry + (strtotime($break->end_at) - strtotime($break->start_at));
                        }
                        return $carry;
                    }, 0);

                    // 実働時間（出退勤が揃っている場合のみ）
                    $workedSeconds = null;
                    if ($record->checkin_at && $record->checkout_at) {
                        $workedSeconds = strtotime($record->checkout_at) - strtotime($record->checkin_at) - $breakTotalSeconds;
                    }

                    $breakText = $breakTotalSeconds ? sprintf('%d:%02d', floor($breakTotalSeconds/3600), floor(($breakTotalSeconds%3600)/60)) : '';
                    $workText  = $workedSeconds !== null ? sprintf('%d:%02d', floor($workedSeconds/3600), floor(($workedSeconds%3600)/60)) : '';
                @endphp
                <tr>
                    <td>{{ $record->user->name }}</td>
                    <td>{{ $record->checkin_at ? \Carbon\Carbon::parse($record->checkin_at)->format('H:i') : '' }}</td>
                    <td>{{ $record->checkout_at ? \Carbon\Carbon::parse($record->checkout_at)->format('H:i') : '' }}</td>
                    <td>{{ $breakText }}</td>
                    <td>{{ $workText }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.detail', ['id' => $record->id]) }}" class="btn btn-sm btn-outline-primary">詳細</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection