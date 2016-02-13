Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
$(function () {


    $.fn.modal.Constructor.prototype.enforceFocus = function () {
        modal_this = this
        $(document).on('focusin.modal', function (e) {
            if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                modal_this.$element.focus()
            }
        })
    };
    ZeroClipboard.config({swfPath: base_url + "/swf/ZeroClipboard.swf"});
    if ($('.show_hosted_url').length) {
        // if there is campaign link take it and put it in the modal
        $(document).on('click', '.show_hosted_url', function () {
            campaign_url = $(this).data('campaing_url');
            custom_url = $(this).data('custom_url');
            if(custom_url != '' && custom_url != undefined){
              temp_base_url = base_url.replace('https://','').replace('http://','');
              campaign_url = campaign_url.replace('https://','').replace('http://','').replace(temp_base_url,custom_url);
            }else{
                campaign_url = campaign_url.replace('https://','').replace('http://','')
            }
            $('#hosted_url_container', '#hostedModal').html(campaign_url);
            $('#copy-url-to-cb', '#hostedModal').attr('data-clipboard-text', campaign_url);
        });

        var clientText = new ZeroClipboard($("#copy-url-to-cb"), {
            moviePath: base_url + "/swf/ZeroClipboard.swf",
            debug: false
        });
        // part for url only
        clientText.on("load", function (clientText) {
            // $('#flash-loaded').fadeIn();

            clientText.on("complete", function (clientText, args) {
                clientText.setText(args.text);
                // $('#copy-url-to-cb-text').fadeIn();
            });
        });
    }

    $(document).ready(function(){
    	(function ($) {
    	    "use strict";
    	    
    	    function centerModal() {
    	        $(this).css('display', 'block');
    	        var $dialog  = $(this).find(".modal-dialog"),
    	        offset       = ($(window).height() - $dialog.height()) / 2,
    	        bottomMargin = parseInt($dialog.css('marginBottom'), 10);

    	        // Make sure you don't hide the top part of the modal w/ a negative margin if it's longer than the screen height, and keep the margin equal to the bottom margin of the modal
    	        if(offset < bottomMargin) offset = bottomMargin;
    	        $dialog.css("margin-top", offset);
    	    }
    	  
            if(typeof announcementWillOpen !== "undefined" && announcementWillOpen == 1){
            	var annoncement_modal = $('#annoncesModal');
            	annoncement_modal.modal('show');
            	annoncement_modal.on('shown.bs.modal', function(){
            		$(window).trigger("resize");
            	});
            	
            	annoncement_modal.on('hidden.bs.modal', function(){
            		if($('input[name="never_display_announce"]').is(':checked')){
            			$.ajaxSetup({
            		        headers: {
            		            'X-CSRF-TOKEN': csrf_token
            		        }
            		    });
            			var jxhr = $.post('handle_display_announce', {dont_display_again: 0}, $.noop);
            		}
            	});
            }
    	}(jQuery));
    	
    });
    
    if ($('.show_embed_code').length) {
        // if there is campaign link take it and put it in the modal
        $(document).on('click', '.show_embed_code', function () {
            campaing_url = $(this).data('campaing_url');
            custom_url = $(this).data('custom_url');
            if(custom_url != '' && custom_url != undefined){
              temp_base_url = base_url.replace('https://','').replace('http://','');
              campaing_url = campaing_url.replace('https://','').replace('http://','').replace(temp_base_url,custom_url);
            }else{
                 campaing_url = campaing_url.replace('https://','').replace('http://','')
            }

            
            js_for_user = $('#java-text-for-cb').text();
            $('#java-text-for-cb').text(js_for_user.replace(/\/\/startconfig[\s\S]*?\/\/endconfig/i,
                "//startconfig\n" +
                "var mobile_optin_campaign_url = '" + campaing_url + "';\n" +
                "//endconfig \n"
            ));
        });
        // part for text area
        var clientTarget2 = new ZeroClipboard($("#copy-js-to-cb"), {
            moviePath: base_url + "/swf/ZeroClipboard.swf",
            debug: false
        });

        clientTarget2.on("load", function (clientTarget) {

            clientTarget.on("complete", function (clientTarget, args) {
                clientTarget.setText(args.text);
            });
        });
    }

    $("#templateEditor").draggable({
        handle: ".modal-header"
    });
    if ($('#template_content').length) {

        //CKEDITOR.disableAutoInline = true;
        CKEDITOR.timestamp = +new Date;

        CKEDITOR.config.allowedContent = true;
        if (CKEDITOR.instances.template_content) {
            try {
                CKEDITOR.instances.template_content.destroy(true);
            } catch (e) {
            }
        }

       CKEDITOR.replace('template_content', {  language: 'en' });
       

    }

    function init_niceDD(destroy_old) {
        if (destroy_old) {
            $('.template_selector').ddslick('destroy');
        }
        $('.template_selector').ddslick({
            selectText: 'Please select a template',
            imagePosition: 'left',
            width: '100%',
            height: '30rem',
            onSelected: function (data) {
                if (data.selectedData.enambed_item === false) {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_DANGER,
                        title: 'Template Group - Please Upgrade',
                        message: 'This option is not available for your account, please upgrade your account',
                        buttons: [{
                            label: 'Ok',
                            action: function (dialogItself) {
                                dialogItself.close();
                            }
                        }]
                    });
                    tid = $(data.original.context).attr('id').substring(13);
                    $('#selector_for_' + tid + '-dd-placeholder').ddslick('select', {index: 1});
                } else {
                    tid = $(data.original.context).attr('id').substring(13);
                    $('span[data-utid=' + tid + '].edit_ut').click();
                }
            }
        });
    }

    if ($('#slug').length && $('#name').length) {
        $(document).on('keyup', '#name', function () {
            var title_text = $(this).val();
            setTimeout(function () {
                sluged_text = convertToSlug(title_text);
                $('#slug').val(sluged_text);
            }, 300);
        });
    }

    $(document).on('keyup', '.percent_container', function () {
        if ($(this).val() > 0) {
            $(this).parent().parent().parent().removeClass('grayed_variation');
            $(this).parent().parent().siblings(".overlay_disabled").addClass('hidden');
        } else {
            $(this).parent().parent().parent().addClass('grayed_variation');
            $(this).parent().parent().siblings(".overlay_disabled").removeClass('hidden');

        }
    })

    function mngao_threshold() {
        if ($('#ao_clicks').val() > 0) {
            $('#ao_threshold').removeAttr('disabled');
        } else {
            $('#ao_threshold').attr('disabled', 'disabled');

        }

    }

    $(document).on('keyup', '#ao_clicks', function () {
        mngao_threshold();
    })
    mngao_threshold();


    init_niceDD(true);

    $(document).on('click', '.remove_ut', function () {
        var pressed_row_delete_button = $(this);
        var tmpl_id = $(this).data('delete_id');
        $.ajax({
            url: base_url + '/campaigns/rut',
            type: 'POST',
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="a-token"]').attr('content')
            },
            data: {tempalte_id: tmpl_id},
            success: function (content) {
                if (content.status == true) {
                    $(pressed_row_delete_button).parent().parent().remove();
                }
            },
            error: function (errordata) {
                if (errordata.status == 401) {
                    show_reloging_info();
                } else if (errordata.status == 500) {
                    show_reloging_info();
                }
            }
        });

    });


    var submit_add       = 1;
    var add_new_template = false;
    $(document).on('click', 'span#add_template', function (e) {
    	
            var campaign_id      = $('#campaign_id').val(),
                template_id      = $("div.template-container").filter(":first").data('utid');
            
            
            /*if (submit_add) {


                container = $('div#different_templates');
                campaign_id = $('#campaign_id').val();
                preview_url = base_url + '/campaigns/add_template_toc/' + campaign_id;

                $.ajax({
                    url: preview_url,
                    type: 'GET',
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        submit_add = 0;
                    },
                    success: function (content) {

                        template_content = {
                            "template_id": content.tmpl_id
                        };
                        $.tmpl(table_row_template, template_content).prependTo(container);
                        init_niceDD(true);

                        if ($('input.percent_container').length == 1) {
                            $('input.percent_container').val(100);
                        }

                        setTimeout(function () {
                            submit_add = 1;
                        }, 100);
                    },
                    error: function (errordata) {
                        if (errordata.status == 401) {
                            show_reloging_info();
                        } else if (errordata.status == 500) {
                            show_reloging_info();
                        }
                    }
                });

            }*/
    	
            if(typeof template_id == 'undefined'){
            	if (submit_add) {
            		 
            		 container = $('div#different_templates');
                     preview_url = base_url + '/campaigns/add_template_toc/' + campaign_id;
                     
	            	$.ajax({
	                    url: preview_url,
	                    type: 'GET',
	                    dataType: "json",
	                    cache: false,
	                    beforeSend: function () {
	                        submit_add = 0;
	                    },
	                    success: function (content) {
	
	                        template_content = {
	                            "template_id": content.tmpl_id
	                        };
	                        $.tmpl(table_row_template, template_content).prependTo(container);
	                        init_niceDD(true);
	
	                        if ($('input.percent_container').length == 1) {
	                            $('input.percent_container').val(100);
	                        }
	
	                        handleIntegrationModal(content.tmpl_id, false, campaign_id);
	                        
	                        setTimeout(function () {
	                            submit_add = 1;
	                        }, 100);
	                    },
	                    error: function (errordata) {
	                        if (errordata.status == 401) {
	                            show_reloging_info();
	                        } else if (errordata.status == 500) {
	                            show_reloging_info();
	                        }
	                    }
	                });
            	}
            }else
            	handleIntegrationModal(template_id, true, campaign_id);
    });
    
    var templateAlreadyLoaded = false,
	onRightMoved          = false,
	onLeftMoved           = false;

	$(document).on('click', 'div.template-container', function (a) {
	        var thi            = $(this),
	        	template_id    = $(this).data('utid'),
	        	container      = $('div#exist_cont_'+template_id),
	            campaign_id    = $('#campaign_id').val(),
	            preview_url    = base_url + '/campaigns/add_change_template_toc/' + template_id;
	        
	        handleIntegrationModal(template_id, false, campaign_id);
	});
    
    function handleIntegrationChoose(campaign_id, add_template, template_id){
    	var input_selected       = $("input[name='template_choosen']").filter(':checked'),
			htmlTemplateContent,
			a_href,
			input_template_selector;
	
		if(input_selected){
			var template_choosen_id = parseInt(input_selected.val(), 10);
			if(!isNaN(template_choosen_id) && template_choosen_id > 0){
				
				var container        = $('div#different_templates'),
	                preview_url      = base_url + '/campaigns/add_template_toc/' + campaign_id;
	    	    
				if(add_template){
					$.ajax({
		                url: preview_url,
		                type: 'GET',
		                dataType: "json",
		                cache: false,
		                beforeSend: function () {
		                    $('#loader_'+template_id).show();
		                },
		                success: function (content) {
		                    $.tmpl(table_row_template, {"template_id": content.tmpl_id })
		                     .prependTo(container);
		                    
		                    input_template_selector = $('input#selector_for_' + content.tmpl_id );
		                    input_template_selector.val( template_choosen_id );
		                    
		                    htmlTemplateContent = input_selected.parent().html();
	            			a_href = input_template_selector
		                				.parent()
		                				.find('a.dd-selected')
		                				.filter(':first');
	            			a_href.html(htmlTemplateContent);
	            			a_href.find('label.dd-option-text').removeClass('hidden');
	            			a_href.find('small').removeClass('hidden');
	            			a_href.find('input[type="radio"]').remove();
	            			
	            			$('span[data-utid=' + content.tmpl_id + '].edit_ut').click();
		                    
		                    if ($('input.percent_container').length == 1) {
		                        $('input.percent_container').val(100);
		                    }
		                    $('#loader_'+template_id).hide();
		                },
		                error: function (errordata) {
		                    if (errordata.status == 401) {
		                        show_reloging_info();
		                    } else if (errordata.status == 500) {
		                        show_reloging_info();
		                    }
		                }
		            });
				}else{
					
					input_template_selector = $('input#selector_for_' + template_id );
	    			input_template_selector.val( template_choosen_id );
	    			htmlTemplateContent = input_selected.parent().html();
	    			a_href = input_template_selector
	            				.parent()
	            				.find('a.dd-selected')
	            				.filter(':first');
	    			
	    			a_href.html(htmlTemplateContent);
	    			a_href.find('label.dd-option-text').removeClass('hidden');
	    			a_href.find('small').removeClass('hidden');
	    			a_href.find('input[type="radio"]').remove();
	    			$('span[data-utid=' + template_id + '].edit_ut').click();
				}
			}
		}
    }
    
    function handleIntegrationModal(template_id, add_template, campaign_id){
    	
    	var containerTabId = $('#templateTabContainerId'),
    	    template_modal = $('#templateModal');
    	
    	if(templateAlreadyLoaded){
        	template_modal.modal('show');
        	template_modal.find("button#template_choosen_selected").off('click').on('click', function(){
        		handleIntegrationChoose(campaign_id, add_template, template_id);
        		template_modal.modal('hide');
        	});
        }else{
        	$.ajax({
                url: base_url + '/campaigns/add_change_template_modal/' + template_id,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function () {
                	$('#loader_'+template_id).show();
                },
                success: function (result) {
                	templateAlreadyLoaded = true;
                	 //Open Template modal
                    var singleWidth     = 200,
                		scrollBarWidths = 40,
                		widthOfList,
                		widthOfHidden,
                		getLeftPosi,
                		reAdjust;
                	
                	template_modal.find('#templateTabModal').html(result.tabHeader);
                	template_modal.find('#TemplateTabModalContent').html(result.tabContent);
                	
                	$("input[name='template_choosen']").on('click', function(){
                		$("input[name='template_choosen']").each(function(){
                			$(this).parent().removeClass('label-checked');
                		});
                		$(this).parent().addClass('label-checked');
                	});
                	
                	$('#loader_' + template_id).hide();
                	template_modal.modal('show');
                	template_modal.on('shown.bs.modal', function(){
                		
                		
                		$('a[href="#cat1"]').parent().addClass('active');
                		$('div#cat1').addClass('active in ');
                		
                		widthOfList = function(){
                      	  var itemsWidth = 0;
                      	  containerTabId.find('.list li').each(function(){
                      	    var itemWidth = $(this).outerWidth();
                      	    itemsWidth+=itemWidth;
                      	  });
                      	  return itemsWidth;
                      	};
                      	
                      	var totalWidthList    = widthOfList();
                      	var totalWrapperWidth =  containerTabId.find('.wrapper').outerWidth();
                      	
                      	
                      	widthOfRightHiddenPart = function(){
                      		return (totalWidthList - totalWrapperWidth - getListPosition(true)) - scrollBarWidths;
                      	};
                      	
                      	widthOfHidden = function(){
                      	  return (totalWrapperWidth-totalWidthList-getListPosition(true))-scrollBarWidths;
                      	};
                      	
                      	getListPosition = function(left){
                      		if(left)
                      			return containerTabId.find('.list').position().left;
                      		else
                      			return containerTabId.find('.list').position().right;
                      	};
                      	
                      	reAdjust = function(){
                      	  if (totalWrapperWidth < totalWidthList) {
                      		containerTabId.find('.scroller-right').show();
                      	  }
                      	  else {
                      		containerTabId.find('.scroller-right').hide();
                      	  }
                      	  
                      	  if (getListPosition(true)<0) {
                      		containerTabId.find('.scroller-left').show();
                      	  }
                      	  else {
                      		containerTabId.find('.item').animate({left:"-="+getListPosition(true)+"px"},'slow');
                      		containerTabId.find('.scroller-left').hide();
                      	  }
                      	}
                      	
                      	reAdjust();
                      	
                      	containerTabId.find('.scroller-right').click(function() {
                      		if(!onLeftMoved){
                      			onLeftMoved = true;
                      			var leftListPosition = getListPosition(true);
                        		if(leftListPosition <= 0 && ((-leftListPosition)+singleWidth+totalWrapperWidth) < totalWidthList){
                            		containerTabId.find('.scroller-left').fadeIn('slow');
                        			containerTabId.find('.list').animate({left:"-=" + singleWidth + "px"},'slow',function(){
                        				onLeftMoved = false;
                            		});
                        		}else{
                        			containerTabId.find('.scroller-left').fadeIn('slow');
                            		containerTabId.find('.scroller-right').hide();
                        			containerTabId.find('.list').animate({left:"+="+widthOfHidden()+"px"},'slow',function(){
                        				onLeftMoved = false;
                            		});
                        		}
                      		}
                    	});
                      	
                    	containerTabId.find('.scroller-left').click(function() {
                    		if(!onRightMoved){
                    			onRightMoved = true;
                    			var leftListPosition = getListPosition(true);
                        		if((leftListPosition + singleWidth) < 0){
                        			containerTabId.find('.scroller-right').fadeIn('slow');
                        			containerTabId.find('.list').animate({left:"+="+ singleWidth +"px"},'slow',function(){
                        				onRightMoved = false;
                            	  	});
                        		}else{
                        			containerTabId.find('.scroller-right').fadeIn('slow');
                            		containerTabId.find('.scroller-left').hide();
                        			containerTabId.find('.list').animate({ left:"-=" + getListPosition(true) + "px" }, 'slow' ,function(){
                        				onRightMoved = false;
                            	  	});
                        		}
                    		}
                    	});
                	});
                	
                	template_modal.find("button#template_choosen_selected").on('click', function(){
                		handleIntegrationChoose(campaign_id, add_template, template_id);
                		template_modal.modal('hide');
                	});
                	
                	template_modal.find("button#template_choosen_cancel").on('click', function(){
                		template_modal.modal('hide');
                	});
                	

                	$(window).on('resize',function(e){  
                	  	reAdjust();
                	});
                },
                error: function (errordata) {
                	$('#loader_'+template_id).hide();
                	if (errordata.status == 401) {
                        show_reloging_info();
                    } else if (errordata.status == 500) {
                        show_reloging_info();
                    }
                }
            });
        }
    }
    

    $(document).on('click', '.edit_ut', function (e) {
        e.preventDefault();
        $("#template_editing_manual").hide();
        $('#edit_template_holder_column').show();

        template_id = $(this).data('utid');


        org_tmp_id = $('#selector_for_' + template_id).val();


        campaign_id = $('#campaign_id').val();
        preview_url = base_url + '/campaigns/preview/' + template_id + '/' + org_tmp_id;

        $.ajax({
            url: preview_url,
            type: 'GET',
            dataType: "json",
            cache: false,
            beforeSend: function () {
                submit_add = 0;
            },
            success: function (content) {

                CKEDITOR.instances.template_content.setData(content.tmpl_body);

                $('#template_name').val(content.template.name);
                $('#therms_link').val(content.template.terms);
                $('#privacy_link').val(content.template.privacy);
                $('#contact_link').val(content.template.contact_us);
                $('#notification_email').val(content.template.notification_email);
               
                $('#contact_type').val(content.template.contact_type);
                $('#integration_id').val(content.template.integration_id);
                $('#redirect_after').val(content.template.redirect_after);
                $('#email_subject').val(content.template.email_subject);
                $('#template_return_email_content').val(content.template.email_message);
                $('#current_edited_template_id').val(template_id);
      

                setTimeout(function () {
                    submit_add = 1;
                }, 100);
                
                      if(content.template.contact_type == 0){
            $('#integration_id').parent().parent().hide();
            $('#notification_email').parent().parent().show();
        }else{
            //   $('#integration_id').parent().parent().show();
            $('#notification_email').parent().parent().hide();
            $('#integration_id').html('');
            $('#integration_id').data('integration-id', content.template.integration_id);
            $('#contact_type').trigger('change');
        }
            
            
            },
            error: function (errordata) {
                if (errordata.status == 401) {
                    show_reloging_info();
                } else if (errordata.status == 500) {
                    show_reloging_info();
                }
            }
        });


    });

    //FORM VALIDATION
    jQuery.validator.addMethod("contactUrl", function(value, element) {
    	  return this.optional(element) ||
    	         /^https?:\/\/(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( value ) ||
    	         (/^mailto:(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!\.)){0,61}[a-zA-Z0-9]?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!$)){0,61}[a-zA-Z0-9]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))([?]([a-zA-Z0-9]+=[a-zA-Z0-9@.\s]+&?)+)?$/).test(value)
	}, "The value you put in the Contact US URL is not correct. It should be formatted as <strong>http(s)://www.google.com</strong> or <strong>mailto:email@gmail.com</strong>.");
    

    $('#save_User_template_form').validate({
		  rules: {
		    contact_link: {
		    	contactUrl: true
			}
		  },
		  submitHandler: function(form) {
			    template_content              = CKEDITOR.instances.template_content.getData();	
		        therms_link                   = $('#therms_link').val();
		        privacy_link                  = $('#privacy_link').val();
		        contact_link                  = $('#contact_link').val();
		        current_edited_template_id    = $('#current_edited_template_id').val();
		        notification_email            = $('#notification_email').val() != '' && $('#notification_email').val() != null ?$('#notification_email').val():'0';
		        contact_type                  = $('#contact_type').val();
		        integration_id                = $('#integration_id').val() != null ?$('#integration_id').val():'0';
		        redirect_after                = $('#redirect_after').val();
		        email_subject                 = $('#email_subject').val();
		        template_return_email_content = $('#template_return_email_content').val();
		        template_name                 = $('#template_name').val();
		        org_tmp_id                    = $('#selector_for_' + template_id).val();
		        
		        if (
		                /*therms_link.trim().length == 0 || */
		                template_content.trim().length == 0 ||
		                /* contact_link.trim().length == 0 || */
		                /* privacy_link.trim().length == 0 || */
		                integration_id.trim().length == 0 ||
		                notification_email.trim().length == 0 ||
		                email_subject.trim().length == 0 ||
		                template_return_email_content.trim().length == 0 ||
		                template_name.trim().length == 0
		            ) {
		                BootstrapDialog.show({
		                    type: BootstrapDialog.TYPE_DANGER,
		                    title: 'Error',
		                    message: 'Please  Fill all fields',
		                    buttons: [{
		                        label: 'Ok',
		                        action: function (dialogItself) {
		                            dialogItself.close();


		                        }
		                    }]
		                });
		            }else if(
		                (therms_link.trim() == 0 ||  ( therms_link.trim().length > 0 && isUrlValid(therms_link.trim()) )) &&
		                (contact_link.trim() == 0 || ( contact_link.trim().length > 0 && isUrlValid(contact_link.trim()) )) &&
		                (privacy_link.trim() == 0 || ( privacy_link.trim().length > 0 && isUrlValid(privacy_link.trim()) ))

		            ){
		                $.ajax({
		                    url: base_url + '/campaigns/save_user_template',
		                    type: 'post',
		                    dataType: "json",
		                    headers: {
		                        'X-CSRF-TOKEN': $('meta[name="a-token"]').attr('content')
		                    },
		                    cache: false,
		                    data: {
		                        template_id: current_edited_template_id,
		                        page_content: template_content,
		                        template_name: template_name,
		                        template_return_email_content: template_return_email_content,
		                        email_subject: email_subject,
		                        redirect_after: redirect_after,
		                        notification_email: notification_email,
		                        contact_type: contact_type,
		                        integration_id: integration_id,
		                        contact_link: contact_link,
		                        privacy_link: privacy_link,
		                        org_tmp_id: org_tmp_id,
		                        therms_link: therms_link

		                    },
		                    success: function (content) {

		                        BootstrapDialog.show({
		                            type: BootstrapDialog.TYPE_SUCCESS,
		                            title: 'Success',
		                            message: 'Template Saved',
		                            buttons: [{
		                                label: 'Ok',
		                                action: function (dialogItself) {
		                                    dialogItself.close();

		                                    $('#edit_template_holder_column').hide();
		                                    $("#template_editing_manual").show();
		                                    new_tmp_content = 'Variation : (' + template_name + ')</br>' +
		                                    ' Redirect url : ' + redirect_after + ' </br> ' +
		                                    'Notify E-mail : ' + notification_email + ' </br> ' +
		                                    'E-mail Subject :' + email_subject + ' </br>'

		                                    ;
		                                    $('.dd-selected-description', '#selector_for_' + current_edited_template_id + '-dd-placeholder').html(new_tmp_content);
		                                    $('.dd-option-description', '#selector_for_' + current_edited_template_id + '-dd-placeholder').html(new_tmp_content);
		                                }
		                            }]
		                        });
		                    },
		                    error: function (errordata) {
		                        if (errordata.status == 401) {
		                            show_reloging_info();
		                        } else if (errordata.status == 500) {
		                            show_reloging_info();
		                        }
		                    }
		                });

		            } else {
		                BootstrapDialog.show({
		                    type: BootstrapDialog.TYPE_DANGER,
		                    title: 'Error',
		                    message: 'URL fields must start with http:// or https://, and be formated like http://www.example.com or http://example.com  ',
		                    buttons: [{
		                        label: 'Ok',
		                        action: function (dialogItself) {
		                            dialogItself.close();


		                        }
		                    }]
		                });

		            }
		  }
	});
    	  
    $(document).on('click', '#save_tmp_changes', function (e) {
        e.preventDefault();
        $('#save_User_template_form').submit();
    });

    // listen for pixel model being launched
    $('#pixelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var pixel_url = button.attr('data-pixel-url'); // Extract info from data-* attributes
        // update the modal's content to include the pixel url
        var modal = $(this);
        modal.find('#pixel_url_container').html(pixel_url);

        // initialize "Copy to clipboard" flash button
        var clientText = new ZeroClipboard($("#copy-url-to-cb"), {
            moviePath: base_url + "/swf/ZeroClipboard.swf",
            debug: false
        });
    });


    if ($('#add_edit_campaign_form').length) {
        $("#add_edit_campaign_form").submit(function (event) {
            var total_percentage = 0;
            $.each($('.percent_container'), function (index, value) {
                total_percentage += parseFloat($(value).val());
            });
            if (total_percentage < 99 || total_percentage > 101) {
                event.preventDefault();
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: 'Error',
                    message: 'Please Adjust you teplates so the combined value is 100%',
                    buttons: [{
                        label: 'Ok',
                        action: function (dialogItself) {
                            dialogItself.close();

                        }
                    }]
                });

            }
        });
    }
    if ($('div').hasClass('reportrange')) {
        //var datatable_events = $('#example').dataTable({
        var datatable_events = $('#stat-summary').dataTable({
            "processing": true,
            "serverSide": true,

            "ajax": {
                "type": "POST",
                "url": base_url + "/campaigns/str/get_data/" + $('#campaign_id').val(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="a-token"]').attr('content')
                },
                "data": function (d) {
                    d.datefrom = $('#filter_start_date').val();
                    d.dateto = $('#filter_end_date').val();
                },
                "error": function (xhr, status, error) {
                    window.location = base_url;
                },
                "dataSrc": function (json) {
                    if (Object.size(json.graph) > 0) {

                        chart_id = 'graph';

                        $('#graph').remove();
                        $('#canvas_holder').add('<canvas id="graph" class="col-md-12" height="50"></canvas>').appendTo($('#canvas_holder'));

                        var ctx = document.getElementById(chart_id).getContext("2d");
                        ctx.clearRect(0, 0, $('#' + chart_id).width(), $('#canvas_holder').height());

                        //var myChart = new Chart(ctx).Line(json.graph, {
                        var myChart = new Chart(ctx).Bar(json.graph, {
                            responsive: true,
                            tooltipTemplate: function (valuesObject) {
                                if (valuesObject.value > 0) {
                                    return valuesObject.datasetLabel + ' : ' + valuesObject.value
                                } else {
                                    return valuesObject.value;
                                }
                            },
                            scaleLabel: "<%=value%>",
                            multiTooltipTemplate: function (valuesObject) {
                                if (valuesObject.value > 0) {
                                    return valuesObject.datasetLabel + ' : ' + valuesObject.value
                                } else {
                                    return valuesObject.value;
                                }
                            }
                        });
                    }
                    //return json.data;
                    json.recordsTotal = json.summary.length;
                    json.recordsFiltered = json.summary.length;
                    return json.summary;
                }

            },
            responsive: true,
            "searching": false,
            /*
            "columns": [
                {"data": "name", "name": 'name', "orderable": false},
                {"data": "event", "name": 'created_at'},
                {"data": "link_text", "name": 'dosage'},
                {"data": "from", "name": 'dosage'},
                {"data": "time", "name": 'dosage'}
            ],
            */
            "columns": [
                {"data": "name", "name": 'name', "orderable": false}, // template name
                {"data": "total_opened", "name": 'total_opened'}, // impressions
                {"data": "total_mailto", "name": 'total_mailto'}, // clicks
                {"data": "click_percent", "name": 'click_percent'}, // click % (clicks / impressions)
                {"data": "total_optin", "name": 'total_optin', "defaultContent": ''}, // optins
                {"data": "optin_percent", "name": 'optin_percent', "defaultContent": ''}, // optin % (optins / impressions)
                {"data": "total_pixel", "name": 'total_pixel', "defaultContent": ''},
                {"data": "pixel_percent", "name": 'pixel_percent', "defaultContent": ''},
                {"data": "pixel_url", "name": 'pixel_url', render: function(data, type, full, meta) {
                    // button to display modal dialog with conversion tracking pixel url
                    return '<button class="btn btn-default show_hosted_url" data-pixel-url="' + data + '" data-toggle="modal" data-target="#pixelModal"><i class="glyphicon glyphicon-link"></i></button>';
                }}
            ],
            //"order": [[4, "desc"]]
            "order": [[0, "desc"]]
        });


        $('div#reportrange').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function (start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#reportrange input#filter_start_date').val(start.format('DD-MM-YYYY'));
                $('#reportrange input#filter_end_date').val(end.format('DD-MM-YYYY'));
            });


        $('div#reportrange').on('apply.daterangepicker', function (ev, picker) {
            //do something, like clearing an input
            datatable_events.api().ajax.reload();
        });
    }

    if ($('div').hasClass('campaings_range')) {


        $('div#campaings_range').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function (start, end) {
                $('#campaings_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#campaings_range input#filter_start_date').val(start.format('DD-MM-YYYY'));
                $('#campaings_range input#filter_end_date').val(end.format('DD-MM-YYYY'));
            });


        $('div#campaings_range').on('apply.daterangepicker', function (ev, picker) {


            $.ajax({
                url: base_url + '/campaigns/get_fresh_stats',
                type: 'post',
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="a-token"]').attr('content')
                },
                cache: false,
                data: {
                    from: $('#filter_start_date').val(),
                    to: $('#filter_end_date').val()

                },
                success: function (content) {

                    $.each(content, function (index, value) {
                        if ($('#stats_for_campaign_id_' + index).length) {
                            $('#stats_for_campaign_id_' + index).html(value.html);
                            $.each($('.list_options>tbody>tr'), function (non, element) {

                                if ($(element).data('campaign_id') == index) {
                                    $('.total_number_of_variation_for_campaign', $(element)).html(value.totalamount);
                                }

                            });
                            if ($('.Curently_displayed_quick_stats').length) {
                                cid = $('.Curently_displayed_quick_stats').data('campaign_id');
                                $('.stats_holder').html($('#stats_for_campaign_id_' + cid).html());

                            }


                        }
                    });
                },
                error: function (errordata) {
                    if (errordata.status == 401) {
                        show_reloging_info();
                    } else if (errordata.status == 500) {
                        show_reloging_info();
                    }
                }
            });


        });
    }

    $(document).on('click', '#campaign_table>tbody>tr>td:not(.slide_table_column)', function () {
        var campaing_id = $(this).parent().data('campaign_id');
        $('tr').removeClass('Curently_displayed_quick_stats');
        $(this).parent().addClass('Curently_displayed_quick_stats');
        $('.stats_holder').html($('#stats_for_campaign_id_' + campaing_id).html());
        $('[id^="hidden_row"]').show();

    });

    if ($('#doughnut_user_actions').length) {
        var ctxd = document.getElementById("doughnut_user_actions").getContext("2d");
        var myDoughnutChart = new Chart(ctxd).Doughnut(all_campaings_doughnut, {
            responsive: true,
            percentageInnerCutout: 65,
            animateScale: true,
            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
        });
        $('#doughnut_user_actions_ledend').html(myDoughnutChart.generateLegend());
        $(document).on('click', '#doughnut_user_actions', function (evt) {

            var activePoints = myDoughnutChart.getSegmentsAtEvent(evt);


            $('#label_Of_dgh').html(activePoints[0].label);
            $('#value_Of_dgh').html(activePoints[0].value);
        });

    }
    if ($('#visits_click_line').length) {
        var ctxl = document.getElementById('visits_click_line').getContext("2d");

        var linear_visits = new Chart(ctxl).Line(all_campaigns_linedata, {
            responsive: true,

            barValueSpacing: 5,
            barDatasetSpacing: 1,

            multiTooltipTemplate: "<%= datasetLabel %> : <%= value %>",
            legendTemplate: "<ul class=\"linedashboard-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

        });
        document.getElementById('linear_visit_legend').innerHTML = linear_visits.generateLegend();


    }
    $('[data-toggle="tooltip"]').tooltip({trigger: 'click hover', width: '300px'})
})
;

