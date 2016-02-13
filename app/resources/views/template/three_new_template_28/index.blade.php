@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
<meta name="viewport" content="width=device-width">
<link href='http://fonts.googleapis.com/css?family=Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
    
<main class="full-container" >
  <section class="container">
    <header class="logobox bordercolor">
<img src="/templates/secondnew_template_03/images/logo.jpg" />  
</header>
    <!-- header ends-->
    
    
    <section class="headingbox bordercolor">
      <h1>Get Instant Access To My Never Before <br>
        Seen "Profit Playbook"</h1>
      <span class="link">Check Out This Shocking Report</span> </section>
    
    
    <section class="imagehere_2">
      <img src="/templates/secondnew_template_03/images/no_img.jpg" />
     </section>
    
    <section class="msg-box"> <span class="arrow-left"></span> <span class="link">Click here to send us an<br>
      email for Instant Access</span> <span class="arrow-right"></span>
    </section>
    <!-- button ends-->
    
    
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