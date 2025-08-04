<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // クエリで受け取った日付（なければ今日）
        $date = $request->input('date')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('date'))
            : \Carbon\Carbon::today();

        // 指定日の勤怠記録を取得
        $records = AttendanceRecord::with('user', 'breakTimes')
            ->whereDate('date', $date->format('Y-m-d'))
            ->get();

        return view('admin.attendance.index', compact('records', 'date'));
    }
}