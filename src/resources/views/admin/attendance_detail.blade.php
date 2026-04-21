@extends('layouts.default')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="attendance-detail">
    <div class="attendance-detail__inner">
        <h1 class="attendance-detail__title">勤怠詳細</h1>

        <form method="POST" action="{{ route('admin.attendance.update', ['user' => $user->id, 'date' => $day->toDateString()]) }}" novalidate>
        @csrf

        <input type="hidden" name="work_date" value="{{ $day->toDateString() }}">
        <input type="hidden" name="attendance_id" value="{{ $attendance?->id }}">

        <table class="attendance-detail-table">
            <tbody>
                <tr>
                    <th>名前</th>
                    <td>{{ $user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>{{ $day->isoFormat('YYYY年') }}　　{{ $day->isoFormat('M月D日') }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time"
                            name="requested_clock_in_at"
                            value="{{ old('requested_clock_in_at', $attendance?->clock_in_at?->format('H:i')) }}">
                        <span class="attendance-detail-table__separator">〜</span>
                        <input type="time"
                            name="requested_clock_out_at"
                            value="{{ old('requested_clock_out_at', $attendance?->clock_out_at?->format('H:i')) }}">
                        @error('requested_clock_in_at')
                            <p class="error">{{ $message }}</p>
                        @enderror
                        @error('requested_clock_out_at')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>

                @php
                    $breaks = $attendance?->breakTimes ?? collect();
                    $count = $breaks->count();
                    $rows = $count + 1;
                @endphp

                @for($i = 0; $i < $rows; $i++)
                    @php
                        $b = $breaks->get($i);
                        $label = $i === 0 ? '休憩' : '休憩' . ($i + 1);
                        $startKey = "breaks.$i.start";
                        $endKey   = "breaks.$i.end";
                    @endphp

                    <tr>
                        <th>{{ $label }}</th>
                        <td>
                            <input type="time"
                                name="breaks[{{ $i }}][start]"
                                value="{{ old($startKey, $b?->break_start_at?->format('H:i')) }}">
                            <span class="attendance-detail-table__separator">〜</span>
                            <input type="time"
                                name="breaks[{{ $i }}][end]"
                                value="{{ old($endKey, $b?->break_end_at?->format('H:i')) }}">

                            @error("breaks.$i.start")
                                <p class="error">{{ $message }}</p>
                            @enderror
                            @error("breaks.$i.end")
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                @endfor

                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="requested_note">{{ old('requested_note', $attendance?->note) }}</textarea>
                        @error('requested_note')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="attendance-detail__actions">
            <button type="submit" class="attendance-detail__button">修正</button>
        </div>

        @error('message')
            <p class="attendance-detail__message-error">{{ $message }}</p>
        @enderror
        </form>
    </div>
</div>

@endsection

