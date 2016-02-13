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
    <header class="logobox bordercolor"><img src="/templates/secondnew_template_02/images/logo.jpg" /> </header>
    <!-- header ends-->
    
    
    <section class="imagehere_2">
    <img src="/templates/secondnew_template_02/images/no_img.jpg" /> 
     </section>
    
    <section class="msg-box"> <span class="arrow-left"></span> <span class="link">Click here to send us an<br>
      email for Instant Access</span> <span class="arrow-right"></span>
    </section>
    <!-- button ends-->
    
    <section class="contentbox bordercolor">
      <div>
        <p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
        <p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but</p>
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