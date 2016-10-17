<?php
/**
 * ----------------------------------------------------------------------
 * NEX-Forms plugin integration
 * http://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891
 */

if(is_plugin_active('Nex-Forms/main.php') || is_plugin_active('nex-forms/main.php')){
	// Remove annoying NEX-forms ads from the WP dashboard
	add_action( 'admin_menu' , 'lbmn_remove_nexform_adbox' );
	function lbmn_remove_nexform_adbox() {
		remove_meta_box( 'basix_dashboard_widget' , 'dashboard' , 'normal' );
	}
}