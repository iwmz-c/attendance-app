<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        $start = Carbon::now()->subMonths(2)->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        foreach ($users as $user) {

            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                if ($date->isWeekend()) {
                    continue;
                }

                if (rand(1, 5) === 1) {
                    continue;
                }

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $date->toDateString(),
                    'clock_in_at' => $date->copy()->setTime(9, 0),
                    'clock_out_at' => $date->copy()->setTime(18, 0),
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start_at' => $date->copy()->setTime(12, 0),
                    'break_end_at' => $date->copy()->setTime(13, 0),
                ]);
            }
        }
    }
}
