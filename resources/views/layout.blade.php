<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Supervisor{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('vendor/supervisor/img/favicon.png')}}">
</head>

<body style="background-color: #ececec; ">
    <div id="app"></div>
    <script>
        window.basePath = '{{ $supervisorBasePath }}'
        window.supervisorConfig = @json($config)
    </script>
    <script src="{{asset(mix('app.js', 'vendor/supervisor'))}}"></script>
</body>

</html>
