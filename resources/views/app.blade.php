<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">

    <title inertia>{{ config('app.name', 'Perfumarte') }}</title>

    <!-- Scripts -->
    <script type="text/javascript">
        window.Laravel = {
            csrfToken: "{{ csrf_token() }}",
            jsPermissions: {!! auth()->check() ? auth()->user()->jsPermissions() : 0 !!}
        };
    </script>
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue?import&raw??"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>