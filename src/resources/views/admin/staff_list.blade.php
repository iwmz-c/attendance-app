@extends('layouts.default')

@section('title', 'スタッフ一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin_staff_list.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="staff-list">
    <div class="staff-list__inner">
        <h1 class="staff-list__title">スタッフ一覧</h1>

        <table class="staff-list-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.staff', ['user' => $user->id]) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection