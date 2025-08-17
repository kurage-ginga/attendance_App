<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Http\Requests\AdminAttendanceUpdateRequest;
use Carbon\Carbon;

class AttendanceApprovalController extends Controller
{
    public function index()
    {
        $pendingRecords = AttendanceRecord::with('user')
            ->where('status', 'pending_correction')
            ->orderByDesc('updated_at')
            ->get();

        $approvedRecords = AttendanceRecord::with('user')
            ->where('status', 'corrected')
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.attendance.approvals', compact('pendingRecords', 'approvedRecords'));
    }

    public function show($id)
    {
        $record = AttendanceRecord::with(['user', 'breakTimes'])->findOrFail($id);

        return view('admin.attendance.approval_detail', compact('record'));
    }

    public function approve($id)
    {
        $record = AttendanceRecord::findOrFail($id);
        $record->status = 'corrected';
        $record->save();

        return redirect()->route('admin.attendance.approvals')->with('success', '申請を承認しました。');
    }

    public function update(AdminAttendanceUpdateRequest $request, $id)
    {
        $record = AttendanceRecord::with('breakTimes')->findOrFail($id);

        $baseDate = $record->date instanceof Carbon ? $record->date : Carbon::parse($record->date);
        // 出勤・退勤時間の更新
        $record->checkin_at = Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $request->checkin_at);
        $record->checkout_at = Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $request->checkout_at);

        // 備考更新
        $record->note = $request->note;

        // ステータス更新
        $record->status = 'corrected';
        $record->save();

        // 既存の休憩時間を削除して再登録
        $record->breakTimes()->delete();
        if ($request->has('breaks')) {
            foreach ($request->breaks as $break) {
                if (!empty($break['start_at']) && !empty($break['end_at'])) {
                    $record->breakTimes()->create([
                        'start_at' => Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $break['start_at']),
                        'end_at' => Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $break['end_at']),
                    ]);
                }
            }
        }

        return redirect()->route('admin.attendance.approvals')->with('success', '勤怠情報を更新しました。');
    }
}