@extends('layouts/main')


@section('content')
    <div class="row">
        <div class="col-md-8 ">
            <h1 class="category_header"><b>{{ $faq_category_name }}</b> / Add A TEA's Answer</h1>
        </div>
        <div class="col-md-4 text-right pt-26">

        </div>
    </div>

    <div id="creator_container">
        <div class="row">
            <div class="col-md-9">
             @if(isset($faq_answer) && $faq_answer->id != 0)
            {!! Form::model($faq_answer, ['route' => ['admin.expert_traffic.add_edit', $faq_answer->id], 'class' => 'form-horizontal', 'files' => true]) !!}
             @else
            {!! Form::open(array('route'=>'admin.expert_traffic.add_edit', 'class' => 'form-horizontal', 'files' => true)) !!}
            @endif
            {!! Form::hidden('id', $faq_answer->id) !!}
            {!! Form::hidden('faq_category_id', $faq_answer->faq_category_id) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div id="editing_template">
                                <form class="form-horizontal" id="save_User_template_form">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-3 control-label">Question</label>
                                        <div class="col-sm-9">
                                               {!! Form::text('question', Input::old('question'), array('class' => 'form-control')) !!}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-3 control-label">Answer</label>
                                        <div class="col-sm-9">
                                               {!! Form::textarea('answer', Input::old('question'), array('class' => 'form-control', 'style' => 'height: 257px; resize: none', 'id' => 'faq-question')) !!}
                                        </div>
                                    </div>
                                    <div class="form-group row">
									    <label for="name" class="col-sm-3 control-label">PDF</label>
									    <div class="col-sm-9">
									        <span class="btn btn-default btn-file">
										        Browse {!! Form::file('pdf_file', null) !!}
										    </span>
										</div>
									</div>
                                    <div class="row">
                                        <div class="col-md-12 col-md-offset-3">
                                            <button type="submit" id="save_tmp_changes2" class="btn  btn-orange">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
	<script type="text/javascript" defer="defer">
		$(document).ready(function(){
			CKEDITOR.replace('faq-question', {  
				height: 257,
				toolbarGroups: [
					{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
					{ name: 'editing',     groups: [ 'find', 'selection' ] },
					{ name: 'insert' },
					{ name: 'document',	   groups: [ 'mode', 'document' ] },
					{ name: 'basicstyles', groups: [ 'basicstyles' ] }
				],
				removeButtons: '',
				removePlugins: '',
				removeDialogTabs: '',
			    format_tags: 'p;h1;h2;h3;pre'
			});
		});
	</script>
@endsection

