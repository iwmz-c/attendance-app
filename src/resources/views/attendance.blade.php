@extends('layouts.default')

@section('title','勤怠登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

@include('components.header')

<h1>勤怠登録画面</h1>

@endsection