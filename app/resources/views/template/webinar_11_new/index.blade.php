@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href='https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Oswald:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
    
<header class="full gray-bg">
  <h1>Lorem ipsum dolor <span>sit amet</span>,  adipiscing elit.</h1>
  <div class="middle_section">
    <p>Lorem ipsum dolor sit amet,  adipiscing elit. Suspendisse sit amet lobortis justo, in mattis augue.</p>
  </div>
</header>
<section class="full colandar-section">
  <div class="middle_section">
    <div class="calender"><img src="/templates/webinar_11_new/images/calender_icon.png" width="82" height="82" alt=""></div>
    <div class="date_text">Thursday 01, October 2015</div>
    <div class="date_time">3pm Pacific / 6pm Eastern</div>
    <div class="registerbutton"><span class="link">Reserve My Spot Now!</span></div>
  </div>
</section>
<section class="full gray-box">
  <div class="middle_section">
    <div class="name_group">
      <h2><span>Host Name</span></h2>
      <div class="picture_1"><img src="/templates/webinar_11_new/images/man_pic.png" width="167" height="158" alt=""></div>
      <h4>Name of the Author</h4>
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
    </div>
  </div>
</section>

<div class="full footer-gray">
  <div class="warning-img"><img src="/templates/webinar_11_new/images/warning_img.png" alt=""> <span><strong>Warning:</strong> Limited Space Available</span> </div>
  <div class="middle_section">
    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.Ipsum has been the industry's standard dummy text ever since when specimen book.</p>
    <div class="registerbutton registerbutton2"><span class="link">Reserve My Spot Now!</span></div>
  </div>
</div>

<footer id="footer">
    <nav>
      <ul>
        <li><a target="_blank" class="therms_of_s" href="{{$therms_of_link}}">Terms of Services</a> </li>
        <li><a target="_blank" class="privacy" href="{{$privacy_link}}"> privacy Policy</a> </li>
        <li><a target="_blank" class="contact_us" href="{{$contact_us_link}}"> Contact us</a></li>
      </ul>
    </nav>
  </footer>

@endsection