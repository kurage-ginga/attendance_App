@extends('layouts.admin_app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
        {{-- タイトル --}}
            <h2 class="h4 fw-bold mb-4 ps-3 border-start border-4 border-dark">申請一覧</h2>

        @if(session('success'))
            <div class="alert alert-success py-2 mb-4">{{ session('success') }}</div>
        @endif

        {{-- タブナビゲーション --}}
        @php($activeTab = request('tab', 'pending'))
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}" id="pending-tab" data-bs-toggle="tab" data-bs-target="#tab-pending" type="button" role="tab" aria-controls="tab-pending" aria-selected="{{ $activeTab === 'pending' ? 'true' : 'false' }}">
                    承認待ち
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'approved' ? 'active' : '' }}" id="approved-tab" data-bs-toggle="tab" data-bs-target="#tab-approved" type="button" role="tab" aria-controls="tab-approved" aria-selected="{{ $activeTab === 'approved' ? 'true' : 'false' }}">
                    承認済み
                </button>
            </li>
        </ul>

        {{-- タブコンテンツ --}}
        <div class="tab-content">
            {{-- 承認待ち --}}
            <div class="tab-pane fade {{ $activeTab === 'pending' ? 'show active' : '' }}" id="tab-pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card shadow-sm mb-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 90px;">状態</th>
                                        <th style="width: 140px;">名前</th>
                                        <th style="width: 140px;">対象日</th>
                                        <th>申請理由</th>
                                        <th style="width: 140px;">申請日時</th>
                                        <th style="width: 80px;" class="text-center">詳細</th>
                                        <th style="width: 96px;" class="text-center">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($pendingRecords ?? []) as $record)
                                        <tr>
                                            <td class="text-center"><span class="badge bg-warning text-dark">承認待ち</span></td>
                                            <td>{{ $record->user->name }}</td>
                                            <td>{{ optional(\Illuminate\Support\Carbon::make($record->date))->format('Y-m-d') }}</td>
                                            <td class="text-truncate" style="max-width: 260px;">{{ $record->note ?? '-' }}</td>
                                            <td>{{ optional(\Illuminate\Support\Carbon::make($record->updated_at ?? $record->created_at))->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.attendance.approvals.show', $record->id) }}" class="link-primary">詳細</a>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.attendance.approve', $record->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm">承認</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">承認待ちの申請はありません。</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 承認済み --}}
            <div class="tab-pane fade {{ $activeTab === 'approved' ? 'show active' : '' }}" id="tab-approved" role="tabpanel" aria-labelledby="approved-tab">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 90px;">状態</th>
                                        <th style="width: 140px;">名前</th>
                                        <th style="width: 140px;">対象日</th>
                                        <th>申請理由</th>
                                        <th style="width: 140px;">承認日時</th>
                                        <th style="width: 80px;" class="text-center">詳細</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($approvedRecords ?? []) as $record)
                                        <tr>
                                            <td class="text-center"><span class="badge bg-success">承認済み</span></td>
                                            <td>{{ $record->user->name }}</td>
                                            <td>{{ optional(\Illuminate\Support\Carbon::make($record->date))->format('Y-m-d') }}</td>
                                            <td class="text-truncate" style="max-width: 260px;">{{ $record->note ?? '-' }}</td>
                                            <td>{{ optional(\Illuminate\Support\Carbon::make($record->updated_at ?? $record->created_at))->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.attendance.approvals.show', $record->id) }}" class="link-primary">詳細</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">承認済みの申請はありません。</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- クエリパラメータでタブを開く（?tab=approved など） --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const params = new URLSearchParams(window.location.search);
                const tab = params.get('tab');
                if (tab === 'approved') {
                    const trigger = document.querySelector('#approved-tab');
                    if (trigger) new bootstrap.Tab(trigger).show();
                } else if (tab === 'pending') {
                    const trigger = document.querySelector('#pending-tab');
                    if (trigger) new bootstrap.Tab(trigger).show();
                }
            });
        </script>
    </div>
</div>
</div>
@endsection