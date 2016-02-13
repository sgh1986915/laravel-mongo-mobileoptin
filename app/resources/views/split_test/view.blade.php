@extends('layouts/main')

@section('content')
    <input type="hidden" id="campaign_id" value="{{$campaign->id}}">
    <div class="row">
        <div class="col-md-8">
            <h3>{{$campaign->name}}</h3>
        </div>
        <div class="col-md-4">
            <br/>

            <div id="reportrange" class="reportrange pull-right">
                <i class="glyphicon glyphicon-calendar"></i>
                <span><?php echo date( "F j, Y", strtotime( '-30 day' ) ); ?> - <?php echo date( "F j, Y" ); ?></span>
                <input type="hidden" id="filter_start_date" value="{{date('d-m-Y',strtotime('-30 day'))}}">
                <input type="hidden" id="filter_end_date" value="{{date('d-m-Y')}}">
                <b class="caret"></b>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="canvas_holder">

            <canvas id="graph"></canvas>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <hr/>
        </div>
    </div>

    {{--
    <div class="row">
        <div class="col-md-12">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Template</th>
                    <th>Event</th>
                    <th>Button</th>
                    <th>Referer</th>
                    <th>Date Time</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    --}}

    <div class="row">
        <div class="col-md-12">
            <table id="stat-summary" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Template</th>
                        <th>Impressions</th>
                        <th>Clicks</th>
                        <th>Click %</th>
                        <th>Optins</th>
                        <th>Optin %</th>
                        <th>Conversions</th>
                        <th>Conversion %</th>
                        <th>Tracking Pixel</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('campaigns.modal.pixel')

@endsection