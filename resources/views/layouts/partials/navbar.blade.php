<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand mx-2" href="/"> Blade Templating </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/students">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/class">Class</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/extracurricular">Extracurricular</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/teachers">Teacher</a>
                </li>
                {{-- @if (Auth::user()->role_id == 1)
            <li class="nav-item">
                <a class="nav-link" href="/users">User</a>
            </li>
            @endif --}}
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    {{-- <a class="nav-link mx-5"">You are {{Auth::user()->role->name}}</a> --}}
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>