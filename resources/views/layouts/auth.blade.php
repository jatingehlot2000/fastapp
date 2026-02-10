<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wowdash - Sign In</title>
    <link rel="icon" type="image/png" href="{{ asset('backend_assets/images/favicon.png') }}" sizes="16x16">
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="{{ asset('backend_assets/css/remixicon.css') }}">
    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('backend_assets/css/lib/bootstrap.min.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('backend_assets/css/style.css') }}">
    @livewireStyles
</head>

<body>

    {{ $slot }}

    <!-- jQuery library js -->
    <script src="{{ asset('backend_assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('backend_assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('backend_assets/js/lib/iconify-icon.min.js') }}"></script>
    <!-- main js -->
    <script src="{{ asset('backend_assets/js/app.js') }}"></script>

    <script>
        // Password Show Hide
        function initializePasswordToggle(toggleSelector) {
            $(toggleSelector).on('click', function() {
                $(this).toggleClass("ri-eye-off-line");
                var input = $($(this).attr("data-toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }
        initializePasswordToggle('.toggle-password');
    </script>

    @livewireScripts
</body>

</html>
