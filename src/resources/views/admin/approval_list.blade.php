@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">修正申請承認画面（管理者）</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ユーザー名</th>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>備考</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pendingRecords as $record)
                <tr>
                    <td>{{ $record->user->name }}</td>
                    <td>{{ $record->date->format('Y-m-d') }}</td>
                    <td>{{ optional($record->checkin_at)->format('H:i') }}</td>
                    <td>{{ optional($record->checkout_at)->format('H:i') }}</td>
                    <td>
                        @foreach ($record->breakTimes as $break)
                            {{ optional($break->start_at)->format('H:i') }} - {{ optional($break->end_at)->format('H:i') }}<br>
                        @endforeach
                    </td>
                    <td>{{ $record->note }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.attendance.approve', $record->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">承認</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection