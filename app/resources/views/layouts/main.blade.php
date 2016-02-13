<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">

    {!! SEOMeta::generate() !!}
    <meta name="a-token" content="{{csrf_token()}}"/>
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="cleartype" content="on">

    {{--<!-- iPad and iPad mini (with @2× display) iOS ≥ 8 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="180x180" href="img/touch/apple-touch-icon-180x180-precomposed.png">--}}
    {{--<!-- iPad 3+ (with @2× display) iOS ≥ 7 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="152x152" href="img/touch/apple-touch-icon-152x152-precomposed.png">--}}
    {{--<!-- iPad (with @2× display) iOS ≤ 6 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/touch/apple-touch-icon-144x144-precomposed.png">--}}
    {{--<!-- iPhone (with @2× and @3 display) iOS ≥ 7 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="120x120" href="img/touch/apple-touch-icon-120x120-precomposed.png">--}}
    {{--<!-- iPhone (with @2× display) iOS ≤ 6 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/touch/apple-touch-icon-114x114-precomposed.png">--}}
    {{--<!-- iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="76x76" href="img/touch/apple-touch-icon-76x76-precomposed.png">--}}
    {{--<!-- iPad mini and the first- and second-generation iPad (@1× display) on iOS ≤ 6 -->--}}
    {{--<link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/touch/apple-touch-icon-72x72-precomposed.png">--}}
    {{--<!-- Android Stock Browser and non-Retina iPhone and iPod Touch -->--}}
    {{--<link rel="apple-touch-icon-precomposed" href="img/touch/apple-touch-icon-57x57-precomposed.png">--}}
    {{--<!-- Fallback for everything else -->--}}
    {{--<link rel="shortcut icon" href="img/touch/apple-touch-icon.png">--}}

    {{--<!----}}
    {{--Chrome 31+ has home screen icon 192×192 (the recommended size for multiple resolutions).--}}
    {{--If it’s not defined on that size it will take 128×128.--}}
    {{---->--}}
    {{--<link rel="icon" sizes="192x192" href="img/touch/touch-icon-192x192.png">--}}
    {{--<link rel="icon" sizes="128x128" href="img/touch/touch-icon-128x128.png">--}}

    {{--<!-- Tile icon for Win8 (144x144 + tile color) -->--}}
    {{--<meta name="msapplication-TileImage" content="img/touch/apple-touch-icon-144x144-precomposed.png">--}}
    {{--<meta name="msapplication-TileColor" content="#222222">--}}


    <meta name="mobile-web-app-capable" content="yes">
    <style type="text/css">
        <?php
         $css1=  File::get( public_path(). Minify::stylesheet([
           '/css/vendors/bootstrap.css',
           '/css/vendors/bootstrap-dialog.css',
           '/css/vendors/bootstrap-theme.css',
           '/css/vendors/bootstrap-switch.min.css',
           '/css/vendors/daterangepicker-bs3.css',
           '/css/vendors/jasny-bootstrap.css',
           '/css/vendors/jquery.dataTables.css',
           '/css/vendors/jquery.dataTables_themeroller.css',
           '/css/vendors/jquery-ui.css',
           '/css/vendors/nprogress.css',

         ]
         )->onlyUrl());
             $css1=  str_replace('../fonts/',URL::to('/fonts/').'/',$css1);
             $css1=   str_replace('../images/',URL::to('/images/').'/',$css1);
             echo  str_replace('../img/',URL::to('/img/').'/',$css1);
        ?>
 <?php
         $css1=  File::get( public_path(). Minify::stylesheet(
         ['/css/template.css']
         )->onlyUrl());
             $css1=  str_replace('../fonts/',URL::to('/fonts/').'/',$css1);
             $css1=   str_replace('../images/',URL::to('/images/').'/',$css1);
             echo  str_replace('../img/',URL::to('/img/').'/',$css1);
        ?>


    </style>
</head>
<body>
@if(isset($admin_navigation))
    @include('include.admin.navigation')
@else
    @include('include.navigation')
@endif
<div class="container">
    @include('include.notification')

    @yield('content')
</div>

<script type="text/javascript">
    var base_url = '{{URL::to('/')}}';
    var CKEDITOR_BASEPATH = '{{URL::to('/ckeditor/')}}/';

    <?php

    $js_file_Path_1=Minify::javascript(
       [
           '/js/jquery-1.11.3.js',
               '/js/jquery-ui.js',

        ]
   )->onlyUrl();


   $js=  File::get( public_path(). $js_file_Path_1);
       echo  $js;


  ?>

</script>
<script type="text/javascript" data-turbolinks-eval="false" src="{{URL::to('/ckeditor/ckeditor.js')}}"></script>

<script type="text/javascript" data-turbolinks-eval="false">

    <?php

               $js_file_Path_3=Minify::javascript(
                  [
                     '/js/helpers.js',            '/js/Chart.min.js',



                  ]
              )->onlyUrl();
              $js3=  File::get( public_path(). $js_file_Path_3);

           echo  $js3;
             ?>

</script>

<script type="text/javascript" data-turbolinks-eval="false">

    <?php

    $js_file_Path_2=Minify::javascript(
       [
           '/js/jquery.tmpl.js',
           '/js/datatables.js',
           '/js/bootstrap.js',
           '/js/bootstrap-switch.min.js',
           '/js/moment.js',
           '/js/daterangepicker.js',
           '/js/ZeroClipboard.js',
           '/js/jasny-bootstrap.js',
           '/js/jquery.ajaxQueue.js',
           '/js/bootstrap-dialog.js',
           '/js/nprogress.js',
           '/js/jquery.ddslick.min.js',
		   '/js/jquery.validate.min.js',
		   '/js/additional-methods.min.js',	
           '/js/custom.js',

       ]
   )->onlyUrl();
   $js2=  File::get( public_path(). $js_file_Path_2);
       echo  $js2;


  ?>


$("[name='active']").bootstrapSwitch();
</script>

@yield('javascript','')


</body>
</html>