<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('date'))
            : \Carbon\Carbon::today();

        $records = AttendanceRecord::with('user', 'breakTimes')
            ->whereDate('date', $date->format('Y-m-d'))
            ->get();

        return view('admin.attendance.index', compact('records', 'date'));
    }


    public function monthly($id, $year, $month = null)
    {
        $month = $month ?? now()->month;
        $date = Carbon::create($year, $month, 1);

        $user = User::findOrFail($id);

        $records = AttendanceRecord::with('breakTimes')
            ->where('user_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.attendance.monthly', [
            'user' => $user,
            'records' => $records,
            'currentMonth' => $date
        ]);
    }

    public function detail($id)
    {
        $record = AttendanceRecord::with(['user', 'breakTimes'])->findOrFail($id);
        $date = $record->date;

        return view('admin.attendance.detail', compact('record', 'date'));
    }

    public function update(Request $request, $id)
    {
        $attendance = AttendanceRecord::with('breakTimes')->findOrFail($id);

        $validated = $request->validate([
            'checkin_at' => 'required|date_format:H:i',
            'checkout_at' => 'required|date_format:H:i|after:checkin_at',
            'breaks' => 'array',
            'breaks.*.start_at' => 'required_with:breaks.*.end_at|date_format:H:i',
            'breaks.*.end_at' => 'required_with:breaks.*.start_at|date_format:H:i|after:breaks.*.start_at',
            'note' => 'nullable|string',
        ]);

        $date = $attendance->date->format('Y-m-d');

        $checkin = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validated['checkin_at']);
        $checkout = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validated['checkout_at']);

        if (!empty($validated['breaks'])) {
            foreach ($validated['breaks'] as $break) {
                $breakStart = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $break['start_at']);
                $breakEnd = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $break['end_at']);
                if ($breakStart->lt($checkin) || $breakEnd->gt($checkout)) {
                    return back()->withErrors(['breaks' => 'Break times must be within check-in and check-out times.'])->withInput();
                }
            }
        }

        $attendance->checkin_at = $checkin;
        $attendance->checkout_at = $checkout;
        $attendance->note = $validated['note'] ?? null;
        $attendance->status = 'corrected';
        $attendance->save();

        $attendance->breakTimes()->delete();
        if (!empty($validated['breaks'])) {
            foreach ($validated['breaks'] as $break) {
                $breakStart = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $break['start_at']);
                $breakEnd = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $break['end_at']);
                $attendance->breakTimes()->create([
                    'start_at' => $breakStart,
                    'end_at' => $breakEnd,
                ]);
            }
        }

        return redirect()->route('admin.attendance.detail', $attendance->id)
            ->with('success', '勤怠情報を更新しました');
    }

    public function csvExport($id, $year, $month = null)
    {
        $month = $month ?? now()->month;
        $user = User::findOrFail($id);

        $records = AttendanceRecord::with('breakTimes')
            ->where('user_id', $id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        $csvHeader = ['日付', '出勤', '退勤', '休憩時間', '備考'];

        $callback = function() use ($records, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($records as $record) {
                $breakTotalMinutes = $record->breakTimes->sum(function($b){
                    return \Carbon\Carbon::parse($b->end_at)->diffInMinutes(\Carbon\Carbon::parse($b->start_at));
                });

                $breakHours = intdiv($breakTotalMinutes, 60);
                $breakMinutes = $breakTotalMinutes % 60;
                $breakTimeStr = sprintf('%02d:%02d', $breakHours, $breakMinutes);

                fputcsv($file, [
                    $record->date->format('Y-m-d'),
                    $record->checkin_at ? $record->checkin_at->format('H:i') : '',
                    $record->checkout_at ? $record->checkout_at->format('H:i') : '',
                    $breakTimeStr,
                    $record->note ?? '',
                ]);
            }

            fclose($file);
        };

        $fileName = "attendance_{$user->id}_{$year}_{$month}.csv";

        return Response::stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ]);
    }
}