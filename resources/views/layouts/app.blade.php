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
                        <div class="row">
                            <div class="col-sm-6">
                                <h1 class="m-0">@yield('title')</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    @section('breadcrumb')
                                        {{-- <li class="breadcrumb-item"><span class="text-uppercase text-primary text-bold">{{ auth()->user()->role }}</span></li> --}}
                                    @show
                                </ol>
                            </div>
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
