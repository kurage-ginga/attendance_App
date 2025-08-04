@extends('layouts.admin_app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">勤怠一覧</h1>

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="bg-blue-500 text-white px-4 py-2 rounded">前日</a>
        <h2 class="text-xl font-semibold">{{ $date->format('Y年m月d日') }}</h2>
        <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="bg-blue-500 text-white px-4 py-2 rounded">翌日</a>
    </div>

    <table class="table-auto w-full border-collapse border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">名前</th>
                <th class="border px-4 py-2">出勤</th>
                <th class="border px-4 py-2">退勤</th>
                <th class="border px-4 py-2">休憩</th>
                <th class="border px-4 py-2">合計</th>
                <th class="border px-4 py-2">詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td class="border px-4 py-2">{{ $record->user->name }}</td>
                    <td class="border px-4 py-2">{{ $record->checkin_at ? \Carbon\Carbon::parse($record->checkin_at)->format('H:i') : '' }}</td>
                    <td class="border px-4 py-2">{{ $record->checkout_at ? \Carbon\Carbon::parse($record->checkout_at)->format('H:i') : '' }}</td>
                    <td class="border px-4 py-2">
                        @php
                            $breakTotal = $record->breakTimes->sum(function ($break) {
                                return strtotime($break->end_at) - strtotime($break->start_at);
                            });
                        @endphp
                        {{ $breakTotal ? floor($breakTotal / 60) . '分' : '' }}
                    </td>
                    <td class="border px-4 py-2">
                        @if ($record->checkin_at && $record->checkout_at)
                            @php
                                $workedSeconds = strtotime($record->checkout_at) - strtotime($record->checkin_at) - $breakTotal;
                            @endphp
                            {{ floor($workedSeconds / 3600) . '時間' . floor(($workedSeconds % 3600) / 60) . '分' }}
                        @endif
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('attendance.detail', ['date' => $record->date->format('Y-m-d')]) }}" class="text-blue-600 underline">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection