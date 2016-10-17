<?php
/**
 * ----------------------------------------------------------------------
 * WordPress Rankie plugin integration
 * http://codecanyon.net/item/rankie-wordpress-rank-tracker-plugin/7605032
 */

if(is_plugin_active('wp-rankie/wp-ranker.php')){

	// Activate Rankie License
	add_action('admin_init', 'lbmn_unlock_rankie');
	function lbmn_unlock_rankie() {
		$licenseactive=get_option('wp_rankie_license_active','');

		if(trim($licenseactive) == ''){
			//activate the plugin
			update_option('wp_rankie_license_active', 'active');
			update_option('wp_rankie_license_active_date', time('now'));
		}
	}

}