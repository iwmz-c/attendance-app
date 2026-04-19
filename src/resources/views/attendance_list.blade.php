@extends('layouts.default')

@section('title','日次勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="attendance-list">
    <div class="attendance-list__inner">
        <h1 class="attendance-list__title">勤怠一覧</h1>

        <div class="attendance-list__month-nav">
            <a class="month-nav__link" href="{{ route('attendance.list', ['month' => $prevMonth]) }}">← 前月</a>
            <span class="month-nav__current">{{ $month }}</span>
            <a class="month-nav__link" href="{{ route('attendance.list', ['month' => $nextMonth]) }}">翌月 →</a>
        </div>

        <div class="attendance-list__table-wrap">    
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($days as $day)
                        @php
                            $key = $day->toDateString();
                            $a = $attendances->get($key);
                        @endphp

                        <tr>
                            <td>{{ $day->format('m/d') }}（{{ $day->isoFormat('ddd') }}）</td>
                            <td>{{ $a?->clock_in_at?->format('H:i') ?? '' }}</td>
                            <td>{{ $a?->clock_out_at?->format('H:i') ?? '' }}</td>
                            <td>{{ $a?->break_time_formatted ?? '' }}</td>
                            <td>{{ $a?->work_time_formatted ?? '' }}</td>
                            <td>
                                <a class="attendance-table__detail" href="{{ route('attendance.detail', ['id' => $a?->id ?? 0, 'date' => $day->toDateString()]) }}">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>    
            </table>    
        </div>
    </div>
</div>

@endsection