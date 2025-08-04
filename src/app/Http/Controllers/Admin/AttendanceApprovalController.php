<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;

class AttendanceApprovalController extends Controller
{
    public function index()
    {
        $pendingRecords = AttendanceRecord::with(['user', 'breakTimes'])
            ->where('status', '修正申請中')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.approval_list', compact('pendingRecords'));
    }

    public function approve($id)
    {
        $record = AttendanceRecord::findOrFail($id);
        $record->status = '修正承認済み';
        $record->save();

        return redirect()->route('admin.attendance.approvals')->with('success', '申請を承認しました。');
    }
}