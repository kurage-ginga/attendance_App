@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">勤怠詳細</h2>

        <form method="POST" action="{{ route('attendance.update', ['date' => $attendanceRecord->date->format('Y-m-d')]) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">名前</label>
            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">日付</label>
            <input type="text" class="form-control" value="{{ $attendanceRecord->date->format('Y年n月j日') }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">出勤・退勤</label>
            <div class="d-flex gap-2">
                <input type="time" class="form-control" name="checkin_at" value="{{ optional($attendanceRecord->checkin_at)->format('H:i') }}">
                <input type="time" class="form-control" name="checkout_at" value="{{ optional($attendanceRecord->checkout_at)->format('H:i') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">休憩</label>
            @foreach ($attendanceRecord->breakTimes as $index => $break)
                <div class="d-flex gap-2 mb-2">
                    <input type="time" class="form-control" name="breaks[{{ $index }}][start_at]" value="{{ optional($break->start_at)->format('H:i') }}">
                    <input type="time" class="form-control" name="breaks[{{ $index }}][end_at]" value="{{ optional($break->end_at)->format('H:i') }}">
                </div>
            @endforeach
            <!-- 追加フィールド -->
            <div class="d-flex gap-2 mb-2">
                <input type="time" class="form-control" name="breaks[new][start_at]" placeholder="開始">
                <input type="time" class="form-control" name="breaks[new][end_at]" placeholder="終了">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">備考</label>
            <textarea class="form-control" name="note" rows="3">{{ old('note', $attendanceRecord->note) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">修正</button>
    </form>
</div>

@endsection