(function($) {
  'use strict';
	$(document).ready(function(){
		var selOpt 			= ".xolo_hotspot_format option:selected";
		var $pins_txt_lbl   = $('input.pins_txt_lbl').val();
		var $pins_image 	= $('input.pins_image').val();
		function xolo_format_ch(){
			if ($(selOpt).val() == "imageLabel") {
		    	var pins_view = $pins_image;
		    	$("p.setIcon").replaceWith(function() {
					return $('<img class="setIcon">', {
				  	html: $(this).html()
				  }).attr("src",pins_view);
				});
		    } else {
		    	var pins_view = $pins_txt_lbl;
		    	$("img.setIcon").replaceWith(function() {
					return $('<p class="setIcon">', {
				  	html: $(this).html()
				  }).text(pins_view);
				});
			}
		}
		function xolo_format(){
			if ($(selOpt).val() == "imageLabel") {        
				$('.xolo_text_label').hide();
				$('.xolo_img_label').show();
				$('.custom_text').hide();
				$('.custom_image').show();
		    } else {			
				$('.xolo_img_label').hide();
				$('.xolo_text_label').show();
				$('.custom_image').hide();
				$('.custom_text').show();			
			}
			xolo_format_ch();
			coreProperties();
		}
		xolo_format();

		$(document).on('change','.xolo_hotspot_format', xolo_format);

		// Instantiates the variable that holds the media library frame.
		var xolo_hotspot_frame;
		// Sets up the media library frame
		function media_wp(){
			xolo_hotspot_frame = wp.media.frames.xolo_hotspot_frame = wp.media({
				title: xolo_hotspot.title,
				button: { text:  xolo_hotspot.button },
				library: { type: 'image' },
				multiple: false
			});
		}
		// Runs when the image button is clicked.
		$(document).on('click','[id*=meta_img_btn]',function(e){		
			e.preventDefault();		
			media_wp();
			// Runs when an image is selected.
			xolo_hotspot_frame.on('select', function(){
				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attach = xolo_hotspot_frame.state().get('selection').first().toJSON();
				// Sends the attachment URL to our custom image input field.
				$('.xolo_image_wrap').addClass('is-pin');
				$('#banner_images').val(media_attach.url);
				if($('#hotspot_panel .xolo_img_wrap img').length > 0){
					$('#hotspot_panel .xolo_img_wrap img').attr('src',media_attach.url);
				}else{
					$('#hotspot_panel .xolo_img_wrap').html('<img src="'+media_attach.url+'">');
				}
			});
			// Opens the media library frame.
			xolo_hotspot_frame.open();
		});
		$(document).on('click','.button-upload',function(e){
			// Prevents the default action from occuring.
			e.preventDefault();
			media_wp();
			var thisUpload = $(this).parents('.xolo_upload_image');
			// Runs when an image is selected.
			xolo_hotspot_frame.on('select', function(){
				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attach = xolo_hotspot_frame.state().get('selection').first().toJSON();
				// Sends the attachment URL to our custom image input field.
				thisUpload.addClass('is-pin');
				thisUpload.find('input[type="hidden"]').val(media_attach.url);
				thisUpload.find('img.image_view').attr('src',media_attach.url);
				$('body').find('img.setIcon').attr('src',media_attach.url);
				cal_position_custom();
			});
			// Opens the media library frame.
			xolo_hotspot_frame.open();
		});
		$(document).on('click','.btnUploadSingle',function(e){
			// Prevents the default action from occuring.
			e.preventDefault();
			media_wp();
			// Prevents the default action from occuring.		
			var thisUpload = $(this).parents('.xolo_upload_image');
			var id = $(this).parents('.list_points').attr('data-points');
			// Runs when an image is selected.
			xolo_hotspot_frame.on('select', function(){
				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attach = xolo_hotspot_frame.state().get('selection').first().toJSON();
				// Sends the attachment URL to our custom image input field.
				thisUpload.addClass('is-pin');
				thisUpload.find('input[type="hidden"]').val(media_attach.url);
				thisUpload.find('img.image_view').attr('src',media_attach.url);
				$('#draggable'+id).find('img').attr('src',media_attach.url);			
			});
			// Opens the media library frame.
			xolo_hotspot_frame.open();
		});
		// var let_me_check = function(event, ui, element) {
	 	//  	element = $(element);
	 	//  	var left = ui.position.left,
		// 		top  = ui.position.top;
		// 	var width = element.width(),
		// 		height = element.height();
		// 	$('#live_check').text('X: ' +left +' ' + 'Y: '+top).append('<br>W: '+width+' H: '+ height).append('<br>top: '+(top/height)*100 +'%;left: '+ (left/width)*100+'%;');
		// }
		
	  	function doDraggable(){
	  		$('.drag_wrap').draggable({
		  	  	containment: '#hotspot_panel',
		  	  	drag: function( event, ui ) {
		  	  		//let_me_check(event, ui, '#hotspot_panel');
		  	  	},
		  	  	stop: function( event, ui ) {
		  	  		var thisPoint = ui.helper[0].id;
		  	  		var dataPoint = $('#'+thisPoint).attr('data-points');
		  	  		var element = $('#hotspot_panel');
			  	  	var left = ui.position.left,
						top  = ui.position.top;
					var width = element.width(),
						height = element.height();
					var topPosition = ((top/height)*100).toFixed(2),
						leftPosition = ((left/width)*100).toFixed(2);				
					
		  	  		$('.xolo_points #info_draggable'+dataPoint+' input[name="pointdata[top][]"]').val(topPosition);
		  	  		$('.xolo_points #info_draggable'+dataPoint+' input[name="pointdata[left][]"]').val(leftPosition);	
	  			}
		  	});
	  	}  	
	  	doDraggable();

	  	function addPoint(){
	  		$(document).on('click','.add_point',function(){

				$pins_txt_lbl   = $('input.pins_txt_lbl').val();
				$pins_image 	= $('input.pins_image').val();

		  		if ($(selOpt).val() == "imageLabel") {
			    	if(!$pins_image) {
			  			(!$pins_txt_lbl && !$pins_image) ? alert('Add pins then point.') : alert('Please set your image and then your pin will appear.');
			  			return false;
			  		}
					var pins_view = $pins_image;
			    } else {
			    	if(!$pins_txt_lbl){		  			
			  			(!$pins_txt_lbl && !$pins_image) ? alert('Add pins then point.') : alert('Please set your text and then your pin will appear.');
			  			return false;
			  		}
					var pins_view = $pins_txt_lbl;
				}

				$(document).on('change','.xolo_hotspot_format', xolo_format);
		  		
		  		var countPoint = parseInt($('.xolo_wrap .drag_wrap').last().attr('data-points'));
		  		var nonceForm = $('#maps_points_meta_box_nonce').val();
		  		if(!countPoint) countPoint = 0;
		  		countPoint = countPoint + 1;
		  		var fullId = 'point_content'+countPoint; 		

		  		$.ajax({
		  			type : "post",
					dataType : "json",
					url: xolo_hotspot.ajaxurl,
					data : {
						action		 :	"xolo_hotspot_clone_point", 
						countpoint 	 : 	countPoint,
						txt_pins	 : 	pins_view,
						img_pins	 : 	pins_view,
						nonce		 : 	nonceForm
					},
					context: this,
					beforeSend: function(){
						$(this).parent().addClass('adding_point');
					},
					success: function(response) {
						if(response.success === true) {
							var data = response.data;
							$('.xolo_wrap').append(data.point_pins);  
					  		$('.xolo_points').append(data.point_data);				  		
					  		
					  		/* this is need for the tabs to work 
					  		source https://github.com/ccbgs/load_editor
					  		*/
							quicktags({id : fullId});
							tinymce.init({
								selector:"#" + fullId,
								content_css : xolo_hotspot.editor_style,
								min_height: 200,
						        textarea_name: "pointdata[content][]",						
								relative_urls:false,
								remove_script_host:false,
								convert_urls:false,
								browser_spellcheck:false,
								fix_list_elements:true,
								entities:"38,amp,60,lt,62,gt",
								entity_encoding:"raw",
								keep_styles:false,
								//paste_webkit_styles:"font-weight font-style color",
								//preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",
								wpeditimage_disable_captions:false,
								wpeditimage_html5_captions:true,
								plugins:"charmap,hr,media,paste,tabfocus,textcolor,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview",						
								resize:"vertical",
								menubar:false,
								wpautop:true,
								indent:false,
								toolbar1:"bold,italic,strikethrough,fontsizeselect,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_adv",
								toolbar2:"formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
								toolbar3:"",
								toolbar4:"",
								tabfocus_elements:":prev,:next",									
							});
							
							// this is needed for the editor to initiate
							tinyMCE.execCommand('mceFocus', false, fullId);
							tinyMCE.execCommand('mceRemoveEditor', false, fullId);
							tinyMCE.execCommand('mceAddEditor', false, fullId); 					
							
					  		doDraggable();
					  		cal_position_custom();
					  		xolo_format();

					  		$(this).parent().removeClass('adding_point');
						}else {
						   alert("Try again!");
						}
					}
		  		}); 
		  		return false;  		
		  	});
	  	}
	  	addPoint();
	  	
	  	$(document).on('click','.button_delete',function(){
	  		var id = $(this).parents('.list_points').attr('data-points');
	        $('[data-popup="info_draggable' + id + '"]').fadeOut(350, function(){
	            $('#info_draggable'+id).remove();
	            $('#draggable'+id).remove();
	        });
	  		return false;
	  	});
	  	$(document).on('click','.xolo_delete_img',function(){
	  		var parent = $(this).parents('.xolo_upload_image');
	  		parent.removeClass('is-pin');
	  		parent.find('input[type="hidden"]').val('');
	  		$('body').find('img.setIcon').attr('src','');
	  		return false;
	  	});
	  	$(document).on('click','.xolo_del_custom',function(){
	  		var pins_view = $('.pins_image').val();
	  		var id = $(this).parents('.list_points').attr('data-points');
	  		var parent = $(this).parents('.xolo_upload_image');
	  		parent.removeClass('is-pin');
	  		parent.find('input[type="hidden"]').val('');
	  		$('#draggable'+id).find('img.setIcon').attr('src',pins_view);
	  		return false;
	  	});
	  	function cal_position(position = 'center_center',$is_hover = false,$return = 'top'){
	  		var $r_top = 0;
			var $r_left = 0;		
			if($is_hover){
				var $width = $('.pins_hover').width(),
		  			$height = $('.pins_hover').height(),
		  			$custom_top = $('[name="custom_hover_top"]').val(),
		  			$custom_left = $('[name="custom_hover_left"]').val();
			}else{
				var $width = $('.pins_item').width(),
					$height = $('.pins_item').height(),
					$custom_top = $('[name="custom_top"]').val(),
					$custom_left = $('[name="custom_left"]').val();
			}
	  		switch (position){
				case 'center_center':
					$r_top = $height/2;
					$r_left = $width/2;
					break;
				case 'top_center':
					$r_top = 0;
					$r_left = $width/2;
					break;
				case 'top_right':
					$r_top = 0;
					$r_left = $width;
					break;
				case 'top_left':
					$r_top = 0;
					$r_left = 0;
					break;
				case 'right_center':
					$r_top = $height/2;
					$r_left = $width;
					break;
				case 'bottom_center':
					$r_top = $height;
					$r_left = $width/2;
					break;
				case 'bottom_right':
					$r_top = $height;
					$r_left = $width;
					break;
				case 'bottom_left':
					$r_top = $height;
					$r_left = 0;
					break;
				case 'left_center':
					$r_top = $height/2;
					$r_left = 0;
					break;
				case 'custom_center':
					$r_top = $custom_top;
					$r_left = $custom_left;
					break;
				default:
					$r_top = $height/2;
					$r_left = $width/2;
					break;
			}
	  		if($return == 'top'){
	  			return $r_top;
	  		}else{
	  			return $r_left;
	  		}
	  	}
	  	function point_position(position = 'center_center'){  		
			$('[name="custom_top"]').val(cal_position(position,false,'top')),
			$('[name="custom_left"]').val(cal_position(position,false,'left'));
			$('[name="custom_hover_top"]').val(cal_position(position,true,'top')),
			$('[name="custom_hover_left"]').val(cal_position(position,true,'left'));
			$('.point_style').each(function(){
				$(this).css({
					'top':'-'+cal_position(position,false,'top')+'px',
					'left':'-'+cal_position(position,false,'left')+'px'
				});
			});
			$(document).on('change keyup paste','.point_style',function(){
				$(this).css({
					'top':'-'+cal_position(position,false,'top')+'px',
					'left':'-'+cal_position(position,false,'left')+'px'
				});
			});
		}
	  	cal_position_custom();
	  	function cal_position_custom(){
	  		var itemVal = $('.xolo_hotspot_position option:selected').val();
	  		point_position(itemVal);
	  	}
	  	$(document).on('change','.xolo_hotspot_position',function(){
	  		var itemVal = $('.xolo_hotspot_position option:selected').val();
	  		point_position(itemVal);
	  		return false;
	  	});
	  	$(document).on('change','[name="custom_top"], [name="custom_left"]',function(){
	  		var itemVal = $('.xolo_hotspot_position option:selected').val();
	  		if(itemVal == 'custom_center')
	  		point_position(itemVal);
	  		return false;
	  	});
	    /*$(document).on('click','.pins_click_to_edit',function(){
	        var target = $(this).data('target')
	        //$(target).modal();
	        $(target).bPopup({
	            content:'iframe'
	        });
	        return false;
	    });*/
	    $(document).on('click','[data-popup-open]',function(e){
	        var targeted_popup_class = jQuery(this).attr('data-popup-open');
	        $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
	        e.preventDefault();
	    });
	    $(document).on('click','[data-popup-close]',function(e){
	        var targeted_popup_class = jQuery(this).attr('data-popup-close');
	        $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
	        e.preventDefault();
	    });

	    // Pins Color Properties
	    function initColorPicker() {
	        $('body').find( '.my-color-picker' ).wpColorPicker({
	            change: _.throttle( function() { // For Customizer
	                $(this).trigger( 'change' );
	            }, 3000 )
	        });        
	    }
	    $('.my-color-picker').each(initColorPicker);
		
		// Pins Font Size Setting
	    function pinsFontSize() {
			var customFont = $('[id^="sliderSize"]').val();
		    $('.pins_click_to_edit').css("font-size", customFont + "px");
		    $('img.setIcon').css("max-width", customFont + "px");
		}

		// Pins Text Change Setting
		function pins_txt_global(){
			var pins_txt_name = $(this);
			var pins_txt = pins_txt_name.val();
			$('p.setIcon').text(pins_txt);
		}
		// Pins Custom Text Change Setting
		function pins_txt_cust(){
			var pins_txt_name = $(this);
			var pins_txt_single = pins_txt_name.val();
			var id = pins_txt_name.parents('.list_points').attr('data-points');
			$('#draggable'+id).find('p').text(pins_txt_single);
		}

		//coreProperties
		function coreProperties() {
			// Pins Text
			$('.pins_txt_lbl').each(pins_txt_cust);
			$(document).on('change keyup paste', '.pins_txt_lbl', pins_txt_global);
			$(document).on('change keyup paste', '.pins_txt_custom', pins_txt_cust);

			// Pins Font Size
			$('.pins_click_to_edit').each(pinsFontSize);
			$(document).on('change keyup paste', 'input[name="custom_pin_size"]', pinsFontSize);

			// Pins Color
			$(document).on('change keyup paste','input[name="custom_color"]',function(){
			    $(".pins_click_to_edit").css("color", $(this).val());
			});
			$('input[name="custom_color"]').each(function(){
			    $(".pins_click_to_edit").css("color", $(this).val());
			});

			// Reset Setting
			$(document).on('click','[id^="reset-size"]',function(){
		  		var defSizeValue = $('[id^="valueSize"]').attr('data-value');
		  		$('input[name="custom_pin_size"]').val(defSizeValue);
		  		$('.pins_click_to_edit').css("font-size", defSizeValue + "px");
		  		$('img.setIcon').css("max-width", defSizeValue + "px");
		  		return false;
		  	});	
		}
		//coreProperties();
	});
}(jQuery));