<header class="header">
    <div class="header__logo">
        <a href="/"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
    </div>
    @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )
    <nav class="header__nav">
        <ul>
            @if(Auth::guard('admin')->check())
                <li><a href="/admin/attendance/list">勤怠一覧</a></li>
                <li><a href="/admin/staff/list">スタッフ一覧</a></li>
                <li><a href="/stamp_correction_request/list">申請一覧</a></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="post">
                        @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
            @elseif(Auth::check())
                <li><a href="/attendance">勤怠</a></li>
                <li><a href="/attendance/list">勤怠一覧</a></li>
                <li><a href="/stamp_correction_request/list">申請</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
            @endif
        </ul>
    </nav>
    @endif
</header>