<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request) 
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $days = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $days->push($d->copy());
        }

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', auth()->id())
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($a) => Carbon::parse($a->work_date)->toDateString());

        $prevMonth = $start->copy()->subMonth()->format('Y-m');
        $nextMonth = $start->copy()->addMonth()->format('Y-m');
    
        return view('attendance_list', compact('month', 'days', 'attendances', 'prevMonth', 'nextMonth'));
    }

    public function create() 
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance) {
            $status = 'before_work';
        } elseif ($attendance->clock_out_at) {
            $status = 'after_work';
        } elseif (
            $attendance->breakTimes()
                ->whereNull('break_end_at')
                ->exists()
        ) {
            $status = 'on_break';
        } else {
            $status = 'working';
        }

        $now = now();

        return view('attendance', compact('status', 'attendance', 'now'));
    }

    public function detail($id, Request $request)
    {
        if ((int)$id === 0) {
            $date = $request->query('date');
            $day = \Carbon\Carbon::createFromFormat('Y-m-d', $date);

            $attendance = null;
        } else {
            $attendance = Attendance::with(['breakTimes','user'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);

            $day = $attendance->work_date;
        }

        $pendingRequest = CorrectionRequest::where('user_id', auth()->id())
            ->whereDate('work_date', $day->toDateString())
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('attendance_detail', compact('attendance', 'day', 'pendingRequest'));
    }

    public function start()
    {
        $exists = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->exists();
        if ($exists) {
            return redirect()->route('attendance.create');
        }

        Attendance::create([
            'user_id' => auth()->id(),
            'work_date' => today(),
            'clock_in_at' => now(),
        ]);

        return redirect()->route('attendance.create');
    }

    public function end()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance || $attendance->clock_out_at) {
            return redirect()->route('attendance.create');
        }

        $attendance->update([
            'clock_out_at' => now(),
        ]);

        return redirect()->route('attendance.create');
    }

    public function breakStart()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance || $attendance->clock_out_at) {
            return redirect()->route('attendance.create');
        }

        $onBreak = $attendance->breakTimes()
            ->whereNull('break_end_at')
            ->exists();

        if ($onBreak) {
            return redirect()->route('attendance.create');
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => now(),
        ]);

        return redirect()->route('attendance.create');
    }

    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if (!$attendance || $attendance->clock_out_at) {
            return redirect()->route('attendance.create');
        }

        $break = $attendance->breakTimes()
            ->whereNull('break_end_at')
            ->latest('break_start_at')
            ->first();

        if (!$break) {
            return redirect()->route('attendance.create');
        }

        $break->update([
            'break_end_at' => now(),
        ]);

        return redirect()->route('attendance.create');
    }

}
