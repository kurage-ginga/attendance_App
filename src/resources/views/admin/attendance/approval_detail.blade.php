@extends('layouts.admin_app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <h2 class="h4 mb-0">勤怠詳細（承認用）</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <tbody>
                    <tr>
                        <th class="bg-light fw-semibold" style="width: 20%">名前</th>
                        <td>{{ $record->user->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light fw-semibold">日付</th>
                        <td>{{ optional(\Illuminate\Support\Carbon::make($record->date))->format('Y年n月j日') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light fw-semibold">出勤・退勤</th>
                        <td>
                            {{ optional(\Illuminate\Support\Carbon::make($record->checkin_at))->format('H:i') ?? '—' }}
                            <span class="mx-2">〜</span>
                            {{ optional(\Illuminate\Support\Carbon::make($record->checkout_at))->format('H:i') ?? '—' }}
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light fw-semibold">休憩</th>
                        <td>
                            @if ($record->breakTimes->isNotEmpty())
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($record->breakTimes as $break)
                                        <div>
                                            {{ optional(\Illuminate\Support\Carbon::make($break->start_at))->format('H:i') }}
                                            <span class="mx-2">〜</span>
                                            {{ optional(\Illuminate\Support\Carbon::make($break->end_at))->format('H:i') ?? '—' }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light fw-semibold">備考</th>
                        <td>{{ $record->note ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <a href="{{ route('admin.attendance.approvals') }}" class="btn btn-outline-secondary">戻る</a>

        @if ($record->status === 'pending_correction')
            <form action="{{ route('admin.attendance.approve', $record->id) }}" method="POST" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-primary px-4">承認</button>
            </form>
        @else
            <span class="text-muted">この勤怠は既に承認済みです。</span>
        @endif
    </div>
</div>
@endsection