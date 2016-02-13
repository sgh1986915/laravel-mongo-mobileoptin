<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="cleartype" content="on">
    <meta name="mobile-web-app-capable" content="yes">
    @yield('header','')

</head>
<body>
@yield('content','')
<script type="text/javascript">
    var base_url = '{{URL::to('/')}}';
</script>
<script type="text/javascript" src="{{URL::to('/embed/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::to('/embed/js/notify.min.js')}}"></script>
</body>
</html>