<!DOCTYPE html>
<html lang="en" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}</title>
    <script>window.APP_NAME = @json(config('app.name'));</script>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="h-full overflow-hidden bg-slate-100">
    <div id="admin-app" class="h-full"></div>
</body>
</html>
