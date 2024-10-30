<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function xolo_hotspot_upgrade_meta_box() {
	//post type
	$screens = array( 'points_pin' );

	foreach ( $screens as $screen ) {
		add_meta_box(
			'xolo-hotspot-upgrade-shortcode',
			__( 'Upgrade to Pro :)', 'hotspot' ),
			'xolo_hotspot_upgrade_shortcode_callback',
			$screen,
			'side',
			'low'
		);
	}
}
add_action( 'add_meta_boxes', 'xolo_hotspot_upgrade_meta_box' );
function xolo_hotspot_upgrade_shortcode_callback(){
	ob_start();
	?>
	<a href="https://wphotspot.com" target="_blank">
		<img src="<?php echo XOLO_HOTSPOT_DIR_URL . 'assets/admin/images/upgrade-to-pro.jpg'?>" alt="Hotspot"/>
	</a>
	<?php
	echo ob_get_clean();
}