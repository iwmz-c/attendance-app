<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminAttendanceUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request) {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : now()->startOfDay();

        $prevDate = $date->copy()->subDay()->toDateString();
        $nextDate = $date->copy()->addDay()->toDateString();

        $users = User::query()
            ->with(['attendances' => function ($query) use ($date) {
                $query->whereDate('work_date', $date->toDateString())->with('breakTimes');
            }])->orderBy('name')->get();

        return view('admin.attendance_list', compact('users', 'date', 'prevDate', 'nextDate'));
    }

    public function show(User $user, string $date) {
        $day = Carbon::parse($date);

        $attendance = Attendance::with(['breakTimes', 'correctionRequests'])
            ->where('user_id', $user->id)
            ->whereDate('work_date', $day->toDateString())
            ->first();
        
        $isPending = $attendance
            ? $attendance->correctionRequests()->where('status', 'pending')->exists()
            : false;

        return view('admin.attendance_detail', compact('user', 'day', 'attendance', 'isPending'));
    }

    public function update(AdminAttendanceUpdateRequest $request, User $user, string $date) {
        $day = Carbon::parse($date);

        $attendance = Attendance::with('correctionRequests')->firstOrNew([
            'user_id' => $user->id,
            'work_date' => $day->toDateString(),
        ]);

        if ($attendance->exists && $attendance->correctionRequests()->where('status', 'pending')->exists()) {
            return redirect()->route('admin.attendance.show', [
                'user' => $user->id,
                'date' => $day->toDateString(),
            ])
            ->withErrors(['message' => '承認待ちのため修正はできません。',]);
        }

        $attendance->clock_in_at = $request->requested_clock_in_at
            ? $day->copy()->setTimeFromTimeString($request->requested_clock_in_at)
            : null;

        $attendance->clock_out_at = $request->requested_clock_out_at
            ? $day->copy()->setTimeFromTimeString($request->requested_clock_out_at)
            : null;

        $attendance->note = $request->requested_note;
        $attendance->save();

        $attendance->breakTimes()->delete();

        foreach ($request->input('breaks', []) as $break) {
            $start = $break['start'] ?? null;
            $end = $break['end'] ?? null;

            if (!$start && !$end) {
                continue;
            }

            $attendance->breakTimes()->create([
                'break_start_at' => $start
                    ? $day->copy()->setTimeFromTimeString($start)
                    : null,
                'break_end_at' => $end
                    ? $day->copy()->setTimeFromTimeString($end)
                    : null,
            ]);
        }

        return redirect()
            ->route('admin.attendance.show', ['user' => $user->id, 'date' => $day->toDateString()])
            ->with('message', '勤怠を修正しました。');
    }
}
