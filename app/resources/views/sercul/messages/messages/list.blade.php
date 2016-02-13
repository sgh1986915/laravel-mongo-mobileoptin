@extends('layouts/main')

@section('content')

    <div class="row">
        <div class="col-md-6 ">
            <h1 class="category_header">Messages</h1>
        </div>
        <div class="col-md-4 text-right pt-3">
          
        </div>
        <div class="col-md-2 text-right pt-26">
           @if(Auth::user()->hasRole( 'admin' ))
                <a href="{{ route('add_message')  }}" class="btn btn-green"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Message</a>
            @endif

        </div>
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <div class="table-responsive">
                <table id="message_table" class="table   table-curved list_options">
                    <thead>
                    <tr>
                 
                        <th align="left">Topic</th>
                        <th style="text-align:right">Date</th>
                          @if(!Auth::user()->hasRole( 'admin' ))
                        <th style="text-align:right">Status</th>
                        @endif
                       @if(Auth::user()->hasRole( 'admin' ))
                            <th class="col-sm-2">Actions</th>
                        @endif
                       
                    </tr>
                    </thead>
                    <tbody data-link="row">
                    <?php $i = 0;?>

                    @foreach($messages as $message)
                
                    <tr role="button" name="messl_{{$message->id}}" data-topic="{{ $message->message?$message->message->topic:$message->topic }}" data-content="{{ $message->message?$message->message->content:$message->content }}"  data-message_id="{{$message->id}}">
                            <td align="left">
                                <span>{{ $message->message?$message->message->topic:$message->topic }}</span>
                            </td>
                             
                            <td align="right">
                              <span>{{ $message->created_at }}</span>
                            </td>
                                @if(!Auth::user()->hasRole( 'admin' ))
                            <td align="right">
                               <span id="status_{{$message->id}}"><?php if(isset($message->status) && $message->status == 1) echo 'Read'; else echo 'Unread'; ?></span>
                            </td>
                           @endif
                            @if(Auth::user()->hasRole( 'admin' ))
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
                                                    <a href="{{ route('edit_message',['id'=> $message->id])  }}">Edit</a>
                                                </li>
                                               
                                                <li>
                                                    @if(Auth::user()->getOwner() == false)
                                                        <a onclick="return confirm(' you want to delete?');" href="{{ route('delete_message',['id'=> $message->id])  }}">Delete</a>
                                                    @endif
                                                </li>
                                            
                                            </ul>
                                        </li>
                                    </ul>

                                </td>
                            @endif
                        </tr>

                    @endforeach
                    </tbody>
                          <tfoot>
        <th class='text-right' colspan="6">
{!! $messages->render() !!}</th>
        </tfoot>
                </table>


            </div>
        </div>
    </div>


@endsection
@section('javascript')
    <script type="text/javascript">
       $(document).ready(function(){
         $('[name^="messl_"] td[class!="rowlink-skip"]').click(function(){
             var mess_id = $(this).parent().data('message_id');
             var content = $(this).parent().data('content');
             var topic = $(this).parent().data('topic');
             BootstrapDialog.show({
            title: topic,
            message: content,
            buttons: [ {
                label: 'Ok',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        $.ajax({
            url: base_url + '/messages/read/'+ mess_id,
            type: 'GET',
            dataType: "json",
            success: function (content) {
              if(content.status == 0){
                $('#status_'+mess_id).html('Read');
                var messcount = $('#messcount').html().match("[0-9]+").pop();
                var newmesscount = (messcount > 1) ? "(" + parseInt(messcount - 1) + ")"  : null;
                $('#messcount').html(newmesscount);
              }
            },
            error: function (errordata) {
               console.log(errordata); 
            }
        });
        
         })  
       })
          </script>
@endsection