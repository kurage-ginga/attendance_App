@extends('layouts.app')

@section('content')
    <div class="container py-4">

        {{-- ページ見出し --}}
        <div class="d-flex align-items-center mb-4">
            <div class="me-2" style="width: 6px; height: 28px; background:#111;"></div>
            <h2 class="m-0 fw-bold">勤怠詳細</h2>
        </div>

        {{-- フラッシュ & バリデーションメッセージ --}}
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ステータスバッジ --}}
        @php
            $isPending = $attendanceRecord->status === 'pending_correction';
            $badgeMap = [
                'original' => ['text' => '修正なし', 'class' => 'bg-secondary'],
                'pending_correction' => ['text' => '承認待ち', 'class' => 'bg-warning text-dark'],
                'approved' => ['text' => '承認済み', 'class' => 'bg-success'],
            ];
            $badge = $badgeMap[$attendanceRecord->status] ?? ['text' => '不明', 'class' => 'bg-secondary'];
        @endphp

        <div class="mb-3">
            <span class="badge {{ $badge['class'] }} px-3 py-2">{{ $badge['text'] }}</span>
        </div>

        {{-- 詳細フォーム --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('attendance.update', \Illuminate\Support\Carbon::make($attendanceRecord->date)->format('Y-m-d')) }}">
                    @csrf

                    {{-- 名前 --}}
                    <div class="row align-items-center py-3 border-bottom">
                        <div class="col-12 col-md-3 text-muted">名前</div>
                        <div class="col-12 col-md-9">
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    </div>

                    {{-- 日付 --}}
                    <div class="row align-items-center py-3 border-bottom">
                        <div class="col-12 col-md-3 text-muted">日付</div>
                        <div class="col-12 col-md-9">
                            <input type="text" class="form-control" value="{{ $attendanceRecord->date->format('Y年n月j日') }}" readonly>
                        </div>
                    </div>

                    {{-- 出勤・退勤 --}}
                    <div class="row align-items-center py-3 border-bottom">
                        <div class="col-12 col-md-3 text-muted">出勤・退勤</div>
                        <div class="col-12 col-md-9">
                            <div class="d-flex gap-2">
                                <input
                                    type="time"
                                    class="form-control"
                                    name="checkin_at"
                                    value="{{ optional($attendanceRecord->checkin_at)->format('H:i') }}"
                                    @if($isPending) disabled @endif
                                >
                                <span class="align-self-center">〜</span>
                                <input
                                    type="time"
                                    class="form-control"
                                    name="checkout_at"
                                    value="{{ optional($attendanceRecord->checkout_at)->format('H:i') }}"
                                    @if($isPending) disabled @endif
                                >
                            </div>
                        </div>
                    </div>

                    {{-- 休憩（既存） --}}
                    <div class="row align-items-center py-3 border-bottom">
                        <div class="col-12 col-md-3 text-muted">休憩</div>
                        <div class="col-12 col-md-9">
                            @forelse ($attendanceRecord->breakTimes as $index => $break)
                                <div class="d-flex gap-2 mb-2">
                                    <input
                                        type="time"
                                        class="form-control"
                                        name="breaks[{{ $index }}][start_at]"
                                        value="{{ optional($break->start_at)->format('H:i') }}"
                                        @if($isPending) disabled @endif
                                    >
                                    <span class="align-self-center">〜</span>
                                    <input
                                        type="time"
                                        class="form-control"
                                        name="breaks[{{ $index }}][end_at]"
                                        value="{{ optional($break->end_at)->format('H:i') }}"
                                        @if($isPending) disabled @endif
                                    >
                                </div>
                            @empty
                                <div class="text-muted small mb-2">登録された休憩はありません</div>
                            @endforelse

                            {{-- 追加フィールド --}}
                            <div class="d-flex gap-2">
                                <input
                                    type="time"
                                    class="form-control"
                                    name="breaks[new][start_at]"
                                    placeholder="開始"
                                    @if($isPending) disabled @endif
                                >
                                <span class="align-self-center">〜</span>
                                <input
                                    type="time"
                                    class="form-control"
                                    name="breaks[new][end_at]"
                                    placeholder="終了"
                                    @if($isPending) disabled @endif
                                >
                            </div>
                            <div class="form-text">休憩が複数ある場合は上の行を編集して追加してください。</div>
                        </div>
                    </div>

                    {{-- 備考 --}}
                    <div class="row align-items-center py-3">
                        <div class="col-12 col-md-3 text-muted">備考</div>
                        <div class="col-12 col-md-9">
                            <textarea class="form-control" name="note" rows="3" @if($isPending) disabled @endif>{{ old('note', $attendanceRecord->note) }}</textarea>
                        </div>
                    </div>

                    {{-- ボタン行 --}}
                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <a href="{{ route('attendance.list') }}" class="btn btn-outline-secondary">一覧へ戻る</a>

                        @if(!$isPending)
                            <button type="submit" class="btn btn-primary px-4">修正</button>
                        @endif
                    </div>

                    {{-- 承認待ちの案内 --}}
                    @if($isPending)
                        <div class="alert alert-warning mt-3 mb-0">
                            承認待ちのため修正はできません。
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection