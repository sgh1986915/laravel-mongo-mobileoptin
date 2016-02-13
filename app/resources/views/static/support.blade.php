@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="tab-content">
            <div id="Support" class="tab-pane fade in active">
                <h2>Support</h2>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Please Watch Our Support Video First</h4>

                        <div>

                            <a class="optimizeplayer-popout" data-launch-with='thumbnail' data-cid='5ba660c0dd48db3ee53e4ddccb8acb36' data-width='704' data-height='405' data-button-color='126,151,161' href='https://embed.optimizeplayer.com/projects/5ba660c0dd48db3ee53e4ddccb8acb36'><img src='//d3o3zcugssr14a.cloudfront.net/thumbnails/d580c6fd1de53628f948057959cb2261.jpg' width='540' height='310'/>
</a>
<script src='https://embed.optimizeplayer.com/projects/5ba660c0dd48db3ee53e4ddccb8acb36/popout.js'></script>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Still Have Questions? Please Submit A Ticket</h4>

                        <form method="POST" name="support_form" action="{{ url('/support') }}">
                            <fieldset>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ old('name') }}">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" placeholder="Enter Email Address" name="email" value="{{ old('email') }}">

                                </div>

                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" placeholder="Enter Message" rows="8" name="message">{{ old('message') }}</textarea>
                                </div>


                                <div class=" text-right">
                                    <button type="submit" class="btn btn-green  ">Send</button>
                                </div>

                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function () {

var tmp2 = new Array();		// массива
var get = location.href;
if (get != '') {
    tmp2 = get.split('#');
    if (tmp2[1] === 'faq') {
        $('#faq').tab('show');
        $('a[href="#faq"]').tab('show')
    } else {
        $('#Support').tab('show');
        $('a[href="#Support"]').tab('show')
    }
}

$('#contact_top').click(function () {
    $('#Support').tab('show');
    $('a[href="#Support"]').tab('show')
});

});


</script>
@endsection