<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $correctionRequests = CorrectionRequest::with('user')
            ->where('status', $status)
            ->latest()
            ->get();

        return view('admin.stamp_correction_request.list', compact(
            'correctionRequests',
            'status'
        ));
    }

    public function show(CorrectionRequest $correctionRequest)
    {
        $correctionRequest->load(['user', 'breaks']);

        return view('admin.stamp_correction_request.approve', compact('correctionRequest'));
    }

    public function approve(CorrectionRequest $correctionRequest)
    {
        if ($correctionRequest->status === 'approved') {
            return redirect()
                ->route('admin.stamp_correction_request.approve', $correctionRequest->id);
        }

        $correctionRequest->load('breaks');

        DB::transaction(function () use ($correctionRequest) {
            $attendance = $correctionRequest->attendance;

            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => $correctionRequest->user_id,
                    'work_date' => $correctionRequest->work_date,
                    'clock_in_at' => $correctionRequest->requested_clock_in_at,
                    'clock_out_at' => $correctionRequest->requested_clock_out_at,
                    'note' => $correctionRequest->requested_note,
                ]);
            } else {
                $attendance->update([
                    'clock_in_at' => $correctionRequest->requested_clock_in_at,
                    'clock_out_at' => $correctionRequest->requested_clock_out_at,
                    'note' => $correctionRequest->requested_note,
                ]);
            }

            $attendance->breakTimes()->delete();

            foreach ($correctionRequest->breaks as $correctionRequestBreak) {
                if (!$correctionRequestBreak->break_start_at || !$correctionRequestBreak->break_end_at) {
                    continue;
                }

                $attendance->breakTimes()->create([
                    'break_start_at' => $correctionRequestBreak->break_start_at,
                    'break_end_at' => $correctionRequestBreak->break_end_at,
                ]);
            }

            $correctionRequest->update([
                'attendance_id' => $attendance->id,
                'status' => 'approved',
                'approved_by' => Auth::guard('admin')->id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.stamp_correction_request.approve', ['correctionRequest' => $correctionRequest->id]);
    }
}
