<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use Carbon\Carbon;

class CorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = Attendance::inRandomOrder()->take(10)->get();

        foreach ($attendances as $attendance) {

            // 承認待ち
            CorrectionRequest::create([
                'user_id' => $attendance->user_id,
                'attendance_id' => $attendance->id,
                'work_date' => $attendance->work_date,

                'requested_clock_in_at' => Carbon::parse($attendance->clock_in_at)->addMinutes(15),
                'requested_clock_out_at' => Carbon::parse($attendance->clock_out_at)->addMinutes(10),
                'requested_note' => '電車遅延のため',

                'status' => 'pending',
            ]);

            // 承認済み
            CorrectionRequest::create([
                'user_id' => $attendance->user_id,
                'attendance_id' => $attendance->id,
                'work_date' => $attendance->work_date,

                'requested_clock_in_at' => Carbon::parse($attendance->clock_in_at)->subMinutes(10),
                'requested_clock_out_at' => Carbon::parse($attendance->clock_out_at)->addMinutes(5),
                'requested_note' => '業務都合で調整',

                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => 1, // 管理者ID
            ]);
        }
    }
}
