<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

    @yield('login')
    @yield('reset')

    @unless(View::hasSection('login') || View::hasSection('reset')) 
        <!-- Wrapper -->
        <div class="wrapper">
                @include('layouts.partials.header')
                @include('layouts.partials.sidebar')

                <!-- Content Wrapper -->
                <div class="content-wrapper pt-3">
                    <!-- Main Content -->
                    <section class="content">
                        @yield('content')
                    </section>
                    <!-- /.content -->
                </div>
                <!-- /.content-wrapper -->
                @include('layouts.partials.footer')
        </div>
        <!-- ./wrapper -->
        @include('layouts.partials.script')
    @endunless
</body>
</html>
