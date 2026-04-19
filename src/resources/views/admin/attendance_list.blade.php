@extends('layouts.default')

@section('title','日次勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin_attendance_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="admin-attendance-list">
    <div class="admin-attendance-list__heading">
        <h1 class="admin-attendance-list__title">{{ $date->isoFormat('YYYY年M月D日') }}の勤怠</h1>
    </div>

    <div class="admin-attendance-list__date-nav">
        <a
            class="date-nav__link"
            href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}"
        >
            前日
        </a>

        <span class="date-nav__current">
            {{ $date->format('Y/m/d') }}
        </span>

        <a
            class="date-nav__link"
            href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}"
        >
            翌日
        </a>
    </div>

    <table class="admin-attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                @php
                    $attendance = $user->attendances->first();

                    $clockIn = $attendance?->clock_in_at
                        ? \Carbon\Carbon::parse($attendance->clock_in_at)->format('H:i')
                        : '';

                    $clockOut = $attendance?->clock_out_at
                        ? \Carbon\Carbon::parse($attendance->clock_out_at)->format('H:i')
                        : '';

                    $breakMinutes = $attendance?->break_minutes ?? null;
                    $workMinutes = $attendance?->work_minutes ?? null;

                    $breakTime = $breakMinutes !== null
                        ? sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60)
                        : '';

                    $workTime = $workMinutes !== null
                        ? sprintf('%d:%02d', floor($workMinutes / 60), $workMinutes % 60)
                        : '';
                @endphp

                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $clockIn }}</td>
                    <td>{{ $clockOut }}</td>
                    <td>{{ $breakTime }}</td>
                    <td>{{ $workTime }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.show', ['user' => $user->id, 'date' => $date->toDateString()]) }}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection