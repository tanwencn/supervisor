<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Supervisor{{ config('app.name') ? ' - ' . config('app.name') : '' }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>

<body style="background-color: #ececec; ">
    <div id="app"></div>
    <!-- built files will be auto injected -->
    <script src="{{asset('js/app.js')}}"></script>
</body>

</html>