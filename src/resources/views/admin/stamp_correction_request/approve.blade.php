@extends('layouts.default')

@section('title', '申請承認')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="attendance-detail">
    <div class="attendance-detail__inner">
        <h1 class="attendance-detail__title">勤怠詳細</h1>

        <table class="attendance-detail-table">
            <tbody>
                <tr>
                    <th>名前</th>
                    <td>{{ $correctionRequest->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($correctionRequest->work_date)->format('Y年n月j日') }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        {{ optional($correctionRequest->requested_clock_in_at)->format('H:i') ?? '' }}
                        〜
                        {{ optional($correctionRequest->requested_clock_out_at)->format('H:i') ?? '' }}
                    </td>
                </tr>

                @foreach($correctionRequest->breaks as $index => $correctionRequestBreak)
                    <tr>
                        <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                        <td>
                            {{ $correctionRequestBreak->break_start_at ? \Carbon\Carbon::parse($correctionRequestBreak->break_start_at)->format('H:i') : '' }}
                            〜
                            {{ $correctionRequestBreak->break_end_at ? \Carbon\Carbon::parse($correctionRequestBreak->break_end_at)->format('H:i') : '' }}
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <th>備考</th>
                    <td>{{ $correctionRequest->requested_note }}</td>
                </tr>
            </tbody>
        </table>

        @if($correctionRequest->status === 'pending')
            <form method="POST" action="{{ route('admin.stamp_correction_request.approve.update', ['correctionRequest' => $correctionRequest->id]) }}" novalidate>
                @csrf
                <div class="attendance-detail__button-wrapper">
                    <button type="submit" class="attendance-detail__button">
                        承認
                    </button>
                </div>
            </form>
        @else
            <div class="attendance-detail__button-wrapper">
                <button type="button" class="attendance-detail__button attendance-detail__button--approved" disabled>
                    承認済み
                </button>
            </div>
        @endif
    </div>
</div>

@endsection