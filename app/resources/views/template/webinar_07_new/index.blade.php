@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href='https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
  
<header class="full gray-bg">
  <div class="top-section">
    <div class="middle_section">
      <h2>Replace your difult head line 
        with your page headline</h2>
    </div>
  </div>
  <div class="middle-container">
    <div class="middle_section">
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ut viverra magna. Suspendisse sit amet lobortis justo, in mattis augue.</p>
      <div class="registerbutton"><span class="link">Click Here To Save Your Spot</span> <span class="arrow-left"></span> <span class="arrow-right"></span> </div>
    </div>
  </div>
</header>
<section class="full colandar-section">
  <div class="middle_section">
    <div class="date_text">Wednesday 30, September 2015</div>
    <div class="date_time">3pm Pacific / 6pm Eastern</div>
    <div class="calender"><img src="/templates/webinar_07_new/images/calender_icon.png" width="82" height="82" alt=""></div>
  </div>
</section>
<section class="full gray-box">
  <div class="middle_section">
    <div class="name_group">
      <div class="picture_1"><img src="/templates/webinar_07_new/images/man_pic.png" width="136" height="126" alt=""></div>
      <h2><span>Host Name</span></h2>
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
    </div>
    <div class="name_group">
      <div class="picture_1"><img src="/templates/webinar_07_new/images/man_pic01.png" width="136" height="126" alt=""></div>
      <h2><span>Co-Host Name</span></h2>
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
    </div>
  </div>
</section>
<section class="full white-box">
  <div class="middle_section">
    <h2><span>You’ar Going to Learn
      About Webinar</span></h2>
    <ul>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting industry. Lorem Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting industry. Lorem Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting industry. Lorem Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting Ipsum has been the industry's standard</li>
      <li><strong>How  To</strong>: Lorem Ipsum is simply dummy text of the printing and
        typesetting industry. Lorem Ipsum has been the industry's standard</li>
    </ul>
  </div>
</section>
<div class="full footer-gray">
  <div class="registerbutton registerbutton2"><span class="link">Reserve My Spot Now!</span></div>
  <div class="warning-img"><img src="/templates/webinar_07_new/images/warning_img.png" alt=""> <span><strong>Warning:</strong><br>
    Limited Space Available</span> </div>
  <div class="middle_section">
    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
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