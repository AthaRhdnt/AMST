@push('css')
@endpush
<nav class="main-header navbar navbar-expand navbar-primary navbar-dark border-bottom-0" style="background-color: #8266a9">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="
                {{-- {{ url(auth()->user()->foto ?? '/img/user.jpg') }} --}}
                " class="user-image img-profil rounded-circle" alt="Pict">
                <div class="d-inline">
                    {{-- {{ auth()->user()->name }} --}}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-item user-header text-center">
                    <img src="
                    {{-- {{ url(auth()->user()->foto ?? '/img/user.jpg') }} --}}
                    " class="img-circle img-profil mt-2" alt="User Image" style="width: 80px; height: 80px;">
                    <h5 class="mt-2">
                        {{-- {{ auth()->user()->name }} --}}Name
                    </h5>
                    <p class="mb-0 text-uppercase text-bold">
                        {{-- {{ auth()->user()->role }} --}}Role
                    </p>
                    {{-- <p class="mb-0 font-italic">{{ auth()->user()->email }}</p> --}}
                </div>
                <div class="dropdown-footer">
                    <a href="
                    {{-- {{ route('user.profil') }} --}}
                    " class="btn btn-primary"><i class="nav-icon fas fa-user-edit"></i> Profil</a>
                    <a href="#" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </div>
                <form action="{{ route('logout') }}" method="GET" id="logout-form" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
@push('scripts')
<script>
    $(document).ready(function() {
        $('.dropdown-menu').on('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
@endpush