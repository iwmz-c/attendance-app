@extends('layouts.default')

@section('title','勤怠登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="attendance">
    <p class="status">
        @if($status === 'before_work')
            勤務外
        @elseif($status === 'working')
            出勤中
        @elseif($status === 'on_break')
            休憩中
        @else
            退勤済
        @endif
    </p>

    <div class="attendance-date">
        <p class="attendance-date__day">
            {{ $now->isoFormat('YYYY年MM月DD日（ddd）') }}
        </p>
        <p class="attendance-date__time">
            {{ $now->format('H:i') }}
        </p>
    </div>



    <div class="actions">
        @if($status === 'before_work')
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button type="submit" class="action__attendance">出勤</button>
            </form>

        @elseif($status === 'working')
            <form method="POST" action="{{ route('attendance.end') }}">
                @csrf
                <button type="submit" class="action__attendance">退勤</button>
            </form>

            <form method="POST" action="{{ route('attendance.break.start') }}">
                @csrf
                <button type="submit" class="action__break">休憩入</button>
            </form>

        @elseif($status === 'on_break')
            <form method="POST" action="{{ route('attendance.break.end') }}">
                @csrf
                <button type="submit" class="action__break">休憩戻</button>
            </form>

        @else
            <p class="action-message">お疲れさまでした。</p>
        @endif
    </div>
</div>
    

@endsection