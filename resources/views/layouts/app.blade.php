<!DOCTYPE html>
<html lang="en">

    <head>
        @include('layouts.partials.head')
    </head>

    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
        <!-- wrapper -->
        <div class="wrapper">
            @include('layouts.partials.header')
            @include('layouts.partials.sidebar')
            <!-- content-wrapper -->
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row px-2">
                            <p class="breadcrumb float-sm-left">
                                @section('breadcrumb')
                                    <h4 class="breadcrumb-item"><span class="text-uppercase text-bold">@yield('title')</span></h4>
                                @show
                            </p>
                        </div>
                    </div>
                </section>
                <!-- Main content -->
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
    </body>

</html>
