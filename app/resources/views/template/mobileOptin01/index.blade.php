@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:400,300,300italic,700,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
    <section class="full-container" id="theme-1">
        <section class="container">
            <!--content box -->
            <article class="content-box blue-border">
                <h2>CLICK THE BUTTON TO SEND US AN EMAIL</h2>
                <span class="link">And we will reply with this game changing report</span>
            </article>
            <!--content box -->
            <section class="content-box msg-box"><span class="arrow-left"></span>
                <span class="link">Click here to send us an<br>
                    email for Instant Access</span>
                <span class="arrow-right"></span></section>
            <!--content box -->
            <article class="content-box orange-border box-instant">
                <p>This is a 100% FREE offer by sending us this secure email request you are agreeing to receive future emails from us. You may unsubscribe from these to emails at any time credit card is NOT required. </p>
                <h4>For Instant access to your
                    <span>“FAST TRAFFIC FORTUNE REPORT”</span> plus unannounced HD video bonus:
                </h4>
                <ul>
                    <li> Touch the button above</li>
                    <li> Click send on the email to submit your request</li>
                    <li> And we will instantly reply with: “THE FAST TRAFFIC FORTUNE
                        REPORT” +HD video bonus
                    </li>
                </ul>
                <span class="button-instant"><span class="link">Or Click Here to SEND us an Email for Instant Access</span></span>
            </article>
            <!--content box -->
        </section>
        <!--container -->

        <!--Content ends Here-->
        <footer id="footer">
            <nav>
                <ul>
                    <li><a href="{{$therms_of_link}}" class="therms_of_s" target="_blank">Terms of Services</a></li>
                    <li><a href="{{$privacy_link}}" class="privacy" target="_blank">privacy Policy</a></li>
                    <li><a href="{{$contact_us_link}}" class="contact_us" target="_blank">Contact us</a></li>
                </ul>
            </nav>
        </footer>
        <!-- footer ends-->
    </section>

@endsection