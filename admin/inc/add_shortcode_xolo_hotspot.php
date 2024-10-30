<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );?>
<?php
function xolo_hotspot_shortcode_function($atts){
	
	$atts = shortcode_atts( array(
		'id' => '',
	), $atts, 'xolo_hotspot' );
	
	$idPost =  intval($atts['id']);
	
	if(get_post_status($idPost) != "publish") return;
	
	$data_post = get_post_meta($idPost, 'hotspot_content', true);

	if(!$data_post){
		$data_post = maybe_unserialize(get_post_field('post_content', $idPost));
	}
		
	$banner_images 				= 	(isset($data_post['banner_images']))?$data_post['banner_images']:'';
	$data_points 				= 	(isset($data_post['data_points']))?$data_post['data_points']:'';
	$spot_type 					= 	(isset($data_post['spot_type']))?$data_post['spot_type']:'';
	$pins_txt_lbl 				= 	(isset($data_post['pins_txt_lbl']))?$data_post['pins_txt_lbl']:'';
	$pins_txt_lbl_hover			= 	(isset($data_post['pins_txt_lbl_hover']))?$data_post['pins_txt_lbl_hover']:'';
	$pins_image 				=   (isset($data_post['pins_image']))?$data_post['pins_image']:'';
	$pins_image_hover 			= 	(isset($data_post['pins_image_hover']))?$data_post['pins_image_hover']:'';
	$pins_more_option 			= 	wp_parse_args($data_post['pins_more_option'],array(
		'position'				=>	'top_left',
		'custom_top'			=>	 0,
		'custom_left'			=>	 0,
		'custom_hover_top'		=>	 0,
		'custom_hover_left'		=>	 0,
		'custom_pin_size'  		=>   25,
		'custom_color'			=>   'inherit',
		'pins_animation'		=>	 'none'
	));
	ob_start();
	$css_output = '#hotspot_panel_'.$idPost.' .point_style {
			top:-'.$pins_more_option['custom_top'].'px;
			left:-'.$pins_more_option['custom_left'].'px;
		}
		#hotspot_panel_'.$idPost.' .drag_wrap .pins_item,
		#hotspot_panel_'.$idPost.' .drag_wrap .pins_item_hover {
			font-size: '.intval($pins_more_option['custom_pin_size']).'px;
			color: '.esc_attr($pins_more_option['custom_color']).';
		}
		#hotspot_panel_'.$idPost.' .drag_wrap img.pins_item,
		#hotspot_panel_'.$idPost.' .drag_wrap img.pins_item_hover {
			width: '.intval($pins_more_option['custom_pin_size']).'px;
		}
		#powerTip {
			color: #333333;
		    background-color: #ffffff;
		    border-color: #ffffff;
		    border-radius: 3px;
		}';
    wp_register_style('xolo_output', false);
    wp_enqueue_style('xolo_output');
    if (!empty( $css_output ) ) {
        wp_add_inline_style( 'xolo_output', $css_output );
    }
	//do_action('add_dynamic_styles_output');
	if($banner_images):
	?>
	<div class="xolo_spot_wrapper">
		<div class="xolo_spot_inside">
			<div class="xolo_spot_drag" id="hotspot_panel_<?php echo $idPost;?>">
				<div class="images_wrap">
					<img src="<?php echo esc_url($banner_images); ?>">
				</div>
				<?php if(is_array($data_points)):?>
				<?php $stt = 1;foreach ($data_points as $point):
				$spot_type 					= 	(isset($data_post['spot_type']))?$data_post['spot_type']:'';
				$pins_txt_lbl 				= 	(isset($data_post['pins_txt_lbl']))?$data_post['pins_txt_lbl']:'';
				$pins_txt_lbl_hover			= 	(isset($data_post['pins_txt_lbl_hover']))?$data_post['pins_txt_lbl_hover']:'';
				$pins_image 				= 	(isset($data_post['pins_image']))?$data_post['pins_image']:'';
				$pins_image_hover 			= 	(isset($data_post['pins_image_hover']))?$data_post['pins_image_hover']:'';

				$linkpins 					= 	isset($point['linkpins'])?esc_url($point['linkpins']):'';	 
				$link_target 				= 	isset($point['link_target'])?esc_attr($point['link_target']): '_self';

				$pins_txt_custom 			= 	isset($point['pins_txt_custom'])?esc_html($point['pins_txt_custom']):'';
				$pins_txt_hover_custom		= 	isset($point['pins_txt_hover_custom'])?esc_html($point['pins_txt_hover_custom']):'';

				$pins_image_custom 			= 	isset($point['pins_image_custom'])?esc_url($point['pins_image_custom']):'';
				$pins_image_hover_custom 	= 	isset($point['pins_image_hover_custom'])?esc_url($point['pins_image_hover_custom']):'';

				$placement 					= 	(isset($point['placement']) && $point['placement'] != '')?esc_attr($point['placement']):'n';
				$pins_id 					= 	(isset($point['pins_id']) && $point['pins_id'] != '')?esc_attr($point['pins_id']):'';
				$pins_class 				= 	(isset($point['pins_class']) && $point['pins_class'] != '')?esc_attr($point['pins_class']):'';

				if($pins_txt_custom) $pins_txt_lbl 					= $pins_txt_custom;
				if($pins_txt_hover_custom) $pins_txt_lbl_hover 		= $pins_txt_hover_custom;
				if($pins_image_custom) $pins_image 					= $pins_image_custom;
				if($pins_image_hover_custom) $pins_image_hover 		= $pins_image_hover_custom;
				
				$noTooltip = false;
				ob_start();?>
				<?php if(isset($point['content'])):?>
					<?php if(!empty($point['content'])):?>
						<div class="box_view_html"><a href="javascript:void(0);" class="close_hp">X</a><div class="hotspot_tooltip_content"><?php echo apply_filters('the_content', $point['content']);?></div></div>
					<?php else :
					$noTooltip = true;
					endif;?>
				<?php endif;?>
				<?php
				$view_html = ob_get_clean();
				?>

				<div class="drag_wrap tips <?php echo ($pins_class)?$pins_class:''?>" style="top:<?php echo esc_attr($point['top']); ?>%;left:<?php echo esc_attr($point['left']); ?>%;" <?php echo ($pins_id)?'id="'.$pins_id.'"':''?>>
				 	<div class="point_style <?php echo ( $pins_txt_lbl_hover || $pins_image_hover)?'is-hover':''?> hotspot_tooltop_html" data-placement="<?php echo esc_attr($placement);?>" data-html="<?php echo esc_attr($view_html)?>">
				 		<?php if($linkpins):?>
				 			<a href="<?php echo esc_url($linkpins);?>" title="" <?php echo ($link_target)?'target="'.$link_target.'"':'';?>>
				 		<?php endif;?>
						 	<div class="pins_normal <?php echo ($pins_more_option['pins_animation'] != 'none')?'pins_animation hotspot_'.$pins_more_option['pins_animation'].'':'';?> <?php if(!$noTooltip):?>hotspot_hastooltop<?php endif;?>">

								<?php if($spot_type == 'textLabel'){?>
									<p class="pins_item pins_txt_lbl"><?php echo esc_html($pins_txt_lbl); ?></p>
					 			<?php if($pins_txt_lbl_hover):?><p class="pins_item_hover pins_txt_lbl_hover"><?php echo esc_html($pins_txt_lbl_hover); ?></p><?php endif;?>

								<?php }else{?>
									<img src="<?php echo esc_url($pins_image); ?>" class="pins_item pins_image">
									<?php if($pins_image_hover):?><img src="<?php echo esc_url($pins_image_hover); ?>" class="pins_item_hover pins_image_hover"><?php endif;?>
								<?php } ?>	 		
							</div>
				 		<?php if($linkpins):?></a>
				 		<?php endif;?>
				 	</div>
				</div>
				<?php $stt++;endforeach;?>
				<?php endif;?>
			</div>
		</div>
	</div>

	<?php	
	endif;	
	return ob_get_clean();
}
add_shortcode('xolo_hotspot','xolo_hotspot_shortcode_function');


