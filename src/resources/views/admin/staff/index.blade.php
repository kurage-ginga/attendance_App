@extends('layouts.admin_app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h2 class="h5 fw-bold mb-4">
                <span class="me-2 border-start border-4 border-dark ps-2">スタッフ一覧</span>
            </h2>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%">名前</th>
                                    <th style="width: 50%">メールアドレス</th>
                                    <th class="text-center" style="width: 20%">月次勤怠</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td class="text-muted">{{ $member->email }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.attendance.monthly', [$member->id, now()->year, now()->month]) }}" class="btn btn-outline-dark btn-sm">詳細</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">スタッフが登録されていません。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection