<?php
/*
Plugin Name: Hotspot
Plugin URI: 
Description: Create an awesome pins for your image. It can be use for any highlighted points and dots on your image.
Version: 1.1
Author: xolosoftware
Author URI: https://profiles.wordpress.org/xolosoftware/
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Text Domain: hotspot
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('XOLO_HOTSPOT_VER', '1.0');
define('XOLO_HOTSPOT_MOD', false);
define( 'XOLO_HOTSPOT_DIR_URL', plugin_dir_url( __FILE__ ) );

define('XOLO_HOTSPOT_DEFAULT_POINT',serialize(array(
	'countPoint'	=>	'',
	'content'		=>	'',
	'left'			=>	'',
	'top'			=>	'',
	'linkpins'		=>	'',
	'link_target'	=>	'',
	'placement'     =>  '',
	'pins_id'       =>  '',
	'pins_class'    =>  ''
)));
define('XOLO_HOTSPOT_DEFAULT_PINS',serialize(array(
	'countPoint'		=>	'',
	'spotType'			=>	'',
	'txtPoint'			=>	'',
	'imgPoint'			=>	'',
	'pins_txt_custom' 	=>  '',
	'pins_image_custom' =>  '',
	'top'				=>	'',
	'left'				=>	''
)));
//include
include 'admin/inc/cpt-hotspot.php';
include 'admin/inc/add_shortcode_xolo_hotspot.php';
include 'admin/inc/metabox-upgrade.php';

//metabox
function xolo_hotspot_meta_box() {
	//post type
	$screens = array( 'points_pin' );

	foreach ( $screens as $screen ) {
		add_meta_box(
			'xolo-hotspot-metabox',
			__( 'Hotspot', 'hotspot' ),
			'xolo_hotspot_meta_box_callback',
			$screen,
			'normal',
			'high'
		);
		add_meta_box(
			'xolo-hotspot-shortcode',
			__( 'Hotspot Shortcode', 'hotspot' ),
			'xolo_hotspot_shortcode_callback',
			$screen,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'xolo_hotspot_meta_box' );

function xolo_wp_default_editor(){
    return "tinymce";
}

function xolo_hotspot_meta_box_callback( $post ) {
    add_filter( 'wp_default_editor', 'xolo_wp_default_editor' );
	//add none field
	wp_nonce_field( 'maps_points_save_meta_box_data', 'maps_points_meta_box_nonce' );

	$data_post = get_post_meta($post->ID, 'hotspot_content', true);

	if(!$data_post){
	    $data_post = maybe_unserialize( $post->post_content );
	}

	$banner_images				= 	(isset($data_post['banner_images']))?$data_post['banner_images']:'';
	$data_points 				= 	(isset($data_post['data_points']))?$data_post['data_points']:'';
	$spot_type 					= 	(isset($data_post['spot_type']))?$data_post['spot_type']:'';
	$pins_txt_lbl 				= 	(isset($data_post['pins_txt_lbl']))?$data_post['pins_txt_lbl']:'';
	$pins_txt_lbl_hover			= 	(isset($data_post['pins_txt_lbl_hover']))?$data_post['pins_txt_lbl_hover']:'';
	$pins_image 				= 	(isset($data_post['pins_image']))?$data_post['pins_image']:'';
	$pins_image_hover 			= 	(isset($data_post['pins_image_hover']))?$data_post['pins_image_hover']:'';

	$pins_more_option 			= 	(isset($data_post['pins_more_option']))?$data_post['pins_more_option']:array();
	$pins_more_option 			= 	wp_parse_args($pins_more_option,array(
		'position'				=>	'top_left',
		'custom_top'			=>	 0,
		'custom_left'			=>	 0,
		'custom_hover_top'		=>	 0,
		'custom_hover_left'		=>	 0,
		'custom_pin_size'		=>   25,
		'custom_color'			=>   'inherit',
		'pins_animation'		=>	 'none'
	));
	?>
	<div class="xolo-group">
		<div class="xolo_hotspot_wrapper">
			<div class="xolo_spot_type">
	            <label class="xolo_hotspot_label mr-2"><?php _e('Spot Type','hotspot')?></label>
				<select id="spot_type" name="spot_type" class="xolo_hotspot_format">
					<?php $override_setting = array(
						'imageLabel'=>__('Image','hotspot'),
						'textLabel'=>__('Text','hotspot')); ?>
						<?php foreach($override_setting as $key => $value) { ?>
						<option value="<?php echo esc_attr($key); ?>" <?php if ($spot_type==$key) { echo 'selected="selected"'; } ?>  >
						<?php _e($value,'hotspot') ?></option>
					<?php } ?>
	            </select>
	        </div>
			<div class="xolo_text_label" style="display: none;">
	        	<div class="xolo_slot">
		            <label class="xolo_hotspot_label"><?php _e('Pins Text','hotspot')?></label>
		            <div class="xolo_form_group global-text">
			            <div class="xolo_upload_image <?=($pins_txt_lbl)?'is-pin':''?>">	
							<input type="text" name="pins_txt_lbl" class="pins_txt_lbl pins_item" value="<?php echo esc_attr($pins_txt_lbl); ?>" />
						</div>
					</div>
				</div>
				<div class="xolo_slot">
					<label class="xolo_hotspot_label"><?php _e('Pins Hover Text','hotspot')?></label>
					<div class="xolo_form_group global-text">
						<div class="xolo_upload_image <?=($pins_txt_lbl_hover)?'is-pin':''?>">
							<input type="text" name="pins_txt_lbl_hover" class="pins_txt_lbl_hover pins_hover" value="<?php echo esc_attr($pins_txt_lbl_hover); ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="xolo_img_label">
	        	<div class="xolo_slot">
		            <label class="xolo_hotspot_label"><?php _e('Pins Image','hotspot')?></label>
		            <div class="xolo_form_group">
			            <div class="xolo_upload_image <?=($pins_image)?'is-pin':''?>">
							<div class="show_target">
								<input type="hidden" name="pins_image" class="pins_image" value="<?php echo $pins_image; ?>" />						
								<img src="<?=$pins_image?>" class="image_view pins_item"/>									
								<a href="#" class="xolo_delete_img">x</a>
							</div>
							<div class="hide_target"><input type="button" class="button-upload button" value="<?php _e( 'Select pins', 'hotspot' )?>" /></div>
						</div>					
					</div>
				</div>
				<div class="xolo_slot">
					<label class="xolo_hotspot_label"><?php _e('Pins Hover Image','hotspot')?></label>
					<div class="xolo_form_group">
						<div class="xolo_upload_image <?=($pins_image_hover)?'is-pin':''?>">	
							<div class="show_target">
								<input type="hidden" name="pins_image_hover" class="pins_image_hover" value="<?php echo $pins_image_hover; ?>" />
								<img src="<?=$pins_image_hover?>" class="image_view pins_hover"/>
								<a href="#" class="xolo_delete_img">x</a>
							</div>
							<div class="hide_target"><input type="button" class="button-upload button" value="<?php _e( 'Select pins hover', 'hotspot' )?>" /></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="xolo-group">
		<div class="xolo_hotspot_radio">
			<div class="xolo_position">
				<label class="xolo_hotspot_label"><?php _e('Pins Properties :-','hotspot')?></label>
				<div class="xolo_form_group">
					<div class="xolo_pins_position">
						<div class="">
							<label class="xolo_hotspot_label"><?php _e('Position','hotspot')?></label>
							<select name="choose_type" class="xolo_hotspot_position">
								<?php
								$shape_setting = array(
									'center_center'		=>	__('Center-Center','hotspot'),
									'top_left'			=>	__('Top-Left','hotspot'),
									'top_center'		=>	__('Top-Center','hotspot'),
									'top_right'			=>	__('Top-Right','hotspot'),
									'right_center'		=>	__('Right-Center','hotspot'),
									'bottom_right'		=>	__('Bottom-Right','hotspot'),
									'bottom_center'		=>	__('Bottom-Center','hotspot'),
									'bottom_left'		=>	__('Bottom-Left','hotspot'),
									'left_center'		=>	__('Left-Center','hotspot'),
									'custom_center'		=>	__('Set-Custom-Position','hotspot')
								);
								foreach($shape_setting as $sk => $sv) {
								?>
								<option name="<?php echo esc_attr($sk); ?>" value="<?php echo esc_attr($sk); ?>" <?=($pins_more_option['position'] == $sk?'selected="selected"':'')?>><?php _e($sv,'hotspot') ?></option>								
								<?php } ?>
				            </select>
			            </div>
						<div class="xolo-range">
							<label class="xolo_hotspot_label"><?php _e('Top','hotspot')?> <input type="number" class="range_value" id="valueTop" name="custom_top" min="0" oninput="sliderTop.value=valueTop.value" value="<?=$pins_more_option['custom_top']?>"></label>
							<div class="input_range">
								<input type="range" class="range_slider" id="sliderTop" name="custom_top" min="0" oninput="valueTop.value=sliderTop.value" value="<?=$pins_more_option['custom_top']?>">
							</div>
						</div>
						<div class="xolo-range">
							<label class="xolo_hotspot_label"><?php _e('Left','hotspot')?> <input type="number" class="range_value" id="valueLeft" name="custom_left" min="0" oninput="sliderLeft.value=valueLeft.value" value="<?=$pins_more_option['custom_left']?>"></label>
							<div class="input_range">
								<input type="range" class="range_slider" id="sliderLeft" name="custom_left" min="0" oninput="valueLeft.value=sliderLeft.value" value="<?=$pins_more_option['custom_left']?>">
							</div>
						</div>
						<input type="hidden" name="custom_hover_top" value="<?=$pins_more_option['custom_hover_top']?>" min="0" step="any">
						<input type="hidden" name="custom_hover_left" value="<?=$pins_more_option['custom_hover_left']?>" min="0" step="any">
					</div>
				</div>				
			</div>
			<div class="xolo_properties">
				<div class="xolo_form_group">
					<div class="xolo_pins_position">
						<div class="xolo-range">
							<label class="xolo_hotspot_label"><?php _e('Pin-Size','hotspot')?></label>
							<input type="range" class="range_slider" id="sliderSize" name="custom_pin_size" min="0" max="200" oninput="valueSize.value=sliderSize.value" value="<?=$pins_more_option['custom_pin_size']?>">
							<input type="number" class="range_value" id="valueSize" name="custom_pin_size" min="0" max="200" oninput="sliderSize.value=valueSize.value" value="<?=$pins_more_option['custom_pin_size']?>" data-value="<?=$pins_more_option['custom_pin_size']?>">
							<span id="reset-size" class="reset-range" title="<?php _e( 'Reset', 'hotspot' ); ?>">
								<span class="dashicons dashicons-image-rotate"></span>
							</span>
						</div>
						<div>
							<label class="xolo_hotspot_label"><?php _e('Color','hotspot')?></label>
							<input type="text" name="custom_color" class="my-color-picker" value="<?=$pins_more_option['custom_color']?>">
						</div>
						<div class="xolo_pins_animation">
							<label class="xolo_hotspot_label"><?php _e('Pins Animation','hotspot')?></label>
							<select name="pins_animation" class="xolo_hotspot_animation">
								<?php
								$animation_setting = array(
									'none'				=>	__('None','hotspot'),
									'zoom'				=>	__('Zoom','hotspot'),
									'pulse_line'		=>	__('Pulse Line','hotspot')
								);
								foreach($animation_setting as $ak => $av) {
								?>
								<option value="<?php echo esc_attr($ak); ?>" <?=($pins_more_option['pins_animation'] == $ak?'selected="selected"':'')?>>
								<?php _e($av,'hotspot') ?></option>									
								<?php } ?>
				            </select>
			            </div>
					</div>
				</div>				
			</div>
		</div>
	</div>

	<div class="xolo_image_wrap <?=($banner_images)?'is-pin':''?>">	
	<div class="xolo_control">
		<input type="button" id="meta_img_btn" class="button" value="<?php _e( 'Upload Image', 'hotspot' )?>" />
		<input type="hidden" name="banner_images" class="banner_images" id="banner_images" value="<?php echo $banner_images; ?>" />
		<input type="button" name="add_point" class="add_point button show_target" value="<?php _e('Add Point','hotspot');?>"/>
		<span class="spinner"></span>
		<p class="note"><?php _e('Note: The position maybe different when you view it on your blog depends on the theme, so please focus on the blog, use here as a hint.','hotspot')?></p>
	</div>
	<p class="message"><?php _e(":) Preview HotSpot (You can drag the icon to update it's position, don't forget to save)",'hotspot')?></p>
	<div class="xolo_wrap show_target" id="hotspot_panel">		
		<div class="xolo_img_wrap">
			<?php if($banner_images):?>
				<img src="<?php echo esc_url($banner_images); ?>">
			<?php endif;?>
		</div>
		<?php if(is_array($data_points)):?>
			<?php $stt = 1;foreach ($data_points as $point):?>		 
			<?php 
		 	$data_input = array(
		 		'countPoint'				=>	$stt,			
				'txtPoint'					=>	$pins_txt_lbl,
				'imgPoint'					=>	$pins_image,
				'spotType'					=>	$spot_type,
				'top'						=>	$point['top'],
				'left'						=>	$point['left'],
				'linkpins'					=>	isset($point['linkpins'])?esc_url($point['linkpins']):'',
				'link_target'				=>	isset($point['link_target'])?esc_attr($point['link_target']):'_self',
				'pins_txt_custom'			=>	isset($point['pins_txt_custom'])?$point['pins_txt_custom']:'',
				'pins_txt_hover_custom'		=>	isset($point['pins_txt_hover_custom'])?$point['pins_txt_hover_custom']:'',
				'pins_image_custom'			=>	isset($point['pins_image_custom'])?$point['pins_image_custom']:'',
				'pins_image_hover_custom'	=>	isset($point['pins_image_hover_custom'])?$point['pins_image_hover_custom']:'',
				'placement'					=>	isset($point['placement'])?$point['placement']:'',
				'pins_id'					=>	isset($point['pins_id'])?$point['pins_id']:'',
				'pins_class'				=>	isset($point['pins_class'])?$point['pins_class']:''
		 	);
		 	echo xolo_hotspot_get_default_pins($data_input);?>
			<?php $stt++;endforeach;?>
		 <?php endif;?>
	 </div>
	 <div class="xolo_points">
	 <?php if(is_array($data_points)):?>
		 <?php $stt = 1;foreach ($data_points as $point):?>
		 	<?php 
		 	$data_input = array(
		 		'countPoint'				=>	$stt,
				'content'					=>	$point['content'],
				'left'						=>	$point['left'],
				'top'						=>	$point['top'],
				'linkpins'					=>	isset($point['linkpins'])?esc_url($point['linkpins']):'',
				'link_target'				=>	isset($point['link_target'])?esc_attr($point['link_target']):'_self',
				'pins_txt_custom'			=>	isset($point['pins_txt_custom'])?$point['pins_txt_custom']:'',
				'pins_txt_hover_custom'		=>	isset($point['pins_txt_hover_custom'])?$point['pins_txt_hover_custom']:'',
				'pins_image_custom'			=>	isset($point['pins_image_custom'])?$point['pins_image_custom']:'',
				'pins_image_hover_custom'	=>	isset($point['pins_image_hover_custom'])?$point['pins_image_hover_custom']:'',
				'placement'					=>	isset($point['placement'])?$point['placement']:'',
				'pins_id'					=>	isset($point['pins_id'])?$point['pins_id']:'',
				'pins_class'				=>	isset($point['pins_class'])?$point['pins_class']:''
		 	);
		 	echo xolo_hotspot_get_default_input_point($data_input);?> 
	 	 <?php $stt++;endforeach;?>
 	 <?php else:?>
 		<div style="display: none;"><?php wp_editor('', '_xolo_hotspot_default_content'); ?></div>
	 <?php endif;?>
	 </div>
	<?php
}
function xolo_hotspot_shortcode_callback( $post ){
	if(get_post_status($post->ID) == "publish"):
	?>
		<span><?php _e('Copy shortcode to view','hotspot')?></span>
		<input readonly="readonly" class="shortcodemap" value='[xolo_hotspot id="<?=$post->ID?>"]'/>
	<?php else:?>
		<span><?php _e('Publish to view shortcode','hotspot')?></span>
	<?php 
	endif;	
}
function xolo_hotspot_save_meta_box_data( $post_id ) {

	if ( ! isset( $_POST['maps_points_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['maps_points_meta_box_nonce'], 'maps_points_save_meta_box_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'points_pin' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	if ( ! isset( $_POST['banner_images'] ) ) {
		return;
	}

	$my_data = esc_url( (isset($_POST['banner_images']))?$_POST['banner_images']:'' );	
	
	$dataPoints = array();	
	
	/*sanitize in xolo_hotspot_convert_array_data*/
	$pointdata = (isset($_POST['pointdata']))?$_POST['pointdata']:'';		
	
	$choose_type = sanitize_text_field((isset($_POST['choose_type']))?$_POST['choose_type']:'');
	$choose_type = sanitize_text_field((isset($_POST['choose_type']))?$_POST['choose_type']:'');
	
	$custom_top = sanitize_text_field((isset($_POST['custom_top']))?$_POST['custom_top']:'');
	$custom_left = sanitize_text_field((isset($_POST['custom_left']))?$_POST['custom_left']:'');
	
	$custom_hover_top = sanitize_text_field((isset($_POST['custom_hover_top']))?$_POST['custom_hover_top']:'');
	$custom_hover_left = sanitize_text_field((isset($_POST['custom_hover_left']))?$_POST['custom_hover_left']:'');

	$custom_pin_size = sanitize_text_field((isset($_POST['custom_pin_size']))?$_POST['custom_pin_size']:'');

	$custom_color = sanitize_text_field((isset($_POST['custom_color']))?$_POST['custom_color']:'');
	
	$pins_animation = sanitize_text_field((isset($_POST['pins_animation']))?$_POST['pins_animation']:'');
	
	$pins_more_option = array(
		'position'				=>	$choose_type,
		'custom_top'			=>	$custom_top,
		'custom_left'			=>	$custom_left,
		'custom_hover_top'		=>	$custom_hover_top,
		'custom_hover_left'		=>	$custom_hover_left,
		'custom_pin_size'		=>  $custom_pin_size,
		'custom_color'			=>  $custom_color,
		'pins_animation'		=>	$pins_animation
	);
	if(is_array($pointdata)){
		$dataPoints = xolo_hotspot_convert_array_data($pointdata);
	}
	$data_post = array(
		'banner_images'			=>	$my_data,
		'spot_type'				=>	sanitize_text_field( (isset($_POST['spot_type']))?$_POST['spot_type']:'' ),
		'pins_txt_lbl'			=>	sanitize_text_field( (isset($_POST['pins_txt_lbl']))?$_POST['pins_txt_lbl']:'' ),
		'pins_txt_lbl_hover'	=>	sanitize_text_field( (isset($_POST['pins_txt_lbl_hover']))?$_POST['pins_txt_lbl_hover']:'' ),
		'pins_image'			=>	sanitize_text_field( (isset($_POST['pins_image']))?$_POST['pins_image']:'' ),
		'pins_image_hover'		=>	sanitize_text_field(isset($_POST['pins_image_hover'])?$_POST['pins_image_hover']:''),
		'pins_more_option'		=>	$pins_more_option,
		'data_points'			=>	$dataPoints
	);
	update_post_meta($post_id, 'hotspot_content', $data_post);
}
add_action( 'save_post', 'xolo_hotspot_save_meta_box_data' );

