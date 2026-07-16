<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Unibs CRM')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- Custom -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

    @stack('styles')

    <style>
        .sidebar {
            width: 220px;
            min-width: 220px;
            min-height: 100vh;
            flex-shrink: 0;
            transition: width 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
            min-width: 80px;
        }

        .sidebar.collapsed span {
            display: none;
        }

        .content {
            flex: 1;
            min-width: 0;
            overflow-x: hidden;
        }

        .content.expanded {
            flex: 1;
        }
    </style>
</head>

<body>

    @if (isset($hideLayout) && $hideLayout === true)
        <div class="d-flex align-items-center justify-content-center min-vh-100">
            @yield('content')
        </div>
    @else
        <div class="d-flex">

            <aside class="sidebar" id="sidebar">
                <!-- sidebar content -->
                @include('partials.sidebar')
            </aside>

            <div class="content" id="mainContent">
                @include('partials.navbar')

                <main class="px-4 py-3">
                    @yield('content')
                </main>
            </div>
        </div>
    @endif

    <!-- ✅ jQuery (REQUIRED) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            $('#toggleSidebar').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                $('#mainContent').toggleClass('expanded');

                // Toggle icon
                $('#toggleIcon').toggleClass('fa-arrow-right fa-arrow-left');
            });

            // Auto collapse after 10 seconds
            // setTimeout(function() {
            //     $('#sidebar').addClass('collapsed');
            //     $('#mainContent').addClass('expanded');

            //     $('#toggleIcon')
            //         .removeClass('fa-arrow-left')
            //         .addClass('fa-arrow-right');
            // }, 10000); // 10000ms = 10 seconds

        });
    </script>

    @stack('scripts')

</body>

</html>
