@extends('layouts.default')

@section('title','申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/stamp_correction_request_list.css') }}">
@endsection

@section('content')
@include('components.header')

<div class="request-list">
    <div class="request-list__inner">
  		<h2 class="request-list__title">申請一覧</h2>

  {{-- タブ --}}
  		<div class="request-list__tabs">
    		<a href="{{ route('stamp_correction_request.list', ['tab' => 'pending']) }}" class="request-list__tab {{ $tab === 'pending' ? 'is-active' : '' }}">
				承認待ち
    		</a>
    		<a href="{{ route('stamp_correction_request.list', ['tab' => 'approved']) }}" class="request-list__tab {{ $tab === 'approved' ? 'is-active' : '' }}">
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
      				@forelse($requests as $r)
        				<tr>
							<td>{{ $r->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
							<td>{{ auth()->user()->name }}</td>
							<td>{{ $r->work_date?->format('Y/m/d') }}</td>
							<td>{{ $r->requested_note }}</td>
							<td>{{ $r->created_at?->format('Y/m/d') }}</td>
							<td>
								<a href="{{ route('attendance.detail', ['id' => $r->attendance_id ?? 0, 'date' => $r->work_date->toDateString()]) }}">
              						詳細
            					</a>
          					</td>
        				</tr>
      				@empty
        				<tr>
							<td colspan="6">データがありません</td>
						</tr>
      				@endforelse
    			</tbody>
  			</table>
		</div>
	</div>
  {{ $requests->links() }}
</div>
@endsection