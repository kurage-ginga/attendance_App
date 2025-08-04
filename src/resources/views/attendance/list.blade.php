@extends('layouts.app')

@section('content')

<h1 class="text-xl font-bold mb-6">勤怠一覧</h1>

@php
    $currentMonth = \Carbon\Carbon::parse($currentMonth);
    $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
@endphp

<div class="flex justify-between items-center mb-4">
    <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="text-blue-500 hover:underline">&lt; 前月</a>
    <span class="text-lg font-semibold">{{ $currentMonth->format('Y年n月') }}</span>
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
        @foreach ($attendanceRecords as $record)
            @php
                $checkin = $record->checkin_at ? \Carbon\Carbon::parse($record->checkin_at) : null;
                $checkout = $record->checkout_at ? \Carbon\Carbon::parse($record->checkout_at) : null;
                $break = $record->breakTimes->sum(function ($b) {
                    return $b->end_at && $b->start_at ? \Carbon\Carbon::parse($b->end_at)->diffInMinutes($b->start_at) : 0;
                });

                $workMinutes = ($checkin && $checkout) ? $checkin->diffInMinutes($checkout) - $break : 0;
            @endphp
            <tr>
                <td class="border px-4 py-2">{{ $record->date }}</td>
                <td class="border px-4 py-2">{{ $checkin ? $checkin->format('H:i') : '' }}</td>
                <td class="border px-4 py-2">{{ $checkout ? $checkout->format('H:i') : '' }}</td>
                <td class="border px-4 py-2">{{ $break ? floor($break / 60) . 'h ' . ($break % 60) . 'm' : '' }}</td>
                <td class="border px-4 py-2">{{ $workMinutes ? floor($workMinutes / 60) . 'h ' . ($workMinutes % 60) . 'm' : '' }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('attendance.detail', ['date' => $record->date]) }}" class="text-blue-500 hover:underline">詳細</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection