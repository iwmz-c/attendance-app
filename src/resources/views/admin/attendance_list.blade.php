@extends('layouts.default')

@section('title','日次勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

@include('components.header')

<h1>xxxx年xx月xx日の勤怠</h1>

@endsection