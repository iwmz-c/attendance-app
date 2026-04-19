@extends('layouts.default')

@section('title', '申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/stamp_correction_request_list.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="request-list">
    <div class="request-list__inner">
        <h1 class="request-list__title">申請一覧</h1>

        <div class="request-list__tabs">
            <a
                href="{{ route('admin.stamp_correction_request.list', ['status' => 'pending']) }}"
                class="request-list__tab {{ $status === 'pending' ? 'is-active' : '' }}"
            >
                承認待ち
            </a>

            <a
                href="{{ route('admin.stamp_correction_request.list', ['status' => 'approved']) }}"
                class="request-list__tab {{ $status === 'approved' ? 'is-active' : '' }}"
            >
                承認済み
            </a>
        </div>

        <div class="request-list__table-wrap">
            <table class="request-list__table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($correctionRequests as $correctionRequest)
                        <tr>
                            <td>{{ $correctionRequest->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                            <td>{{ $correctionRequest->user->name ?? '' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($correctionRequest->work_date)->format('Y/m/d') }}
                            </td>
                            <td>{{ $correctionRequest->requested_note }}</td>
                            <td>{{ $correctionRequest->created_at->format('Y/m/d') }}</td>
                            <td>
                                <a href="{{ route('admin.stamp_correction_request.approve', ['correctionRequest' => $correctionRequest->id]) }}">
                                    詳細
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">申請はありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection