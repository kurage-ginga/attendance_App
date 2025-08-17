<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::guard('employee')->user();
        $today = Carbon::today();

        // 今日の出勤記録
        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with('breakTimes')
            ->first();

        // 状態判定
        $status = 'before_work'; // 初期値
        if ($attendance) {
            if ($attendance->checkout_at) {
                $status = 'after_work';
            } elseif ($attendance->breakTimes->whereNull('end_at')->isNotEmpty()) {
                $status = 'on_break';
            } elseif ($attendance->checkin_at) {
                $status = 'working';
            }
        }

        return view('attendance.index', compact('attendance', 'status'));
    }

    public function start()
    {
        AttendanceRecord::create([
            'user_id' => Auth::guard('employee')->id(),
            'date' => now()->toDateString(),
            'checkin_at' => now(),
        ]);
        return redirect()->route('attendance');
    }

    public function end()
    {
        $record = AttendanceRecord::where('user_id', Auth::guard('employee')->id())
            ->whereDate('date', now()->toDateString())
            ->first();

        $record->update(['checkout_at' => now()]);
        return redirect()->route('attendance');
    }

    public function startBreak()
    {
        $record = AttendanceRecord::where('user_id', Auth::guard('employee')->id())
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($record) {
            BreakTime::create([
                'attendance_record_id' => $record->id,
                'start_at' => now(),
            ]);
        }

        return redirect()->route('attendance');
    }

    public function endBreak()
    {
        $break = BreakTime::whereHas('attendanceRecord', function ($q) {
            $q->where('user_id', Auth::guard('employee')->id())->whereDate('date', now()->toDateString());
        })->whereNull('end_at')->latest()->first();

        $break->update(['end_at' => now()]);
        return redirect()->route('attendance');
    }

    public function list(Request $request)
    {
        $user = Auth::guard('employee')->user();

        // 対象月（デフォルトは今月）
        $month = $request->input('month') 
            ? Carbon::createFromFormat('Y-m', $request->input('month'))
            : Carbon::now();

        // 月初と月末
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $attendanceRecords = AttendanceRecord::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get();

        return view('attendance.list', [
            'attendanceRecords' => $attendanceRecords,
            'currentMonth' => $month,
        ]);
    }

    public function detail($date)
    {
        $user = Auth::guard('employee')->user();

        $record = AttendanceRecord::with('breakTimes')
            ->where('user_id', $user->id)
            ->where('date', $date)
            ->firstOrFail();

        return view('attendance.detail', ['attendanceRecord' => $record]);
    }


    public function update(UpdateAttendanceRequest $request, $date)
    {
        $user = Auth::guard('employee')->user();

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $date)
            ->firstOrFail();

        // 勤怠情報を更新
        $record->checkin_at = Carbon::createFromFormat('Y-m-d H:i', $record->date . ' ' . $request->input('checkin_at'));
        $record->checkout_at = Carbon::createFromFormat('Y-m-d H:i', $record->date . ' ' . $request->input('checkout_at'));
        $record->status = 'pending_correction';

        $record->save();

        // 既存の休憩時間を削除して新規登録
        $record->breakTimes()->delete();

        $breaks = $request->input('breaks', []);
        $dateCarbon = Carbon::parse($record->date);

        foreach ($breaks as $break) {
            if (!empty($break['start_at']) && !empty($break['end_at'])) {
                $startAt = Carbon::createFromFormat('Y-m-d H:i', $dateCarbon->format('Y-m-d') . ' ' . $break['start_at']);
                $endAt   = Carbon::createFromFormat('Y-m-d H:i', $dateCarbon->format('Y-m-d') . ' ' . $break['end_at']);

                $record->breakTimes()->create([
                    'start_at' => $startAt,
                    'end_at'   => $endAt,
                ]);
            }
        }

        return redirect()->route('attendance.list')->with('success', '勤怠情報を更新しました。');
    }

    public function requestList()
    {
        $user = Auth::guard('employee')->user();

        $pendingRecords = AttendanceRecord::where('user_id', $user->id)
            ->where('status', 'pending_correction')
            ->orderBy('date', 'desc')
            ->get();

        $approvedRecords = AttendanceRecord::where('user_id', $user->id)
            ->where('status', 'corrected')
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.request_list', compact('pendingRecords', 'approvedRecords'));
    }
}
