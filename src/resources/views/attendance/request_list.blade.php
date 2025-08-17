@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">申請一覧</h2>

    <div class="tabs mb-3">
        <button onclick="switchTab('pending')" class="tab-button active">承認待ち</button>
        <button onclick="switchTab('approved')" class="tab-button">承認済み</button>
    </div>

    <div id="pending-tab" class="tab-content">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>申請日</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingRecords as $record)
                <tr>
                    <td>承認待ち</td>
                    <td>{{ $record->user->name }}</td>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->note }}</td>
                    <td>{{ $record->updated_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('attendance.detail', ['date' => $record->date]) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="approved-tab" class="tab-content" style="display:none;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>承認日</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvedRecords as $record)
                <tr>
                    <td>承認済み</td>
                    <td>{{ $record->user->name }}</td>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->note }}</td>
                    <td>{{ $record->updated_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('attendance.detail', ['date' => $record->date]) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('pending-tab').style.display = tab === 'pending' ? 'block' : 'none';
    document.getElementById('approved-tab').style.display = tab === 'approved' ? 'block' : 'none';
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.tab-button[onclick="switchTab('${tab}')"]`).classList.add('active');
}
</script>
@endsection