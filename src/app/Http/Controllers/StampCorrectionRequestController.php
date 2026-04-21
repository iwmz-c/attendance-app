<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStampCorrectionRequest;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');
        $status = $tab === 'approved' ? 'approved' : 'pending';

        $requests = CorrectionRequest::where('user_id', auth()->id())
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('stamp_correction_request.list', compact('requests', 'tab'));
    }

    public function store(StoreStampCorrectionRequest $request)
    {
        $validated = $request->validated();
        $workDate = Carbon::parse($validated['work_date'])->toDateString();

        $alreadyPending = CorrectionRequest::where('user_id', auth()->id())
            ->whereDate('work_date', $workDate)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()
                ->withErrors(['message' => '承認待ちのため修正はできません。'])
                ->withInput();
        }

        $attendance = null;

        if (!empty($validated['attendance_id'])) {
            $attendance = Attendance::where('user_id', auth()->id())
                ->where('id', $validated['attendance_id'])
                ->first();
        }

        if (!$attendance) {
            $attendance = Attendance::where('user_id', auth()->id())
                ->whereDate('work_date', $workDate)
                ->first();
        }

        $breakRows = collect($validated['breaks'] ?? [])
            ->filter(fn($b) => !empty($b['start']) && !empty($b['end']))
            ->map(fn($b) => [
                'break_start_at' => $workDate . ' ' . $b['start'],
                'break_end_at'   => $workDate . ' ' . $b['end'],
            ])
            ->values()
            ->all();

        DB::transaction(function () use ($validated, $workDate, $attendance, $breakRows) {
            $cr = CorrectionRequest::create([
                'user_id' => auth()->id(),
                'attendance_id' => $attendance?->id,
                'work_date' => $workDate,
                'requested_clock_in_at' =>
                    !empty($validated['requested_clock_in_at'])
                        ? $workDate . ' ' . $validated['requested_clock_in_at']
                        : null,
                'requested_clock_out_at' =>
                    !empty($validated['requested_clock_out_at'])
                        ? $workDate . ' ' . $validated['requested_clock_out_at']
                        : null,
                'requested_note' => $validated['requested_note'],
                'status' => 'pending',
            ]);

            if (!empty($breakRows)) {
                $cr->breaks()->createMany($breakRows);
            }
        });

        $id = $attendance?->id ?? 0;

        return redirect()
            ->route('attendance.detail', ['id' => $id, 'date' => $workDate])
            ->with('message', '申請しました');
    }
}