function xolo_hotspot_editor_styles(){
	
	global $wp_version;
	
	$baseurl = includes_url( 'js/tinymce' );
	
	$suffix = SCRIPT_DEBUG ? '' : '.min';
	$version = 'ver=' . $wp_version;
	$dashicons = includes_url( "css/dashicons$suffix.css?$version" );

	// WordPress default stylesheet and dashicons
	$mce_css = array(
		$dashicons,
		$baseurl . '/skins/wordpress/wp-content.css?' . $version
	);

	$editor_styles = get_editor_stylesheets();
	if ( ! empty( $editor_styles ) ) {
		foreach ( $editor_styles as $style ) {
			$mce_css[] = $style;
		}
	}
	
	$mce_css = trim( apply_filters( 'xolo_hotspot_mce_css', implode( ',', $mce_css ) ), ' ,' );

	if ( ! empty($mce_css) )
		return $mce_css;
	else
		return false;
	
}

/*Add admin script*/
function xolo_hotspot_admin_script() {
	global $typenow;
	if( $typenow == 'points_pin' ) {
		wp_enqueue_media();	
		
	    wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script('jquery-ui-droppable');

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');

		wp_register_script( 'xolo-hotspot-admin', plugin_dir_url( __FILE__ ) . 'assets/admin/js/xolo-hotspot-admin.js', array( 'jquery' ), XOLO_HOTSPOT_VER, true );
		wp_localize_script( 'xolo-hotspot-admin', 'xolo_hotspot',
			array(
				'title' 		=> __( 'Select image', 'hotspot' ),
				'button' 		=> __( 'Select', 'hotspot' ),
				'site_url'		=>	home_url(),
				'ajaxurl'		=>	admin_url('admin-ajax.php'),
				'editor_style'	=>	xolo_hotspot_editor_styles()
			)
		);
		wp_enqueue_script( 'xolo-hotspot-admin' );
	}
}
add_action( 'admin_enqueue_scripts','xolo_hotspot_admin_script' );

/*Add admin style*/
function xolo_hotspot_admin_styles(){
	global $typenow;
	if( $typenow == 'points_pin' ) {
		wp_enqueue_style( 'xolo-custom', plugin_dir_url( __FILE__ ) . 'assets/admin/css/xolo-custom.css', array(), XOLO_HOTSPOT_VER, 'all' );
		wp_enqueue_style( 'xolo-hotspot-admin', plugin_dir_url( __FILE__ ) . 'assets/admin/css/xolo-hotspot-admin.css', array(),XOLO_HOTSPOT_VER, 'all' );
	}
}
add_action( 'admin_print_styles', 'xolo_hotspot_admin_styles' );

/*Add public scripts*/
function xolo_hotspot_public_scripts() {
		wp_enqueue_script( 'powertip', plugin_dir_url( __FILE__ ) . 'assets/frontend/js/jquery.powertip.min.js', array('jquery'), XOLO_HOTSPOT_VER, true );
		wp_enqueue_style('xolo-hotspot-public',plugin_dir_url( __FILE__ ) . 'assets/frontend/css/xolo-hotspot-public.css',array(),XOLO_HOTSPOT_VER,'all');
		
		wp_enqueue_script( 'xolo-hotspot-public', plugin_dir_url( __FILE__ ) . 'assets/frontend/js/xolo-hotspot-public.js', array('jquery'), XOLO_HOTSPOT_VER, true );
}
add_action( 'wp_enqueue_scripts', 'xolo_hotspot_public_scripts' );

function xolo_hotspot_get_default_input_point($data = array()){
	if(!is_array($data)) $data = array();
	$data = wp_parse_args($data,unserialize(XOLO_HOTSPOT_DEFAULT_POINT));

	$countPoint 				= isset($data['countPoint'])?$data['countPoint']:'';
	$pointContent 				= isset($data['content'])?$data['content']:'';
	$pointLeft 					= isset($data['left'])?$data['left']:'';
	$pointTop 					= isset($data['top'])?$data['top']:'';
	$pointLink 					= isset($data['linkpins'])?$data['linkpins']:'';
	$link_target 				= isset($data['link_target'])?$data['link_target']:'_self';
	$pins_txt_custom			= isset($data['pins_txt_custom'])?$data['pins_txt_custom']:'';
	$pins_txt_hover_custom		= isset($data['pins_txt_hover_custom'])?$data['pins_txt_hover_custom']:'';
	$pins_image_custom			= isset($data['pins_image_custom'])?$data['pins_image_custom']:'';
	$pins_image_hover_custom	= isset($data['pins_image_hover_custom'])?$data['pins_image_hover_custom']:'';
	$placement					= isset($data['placement'])?$data['placement']:'';
	$pins_id					= isset($data['pins_id'])?$data['pins_id']:'';
	$pins_class					= isset($data['pins_class'])?$data['pins_class']:'';
	ob_start();
	?>	
	<div class="xolo_popup list_points" tabindex="-1" role="dialog" id="info_draggable<?php echo esc_attr($countPoint); ?>" data-popup="info_draggable<?php echo esc_attr($countPoint); ?>" data-points="<?php echo esc_attr($countPoint); ?>">
	 	<div class="xolo_popup-inner">
			<div class="xolo_popup_content">
				<div class="xolo_popup_header">
					<h3 class="modal_title"><?php _e('Content','hotspot')?></h3>
			  	</div>
		  		<div class="xolo_popup_body">
					<?php
                    add_filter( 'wp_default_editor', 'xolo_wp_default_editor' );
					$settings = array(
						'textarea_name'	=>	'pointdata[content][]',		
						'tabindex' => 4,
					      	'tinymce' => array(
						        'min_height'	=>	60,
								'toolbar1'		=>	'bold,italic,underline,fontsizeselect,alignleft,aligncenter,alignright,alignjustify,bullist,numlist,link,unlink,forecolor,undo,redo,wp_more',
								'toolbar2'		=>	'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
							),		
					);
					wp_editor($pointContent, 'point_content'.$countPoint, $settings);
					?>
					<div class="xolo_row">
						<!-- Custom Image Label Start -->
						<div class="xolo_col_3 custom_image">
							<label><?php _e('Pin Image Custom','hotspot');?></label>
							<div class="xolo_upload_image <?=($pins_image_custom)?'is-pin':''?>">						
								<div class="show_target">
									<input type="hidden" name="pointdata[pins_image_custom][]" class="pins_image" value="<?php echo $pins_image_custom; ?>" />								
									<img src="<?=$pins_image_custom?>" class="image_view pins_item"/>									
									<a href="#" class="xolo_del_custom">x</a>
								</div>
								<div class="hide_target"><input type="button" class="btnUploadSingle button" value="<?php _e( 'Select pins', 'hotspot' )?>" /></div>
							</div>
						</div>
						<div class="xolo_col_3 custom_image">
							<label><?php _e( 'Pins hover image custom', 'hotspot' )?></label>
							<div class="xolo_upload_image <?=($pins_image_hover_custom)?'is-pin':''?>">						
								<div class="show_target">
									<input type="hidden" name="pointdata[pins_image_hover_custom][]" class="pins_image_hover" value="<?php echo $pins_image_hover_custom; ?>" />								
									<img src="<?=$pins_image_hover_custom?>" class="image_view pins_hover"/>									
									<a href="#" class="xolo_del_custom">x</a>
								</div>
								<div class="hide_target"><input type="button" class="btnUploadSingle button" value="<?php _e( 'Select pins hover', 'hotspot' )?>" /></div>
							</div>
						</div>
						<!-- Custom Image Label End -->

						<!-- Custom Text Label Start -->
			        	<div class="xolo_col_3 custom_text">
				            <label class="xolo_hotspot_label"><?php _e('Pins Text Custom','hotspot')?></label>
				            <div class="xolo_form_group">
					            <div class="xolo_upload_image <?=($pins_txt_custom)?'is-pin':''?>">	
									<input type="text" name="pointdata[pins_txt_custom][]" class="pins_txt_custom pins_item" value="<?php echo esc_attr($pins_txt_custom); ?>" />
								</div>
							</div>
						</div>
						<div class="xolo_col_3 custom_text">
							<label class="xolo_hotspot_label"><?php _e('Pins Hover Text','hotspot')?></label>
							<div class="xolo_form_group">
								<div class="xolo_upload_image <?=($pins_txt_hover_custom)?'is-pin':''?>">
									<input type="text" name="pointdata[pins_txt_hover_custom][]" class="pins_txt_hover_custom pins_hover" value="<?php echo esc_attr($pins_txt_hover_custom); ?>" />
								</div>
							</div>
						</div>

						<!-- Custom Text Label End -->
						<div class="xolo_col_3">
							<label class="xolo_hotspot_label"><?php _e('Placement','hotspot')?></label>
							<select name="pointdata[placement][]">
							    <?php
							    $allPlacement = array(
                                    'n'  		=>  __('North'),
                                    'e'  		=>  __('East'),
                                    's'  		=>  __('South'),
                                    'w'  		=>  __('West'),
                                    'nw' 		=>  __('North West'),
                                    'ne' 		=>  __('North East'),
                                    'sw' 		=>  __('South West'),
                                    'se' 		=>  __('South East'),
                                    'nw-alt' 	=>  __('North West Alt'),
                                    'ne-alt' 	=>  __('North East Alt'),
                                    'sw-alt' 	=>  __('South West Alt'),
                                    'se-alt' 	=>  __('South East Alt')
							    );
							    foreach ($allPlacement as $k=>$v) {
                                ?>
							    <option value="<?php echo $k;?>" <?php selected($k,$placement)?>><?php echo $v;?></option>
							    <?php }?>
                            </select>
						</div>						
					</div>
					<div class="xolo_row">
						<div class="xolo_col_3">
							<label class="xolo_hotspot_label"><?php _e('Link to pins','hotspot'); ?></label>
							<input type="text" name="pointdata[linkpins][]" value="<?php echo esc_attr($pointLink); ?>" placeholder="Link to pins"/>
						</div>
						<div class="xolo_col_3">
							<label class="xolo_hotspot_label"><?php _e('Link target','hotspot'); ?></label>
							<select name="pointdata[link_target][]">
							    <option value="_self" <?php selected('_self',$link_target);?>><?php _e('Open curent window','hotspot'); ?></option>
							    <option value="_blank" <?php selected('_blank',$link_target);?>><?php _e('Open new window','hotspot'); ?></option>
							</select>							
						</div>
						<div class="xolo_col_3">
							<label class="xolo_hotspot_label"><?php _e('Pins ID','hotspot'); ?></label>
							<input type="text" name="pointdata[pins_id][]" value="<?php echo esc_attr($pins_id); ?>" placeholder="Type a ID"/>
                        </div>
                        <div class="xolo_col_3">
							<label class="xolo_hotspot_label"><?php _e('Pins Class','hotspot'); ?></label>
							<input type="text" name="pointdata[pins_class][]" value="<?php echo esc_attr($pins_class); ?>" placeholder="Ex: class_1 class_2 class_3"/>
                        </div>
					</div>
					<p>
						<input type="hidden" name="pointdata[top][]" min="0" max="100" step="any" value="<?php echo esc_attr($pointTop); ?>" />
					</p>
					<p>
						<input type="hidden" name="pointdata[left][]" min="0" max="100" step="any" value="<?php echo esc_attr($pointLeft); ?>" />
					</p>
		  		</div>
			  	<div class="xolo_popup_footer">
					<button type="button" class="button button-danger button-large button_delete"><?php _e('Delete','hotspot')?></button>
					<button type="button" class="button button-primary button-large" data-popup-close="info_draggable<?php echo esc_attr($countPoint); ?>"><?php _e('Done','hotspot')?></button>
			  	</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->		
	<?php		
	return ob_get_clean();
}

function xolo_hotspot_get_default_pins($datapin = array()){
	if(!is_array($datapin)) $datapin = array();
	$datapin 							= wp_parse_args($datapin,unserialize(XOLO_HOTSPOT_DEFAULT_PINS));
	$countPoint 						= $datapin['countPoint'];	
	$spotType 							= $datapin['spotType'];
	$txtPin 							= $datapin['txtPoint'];
	$imgPin 							= $datapin['imgPoint'];
	$topPin 							= $datapin['top'];
	$leftPin 							= $datapin['left'];
	$pins_txt_custom					= $datapin['pins_txt_custom'];
	$pins_image_custom 					= $datapin['pins_image_custom'];
	if($pins_image_custom) $imgPin 		= $pins_image_custom;
	if($pins_txt_custom) $txtPin 		= $pins_txt_custom;
	ob_start();
	?>
	
	<div id="draggable<?php echo esc_attr($countPoint); ?>" data-points="<?php echo esc_attr($countPoint); ?>" class="drag_wrap" <?php if($topPin && $leftPin):?> style="top:<?php echo esc_attr($topPin); ?>%; left:<?php echo esc_attr($leftPin); ?>%;"<?php endif;?>>
		<div class="point_style">
			<div class="point_wrap">
				<a href="#" class="pins_click_to_edit" data-popup-open="info_draggable<?php echo esc_attr($countPoint); ?>" data-target="#info_draggable<?php echo esc_attr($countPoint); ?>">
				<?php if($spotType == 'textLabel') { ?>			
					<p class="setIcon"><?php echo esc_html($txtPin) ?></p>
				<?php } else { ?>
					<img class="setIcon" src="<?php echo esc_url($imgPin) ?>">
				<?php } ?>
				</a>
			</div>
		</div>
	</div>	

	<?php
	return ob_get_clean();
}
//Clone Point
add_action( 'wp_ajax_xolo_hotspot_clone_point', 'xolo_hotspot_clone_point_function' );
function xolo_hotspot_clone_point_function() {
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "maps_points_save_meta_box_data")) {
    	exit();
   	}   
	if(!is_user_logged_in()){
		wp_send_json_error();
	}
	
	$countPoint = intval($_POST['countpoint']);
	$txtPin		= sanitize_text_field(isset($_POST['txt_pins'])?$_POST['txt_pins']:'');
	$imgPin		= esc_url(isset($_POST['img_pins'])?$_POST['img_pins']:'');
	$countPoint = (isset($countPoint) && !empty($countPoint)) ? $countPoint : mt_rand();
	$datapin = array(
		'countPoint'	=>	$countPoint,
		'txtPoint'		=>	$txtPin,
		'imgPoint'		=>	$imgPin
	);

	$data_input = array(
		'countPoint'	=>	$countPoint,
	);
	wp_send_json_success(array(
		'point_pins'	=>	xolo_hotspot_get_default_pins($datapin),
		'point_data'	=>	xolo_hotspot_get_default_input_point($data_input)
	));
	die();
}

function xolo_hotspot_convert_array_data($inputArray = array()){
	$aOutput =  array();		
	$firstKey = null;
	foreach ($inputArray as $key => $value){
		$firstKey = $key;
		break;
	}
	$nCountKey = count($inputArray[$firstKey]);
	for ($i =0; $i<$nCountKey;$i++){
		$element = array();
		foreach ($inputArray as $key => $value){
			$element[$key] = wp_kses_post($value[$i]);
		}
		array_push($aOutput,$element);
	}
	return $aOutput;
}



/**
 * Main Hotspot plugin class/file.
 *
 * @package HOTSPOT
 */


if ( ! class_exists( 'Xolo_Hotspot_Recommended' ) ) {

	class Xolo_Hotspot_Recommended {

		/**
	 * The instance *Singleton* of this class
	 *
	 * @var object
	 */
	private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
	/**
	 * Class construct function, to initiate the plugin.
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	public function __construct() {
		// Actions.
		$this->includes();
		add_action( 'admin_notices', array( $this, 'add_notice' ), 1 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'wp_ajax_xolo-hotspots-activate-theme', array( $this, 'activate_theme' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	
	/**
		 * Activate theme
		 *
		 * @since 1.0
		 * @return void
		 */
		function activate_theme() {

			switch_theme( 'xolo' );

			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( 'Theme Successfully Activated', 'xolo-hotspots' ),
				)
			);
		}


	/**
	 * Enqueue admin scripts (JS and CSS)
	 *
	 * @param string $hook holds info on which admin page you are currently loading.
	 */
	public function admin_enqueue_scripts( $hook ) {
		// Enqueue the scripts only on the plugin page.
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );	
			
			wp_enqueue_script( 'xolo-hotspots-install-theme', XOLO_HOTSPOT_DIR_URL . 'assets/admin/js/install-theme.js', array( 'jquery', 'updates' ), XOLO_HOTSPOT_VER, true );
			
			$data = apply_filters(
				'xolo_hotspots_install_theme_localize_vars',
				array(
					'installed'  => __( 'Installed! Activating..', 'xolo-hotspots' ),
					'activating' => __( 'Activating..', 'xolo-hotspots' ),
					'activated'  => __( 'Activated! Reloading..', 'xolo-hotspots' ),
					'installing' => __( 'Installing..', 'xolo-hotspots' ),
					'ajaxurl'    => esc_url( admin_url( 'admin-ajax.php' ) ),
				)
			);
			wp_localize_script( 'xolo-hotspots-install-theme', 'XoloHotspotInstallThemeVars', $data );
	}

	
	/**
		 * Add Admin Notice.
		 */
		function add_notice() {

			$theme_status = 'xolo-hotspots-theme-' . $this->get_theme_status();
			Xolo_Hotspot_Notices::add_notice(
				array(
					'id'               => 'xolo-theme-activation-xl',
					'type'             => 'error',
					'show_if'          => ( ! defined( 'XOLO_THEME_VERSION' ) ) ? true : false,
					/* translators: 1: theme.php file*/
					'message'          => sprintf( __( '<b>Xolo</b> Theme needs to be active for you to use currently installed "%1$s" plugin. <a href="#" class="%3$s xl-btn-active" data-theme-slug="xolo">Install & Activate Now</a>', 'xolo-hotspots' ), __( 'Hotspot', 'xolo-hotspots' ), esc_url( admin_url( 'themes.php?theme=xolo' ) ), $theme_status ),
					'dismissible'      => true,
					'dismissible-time' => WEEK_IN_SECONDS,
				)
			);

		}
		
		
		/**
		 * Get theme install, active or inactive status.
		 *
		 * @since 1.0
		 *
		 * @return string Theme status
		 */
		function get_theme_status() {

			$theme = wp_get_theme();

			// Theme installed and activate.
			if ( 'Xolo' == $theme->name || 'Xolo' == $theme->parent_theme ) {
				return 'installed-and-active';
			}

			// Theme installed but not activate.
			foreach ( (array) wp_get_themes() as $theme_dir => $theme ) {
				if ( 'Xolo' == $theme->name || 'Xolo' == $theme->parent_theme ) {
					return 'installed-but-inactive';
				}
			}

			return 'not-installed';
		}
		
		/**
		 * Admin Notices
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function admin_notices() {

			if ( ! defined( 'XOLO_THEME_SETTINGS' ) ) {
				return;
			}

			add_action( 'plugin_action_links_' . XOLO_HOTSPOT_BASE, array( $this, 'action_links' ) );
		}

	/**
	 * Load all the required files in the importer.
	 *
	 * @since  1.0.0
	 */
	 function includes() {
		require_once 'admin/inc/xolo-hotspot-notices.php';
	}
	

	}
}// End if().

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Xolo_Hotspot_Recommended::get_instance();
