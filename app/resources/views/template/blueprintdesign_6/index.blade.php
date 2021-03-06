@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
<meta name="viewport" content="width=device-width">
<link href='http://fonts.googleapis.com/css?family=Lato:400,300,300italic,700,400italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
  

<section class="full-container" id="theme-6">
  <section class="container">
    <article class="content-box border">
      <h2>Get Instant Access To My Never Before<br>
        Seen <strong>"Profit Playbook"</strong></h2>
      <span class="link">Check Out This Shocking Report</span> 
      </article>
    
    <!--  content box -->
    <article class="content-box border change-font">
      <h2>CLICK THE BUTTON TO SEND US AN EMAIL</h2>
      <span class="link">And we will reply with this game changing report</span> 
      </article>
      
    <!--  content box -->
    <section class="content-box msg-box"> <span class="arrow-left"></span> <span class="link">Click here to send us an<br>
      email for Instant Access</span> <span class="arrow-right"></span> </section>
    <!--  content box -->
    <article class="content-box blue-print-box">
      <h3>Free Blueprint</h3>
      <div class="blue-print-img"></div>
    </article>
    <!--  content box --> 
  </section>
  <!--  container -->
  
  <footer id="footer">
    <nav>
      <ul>
        <li><a href="#">Terms of Services</a> </li>
        <li><a href="#"> privacy Policy</a> </li>
        <li><a href="#"> Contact us</a></li>
      </ul>
    </nav>
  </footer>
  <!-- footer ends--> 
</section>

@endsection