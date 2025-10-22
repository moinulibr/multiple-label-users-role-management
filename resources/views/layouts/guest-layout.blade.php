<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ $title ?? 'Auth | Admin Panel' }}</title>

    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
    
    <!-- CORE CSS FILES (Laravel Asset Path) -->
    <link href="{{ asset('assets/admin/plugins/material/css/materialdesignicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/simplebar/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/nprogress/nprogress.css') }}" rel="stylesheet" />
    
    <!-- MONO CSS -->
    <link id="main-css-href" rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}" />

    <!-- FAVICON -->
    <link href="{{ asset('assets/admin/images/favicon.png') }}" rel="shortcut icon" />

    <!-- NProgress Script -->
    <script src="{{ asset('assets/admin/plugins/nprogress/nprogress.js') }}"></script>
    
    @stack('css')

</head>
<body class="bg-light-gray" id="body">
    <script>
        NProgress.configure({ showSpinner: false });
        NProgress.start();
    </script>

    <!-- main- html wrapper classes (template provided)-->
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh">
        <div class="d-flex flex-column justify-content-between">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-10">
                    <!-- Login and Sign Up Form will be loaded here -->
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- CORE JAVASCRIPT FILES (Laravel Asset Path) -->
    <script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/toaster/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/mono.js') }}"></script>
    <script src="{{ asset('assets/admin/js/custom.js') }}"></script>

    @stack('script')
</body>
</html>
