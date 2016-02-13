@extends('layouts/mobileoptin')
@section('header')
<meta name="viewport" content="width=device-width">
    <title>{{$title}}</title>
<link href='http://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
  

<main class="full-container" >
  <section class="container">
    <header class="logobox bordercolor"> <img src="/templates/imagetemplate_6_1/images/logo.jpg" /></header>
    <!-- header ends-->
    
    <section class="headingbox bordercolor">
      <h1>Get Instant Access To My Never Before <br>
        Seen "Profit Playbook"</h1>
      <span class="link">Check Out This Shocking Report</span> </section>
    <section class="msg-box"> <span class="arrow-left"></span> <span class="link">Click here to send us an<br>
      email for Instant Access</span> <span class="arrow-right"></span> </section>
    <!-- button ends-->
    
    <section class="contentbox bordercolor">
      <h2>Title</h2>
      <div class="imagehere"> <img src="/templates/imagetemplate_6_1/images/no_img.jpg" /> </div>
      <div>
        <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially </p>
      </div>
    </section>
    <footer>
      <nav>
        <ul>
          <li><a target="_blank" class="therms_of_s" href="{{$therms_of_link}}">Terms of Services</a> </li>
          <li><a target="_blank" class="privacy" href="{{$privacy_link}}"> privacy Policy</a> </li>
          <li><a target="_blank" class="contact_us" href="{{$contact_us_link}}"> Contact us</a></li>
        </ul>
      </nav>
    </footer>
    <!-- footer ends--> 
    
  </section>
</main>


@endsection