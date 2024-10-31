<nav class="main-header navbar navbar-expand navbar-primary navbar-dark border-bottom-0"
    style="background-color: #8266a9">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <div class="d-flex align-items-center nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <div class="cursor-pointer symbol symbol-circle symbol-35px symbol-md-40px">
                    <div class="symbol-label fs-3" style="background-color: #674d86">
                        {{ substr(Auth::user()->nama_user, 0, 1) }}</div>
                </div>
            </div>
            <div class="dropdown-menu dropdown-menu-right mt-3">
                <div class="dropdown-item user-header text-left">
                    <div class="d-flex align-items-center py-2">
                        <div class="symbol symbol-50px symbol-circle mr-2" style="border: 1px solid #674d86;">
                            <div class="symbol-label fs-3" style="background-color: rgba(103, 77, 134, 0.5); ">
                                {{ substr(Auth::user()->nama_user, 0, 1) }}</div>
                        </div>
                        <div class="d-block">
                            <span class="badge badge-color fw-bold">{{ auth()->user()->role->nama_role }}</span>
                            <div class="fw-semibold d-flex align-items-center fs-5"
                                style="word-wrap: break-word; max-width: 180px;">{{ Auth::user()->nama_user }}</div>
                        </div>
                    </div>
                </div>
                <div class="dropdown-item">
                    <div class="menu-item">
                        <a href="" class="menu-link">Ubah Password</a>
                    </div>
                    <div class="menu-item">
                        <form method="GET" action="{{ route('logout') }}">
                            @csrf
                            <a href="route('logout')" onclick="event.preventDefault();
                                this.closest('form').submit();" class="menu-link">
                                        Keluar
                            </a>
                        </form>
                    </div>
                    {{-- @if (auth()->user()->role->nama_role == 'Pemilik')
                        <a href="" class="btn btn-primary"><i class="nav-icon fas fa-cog"></i>Setting</a>
                    @endif
                    <a href="#" class="btn btn-danger"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="fas fa-sign-out-alt"></i> Keluar</a> --}}
                </div>
                {{-- <form action="{{ route('logout') }}" method="GET" id="logout-form" class="d-none">
                    @csrf
                </form> --}}
            </div>
        </li>
    </ul>
</nav>
