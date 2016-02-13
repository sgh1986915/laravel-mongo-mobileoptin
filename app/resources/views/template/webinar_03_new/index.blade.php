@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href='https://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
    

<header class="full red-bg">
  <div class="middle_section">
  <div class="top_txt">Lorem ipsum dolor sit amet,  adipiscing elit. Suspendisse justo, in mattis augue.</div>
    <p>Lorem ipsum dolor sit amet,  adipiscing elit. Suspendisse sit amet lobortis justo, in mattis augue.</p>
    <div class="registerbutton"><span class="link">Click Here To Save Your Spot</span></div>
  </div>
</header>
<section class="full red-bg red-bg-1">
  <div class="middle_section">
    <div class="date_text">Wednesday 30, September 2015</div>
    <div class="date_time">3pm Pacific / 6pm Eastern</div>
    <div class="calender"><img src="/templates/webinar_03_new/images/calender_icon.png"  alt=""></div>
  </div>
</section>

<section class="full brown-box"> 
    <div class="name_group" style="background-color:#3e3a38">
     <div class="middle_section">
      <div class="picture_1"><img src="/templates/webinar_03_new/images/man_pic.png" width="136" height="126" alt=""></div>
      <h2>Host Name</h2>
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy 
        text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
        </div>
    </div>
    
    <div class="name_group">
    <div class="middle_section">
      <div class="picture_1"><img src="/templates/webinar_03_new/images/man_pic01.png" width="136" height="126" alt=""></div>
      <h2>Co-Host Name</h2>
      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy 
        text ever since when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
        </div>
    </div>
 
</section>


<section class="full webinar-white">
  <div class="middle_section">
  
  	<h2>Here Whats You Going to Learn <br> in This Webinar</h2>  
    
    <ul>
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing viverra magna elit.</li>
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing viverra magna elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing viverra magna elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing viverra magna elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing viverra magna elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>    
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing viverra magna elit.</li>    
    </ul>

  
   <div class="registerbutton"><span class="link">Click Here To Save Your Spot</span></div>
  
  </div>
</section>


<div class="full footer-gray light-gray">
  <div class="warning-img"><img src="/templates/webinar_03_new/images/warning_img.png" alt=""></div>
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