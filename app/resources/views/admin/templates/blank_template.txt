@extends('layouts/mobileoptin')
@section('header')
    <title>{{$title}}</title>
    <link rel="stylesheet" type="text/css" href="{{URL::to('/')}}/templates/{{$template_path}}/css/style.css">
    <meta content="{{$template_id}}" name="template_id"/>
    <meta content="{{$campaign_id}}" name="campaign_id"/>
@endsection
@section('content')
    <span class="link">And we will reply with this game changing report</span>
    <br/>
    <a href="{{$therms_of_link}}" class="therms_of_s" target="_blank">Terms of Services</a>
    <br/>
    <a href="{{$privacy_link}}" class="privacy" target="_blank">privacy Policy</a>
    <br/>
    <a href="{{$contact_us_link}}" class="contact_us" target="_blank">Contact us</a>
@endsection