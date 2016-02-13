@extends('layouts/main')



@section('content')


    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Dashboard</h1>
        </div>
        <div class="col-md-6 text-right pt-26">
            @if(Auth::user()->can('manage_campaign'))
                <a href="{{ route('add_campaigns')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Campaign</a>
            @endif

        </div>
    </div>
    @if(method_exists($campaigns,'total') && $campaigns->total())
        <div class="row" id="dashboard_data">
            <div class="col-md-12 ">

                <div class="table-responsive">
                    <div class="row borderBotg">
                        <div class="col-md-5 dasboard_doughnut_wraper">
                            <div>
                                <div class="row">
                                    <div class="col-xs-11">
                                        <h2>All Campaigns</h2>
                                    </div>
                                    <div class="col-xs-1">
                                        {{--<span class="glyphicon glyphicon-refresh" id="refresh_all_stats"></span>--}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <div id="doughnut_user_actions_holder">
                                            <canvas id="doughnut_user_actions" width="221" height="221"></canvas>
                                            <div id="hold_doughnut_click_data">
                                                <span id="label_Of_dgh"></span>
                                                <span id="value_Of_dgh"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4" id="doughnut_user_actions_ledend">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 dasboard_line_wraper">
                            <div>
                                <div class="row">
                                    <div class="col-xs-5">
                                        <h2>All Campaigns Visitors</h2>
                                    </div>
                                    <div class="col-xs-7">
                                        <div id="linear_visit_legend"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div id="visits_click_line_holder">
                                            <canvas id="visits_click_line" width="100%" height="43%"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Recent Campaigns - Last 7 Days Statistics</h3>
                        </div>
                    </div>
                    <table id="campaign_table" class="table   table-curved list_options">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Integrations</th>
                            <th>Status</th>
                            <th>Visitors</th>
                            @if(Auth::user()->can('manage_campaign'))
                                <th class="col-sm-2">Actions</th>
                            @endif
                            <th class=" slide_table_column"> Campaign details</th>
                        </tr>
                        </thead>
                        <tbody data-link="row">
                                         <?php 
                    $i = 0;
                    $countc = count($campaigns->items());
                    if($countc % 10  != 0){
                        $mod = $countc % 10;
                        $cc = 10 - $mod;
                        
                        for ($index = 0; $index < $cc; $index++) {
                            $campaigns[] = new MobileOptin\Models\Campaigns;
                        }
                    }
                    
                    ?>

                    @foreach($campaigns as $campaign)
                    @if($campaign->id == '')
                    <tr style='display: none;' id='hidden_row'>
                        <td  ><span style='height: 60px;' class="campaign_name"></span></td>
                         <td ><span style='height: 60px;' class="campaign_name"></span></td>
                          <td ><span style='height: 60px;' class="campaign_name"></span></td>
                           <td ><span style='height: 60px;' class="campaign_name"></span></td>
                            <td ><span style='height: 60px;' class="campaign_name"></span></td>
                    </tr>
                    @else
                            <tr data-campaign_id="{{$campaign->id}}">

                                <td><span class="campaign_name">{{ $campaign->name }}</span>


                                    <div class="row campaign_info">
                                        <div class="col-md-6">
                                            <strong>Created</strong>
                                        </div>
                                        <div class="col-md-6 ">
                                            {{date('F d,Y',strtotime($campaign->created_at))}}
                                        </div>
                                    </div>
                                    <div class="row campaign_info">
                                        <div class="col-md-6">
                                            <strong>Updated</strong>
                                        </div>
                                        <div class="col-md-6 ">
                                            {{date('F d,Y',strtotime($campaign->updated_at))}}
                                        </div>
                                    </div>
                                </td>

                                <td class="rowlink-skip " align="center">
                                    <div class="embed_btn_holder">
                                        <div class="or_circle">
                                            or
                                        </div>
                                        <button type="button" data-campaing_url="{{ route('campaign_link',['campaign_id'=> $campaign->id ,'campaing_name'=>$campaign->slug])  }}" class="show_embed_code btn
                                     @if($has_embed)
                                     btn-embedCode
                                     @else
                                                btn-embedCode
                                        @endif
                                                btn-sm" data-toggle="modal" data-target="#myModal"
                                        @if(!$has_embed)
                                                disabled="disabled"
                                                @endif
                                                >
                                            Embed code
                                        </button>

                                        <button type="button" data-campaing_url="{{ route('campaign_link',['campaign_id'=> $campaign->id ,'campaing_name'=>$campaign->slug])  }}" class="show_hosted_url btn  @if($has_hosted)
                                     btn-embedCode
                                     @else
                                                btn-embedCode
                                         @endif btn-sm" data-toggle="modal" data-target="#hostedModal"
                                        @if(!$has_hosted)
                                                disabled="disabled"
                                                @endif
                                                >
                                            hosted url
                                        </button>
                                    </div>
                                </td>
                                <td align="center">
                                    <span class="light_gray">{{ $campaign->active ? 'Active' : 'Disabled' }}</span></td>
                                <td align="center"><span class="dark_gray"><?php

                                        $totalvisits = 0;
                                        if ( isset( $splitTestStats[ $campaign->id ] ) ) {
                                            foreach ( $splitTestStats[ $campaign->id ] as $tmp_id => $twe ) {
                                                if(isset($twe[ 'total_opened' ])){
                                                $totalvisits += $twe[ 'total_opened' ];}
                                                
                                            }
                                        }
                                        ?>

                                        {{$totalvisits}}
                                    </span>
                                </td>
                                @if(Auth::user()->can('manage_campaign'))
                                    <td class="rowlink-skip">


                                        <ul class="nav nav-pills">
                                            <li role="presentation" class="dropdown">
                                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text">Action</span>

                                                    <div class="iconholder">
                                                        <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                                                    </div>
                                                </a>
                                                <ul class="dropdown-menu">

                                                    <li>
                                                        <a href="{{ route('edit_campaigns',['id'=> $campaign->id])  }}">Edit</a>
                                                    </li>
                                                    <li>
                                                        @if($campaign->active)
                                                            <a href="{{ route('change_status_campaigns',['id'=> $campaign->id ,'status'=>0])  }}">Disable</a>
                                                        @else
                                                            <a href="{{ route('change_status_campaigns',['id'=> $campaign->id ,'status'=>1])  }}">Enable</a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        @if(Auth::user()->getOwner() == false)
                                                            <a onclick="return confirm(' you want to delete?');" href="{{ route('delete_campaigns',['id'=> $campaign->id])  }}">Delete</a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        @if(Auth::user()->getOwner() == false)
                                                            <a href="{{ route('campaigns_assinged',['id'=> $campaign->id])  }}">Assigned Users</a>
                                                        @endif
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>

                                    </td>
                                @endif
                                @if($i==0)
                                    <td class="rowlink-skip slide_table_column  " rowspan="{{count($campaigns->items())}}">
                                        <div class="stats_holder">
                                            <div class="howtousecampaignlist">
                                                <img src="{{url('img/green_arrow.png')}}" width="60" height="73">
                                                </br>
                                                Click on title of the campaign to view quick stats
                                            </div>
                                        </div>
                                    </td>
                                    <?php $i = 1?>

                                @endif
                                </tr>
            @endif
            @endforeach
        </tbody>
                    </table>


                </div>
            </div>
        </div>


        <div style="display: none;">
          
            @foreach($campaigns as $campaign)
              <?php $wfo = ''; ?> 
                <div id="stats_for_campaign_id_{{$campaign->id}}">
                      <div style="overflow-y: scroll;max-height: 100rem !important;">
                    @if(isset($splitTestStats[$campaign->id])  )
                        <a href="{{route('extended_testing_results',['campaign_id'=>$campaign->id])}}" class="btn btn-extended_stats pull-right">Results</a>
                        <br/>
                        <br/>

                        @foreach($splitTestStats[$campaign->id] as $tmp_id=>$twe)
                            <?php
                                            if (isset($twe['total_events'])) {
                    $total_ev = $twe['total_events'];
                } else {
                    $total_ev = 0;
                }
                if (isset($twe['total_opened'])) {
                    $total_opened = $twe['total_opened'];
                } else {
                    $total_opened = 0;
                }

                if (isset($twe['total_mailto'])) {
                    $total_mailto = $twe['total_mailto'];
                  
                } else {
                    $total_mailto = 0;
                }
                
                if(isset($total_mailto) && $total_mailto > 0 && isset($total_opened) && $total_opened > 0){
                    
                $conversion =(round($total_mailto / $total_opened, 3 ) * 100 ) . '%';
     
                }
                else{
                $conversion = '0%';   
                
                }
                $array_of_ids_templates[] = $tmp_id;
                            $afp = 0;
                            $name = '';
                             $flag = false;
                            foreach ( $campaign->template as $tmpinfo ) {

                                if ( $tmpinfo->id == $tmp_id ) {
                                    $afp  = $tmpinfo->affect_percentile;
                                    $name = $tmpinfo->name;
                                    $flag = true;
                                }
                            }

                            if ($flag == true) {
                    if ($afp == 0) {

                        $wfo .= ' <div class="stats_for_t">
                            <h5 class="template_title_qs">Variation: ( ' . $name . ' )</h5>

                            <div class="row">
                                <div class="col-xs-4">
                                    <span class="sttitle">Visitors</span>
                                    ' . $total_opened . '
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Clicks</span>
                                    ' . $total_ev . '
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Conversion</span>
                                    ' . $conversion . '
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12" style="background-color:grey">
                                    Disabled
                                </div>
                            </div>
                        </div>';
                    } else {
                        $wfo .= ' <div class="stats_for_t">
                            <h5 class="template_title_qs">Variation: ( ' . $name . ' )</h5>

                            <div class="row">
                                <div class="col-xs-4">
                                    <span class="sttitle">Visitors</span>
                                    ' . $total_opened . '
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Clicks</span>
                                    ' . $total_ev . '
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Conversion</span>
                                    ' . $conversion . '
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    Traffic Allocation:
                                    <span class="tmp_percentage">
                                         ' . $afp . '
                                        %
                                        </span>
                                </div>
                            </div>
                        </div>';
                    }
              
                
                }
                
            ?> @endforeach;
      
            <?php
            
            foreach ($campaign->template as $tmpinfo) {

            if (!in_array($tmpinfo->id, $array_of_ids_templates)) {

                if ($tmpinfo->affect_percentile == 0) {
                    $wfo .= ' <div class="stats_for_t">
                            <h5 class="template_title_qs">Variation: ( ' . $tmpinfo->name . ' )</h5>

                            <div class="row">
                                <div class="col-xs-4">
                                    <span class="sttitle">Visitors</span>
                                   0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Clicks</span>
                                   0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Conversion</span>
                                    0
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12" style="background-color:grey">
                                    Disabled
                                </div>
                            </div>
                        </div>';
                } else {
                    $wfo .= ' <div class="stats_for_t">
                            <h5 class="template_title_qs">Variation: ( ' . $tmpinfo->name . ' )</h5>

                            <div class="row">
                                <div class="col-xs-4">
                                    <span class="sttitle">Visitors</span>
                                    0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Clicks</span>
                                    0
                                </div>
                                <div class="col-xs-4">
                                    <span class="sttitle">Conversion</span>
                                    0
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    Traffic Allocation:
                                    <span class="tmp_percentage">
                                         ' . $tmpinfo->affect_percentile . '
                                        %
                                        </span>
                                </div>
                            </div>
                        </div>';
                }
      
            }
        }
        ?>
      <?php echo $wfo; ?>
                    @else
                        <h4>This campaign currently does not have any stats.</h4>
                    @endif
                </div>
                    </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-xs-12">
                <h4>No campaigns activity detected in the last 7 days</h4>
            </div>
        </div>
    @endif
    @include('campaigns.modal.hosted')
    @include('campaigns.modal.embed')
    @include('campaigns.modal.annonces', ['content' => $announcement_params['content'], 'title' => $announcement_params['title'] ])


@endsection

@section('javascript')
    <script type="text/javascript">

        var all_campaings_doughnut = [
            {
                value: {{$stats_for_doughnut['Visitors']}},
                color: "#ffc400",
                highlight: "#ffd829",
                label: "Visitors"
            },
            {
                value: {{$stats_for_doughnut['Clicked']}},
                color: "#38a4dd",
                highlight: "#72d2ff",
                label: "Clicked"
            },
            {
                value: {{$stats_for_doughnut['bounce']}},
                color: "#b39ddb",
                highlight: "#eed5ff",
                label: "Bounce"
            }
        ];


        var all_campaigns_linedata = {
                    labels: <?=json_encode(array_values($graph_labels), true)?>,
                    datasets: [
                        {
                            label: "Visits",
                            fillColor: "rgba(220,220,220,0.2)",
                            strokeColor: "rgba(220,220,220,1)",
                            pointColor: "rgba(220,220,220,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            data: <?=json_encode(array_values($stats_for_line['visits']), true) ?>
                        },
                        {
                            label: "Clicks",
                            fillColor: "rgba(151,187,205,0.2)",
                            strokeColor: "rgba(151,187,205,1)",
                            pointColor: "rgba(151,187,205,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(151,187,205,1)",
                            data: <?=json_encode(array_values($stats_for_line['clicks']), true)?>
                        }
                    ]
                }
                ;
		var announcementWillOpen    = <?= (isset($openAnnouncementModal) ? $openAnnouncementModal : 0) ?>;
		var csrf_token = <?= json_encode(csrf_token()) ?>; 
    </script>
@endsection
