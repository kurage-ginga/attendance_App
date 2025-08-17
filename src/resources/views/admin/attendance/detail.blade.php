@extends('layouts.admin_app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h5 fw-bold mb-4">
                        <span class="me-2 border-start border-4 border-dark ps-2">勤怠詳細</span>
                    </h2>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.attendance.update', $record->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="table-responsive">
                            <table class="table align-middle mb-4">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" style="width: 20%">名前</th>
                                        <td>{{ $record->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">日付</th>
                                        <td>{{ optional(\Illuminate\Support\Carbon::make($record->date))->format('Y年n月j日') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">出勤・退勤</th>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <input type="time" name="checkin_at" class="form-control form-control-sm" style="max-width: 140px;"
                                                    value="{{ optional(\Illuminate\Support\Carbon::make($record->checkin_at))->format('H:i') }}">
                                                <span class="text-muted">〜</span>
                                                <input type="time" name="checkout_at" class="form-control form-control-sm" style="max-width: 140px;"
                                                    value="{{ optional(\Illuminate\Support\Carbon::make($record->checkout_at))->format('H:i') }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light align-top">休憩</th>
                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                @foreach ($record->breakTimes as $i => $break)
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <input type="time" name="breaks[{{ $i }}][start_at]" class="form-control form-control-sm" style="max-width: 140px;"
                                                            value="{{ optional(\Illuminate\Support\Carbon::make($break->start_at))->format('H:i') }}">
                                                        <span class="text-muted">〜</span>
                                                        <input type="time" name="breaks[{{ $i }}][end_at]" class="form-control form-control-sm" style="max-width: 140px;"
                                                            value="{{ optional(\Illuminate\Support\Carbon::make($break->end_at))->format('H:i') }}">
                                                    </div>
                                                @endforeach
                                                {{-- 追加用1行 --}}
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <input type="time" name="breaks[new][start_at]" class="form-control form-control-sm" style="max-width: 140px;">
                                                    <span class="text-muted">〜</span>
                                                    <input type="time" name="breaks[new][end_at]" class="form-control form-control-sm" style="max-width: 140px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">備考</th>
                                        <td>
                                            <textarea name="note" class="form-control" rows="2" placeholder="修正理由を入力">{{ old('note', $record->note) }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-dark px-4">修正</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection