<?php
/**
 * Search enabler for the Live Composer powered pages
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Problem: by default Live Composer save all the content
 * in the 'dslc_code' custom field. The data is serialized and compressed.
 *
 * Solution: on each post update 'save_post' extract content parts
 * of LC modules and save them as a plain text in 'dslc_search_content'
 * meta box. Then modify WP search so it goes through this custom field too
 * each time it search through the database.
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if Live Composer is active
if ( defined( 'DS_LIVE_COMPOSER_URL' ) ) {

	// On each post update extract LC modules content ans save it as plain text
	// into 'dslc_search_content' custom field
	add_action( 'save_post', 'lbmn_save_lc_content_as_meta' );
	function lbmn_save_lc_content_as_meta( $post_id ) {

		$dslc_code_content = get_post_meta( $post_id, 'dslc_code', true );
		if ( $dslc_code_content ) {
			$raw_lc_code = get_post_meta( $post_id, 'dslc_code', true );
			$lc_code_decoded = array();
			$lc_code_serialized_parts = array();

			preg_match_all(
				"/\[dslc_module\]([A-Za-z0-9+\/\=]*)?\[\/dslc_module\]+/",
				$raw_lc_code,
				$lc_code_serialized_parts, PREG_SET_ORDER);

			foreach ($lc_code_serialized_parts as $module_code_serialized) {
				$module_code_serialized = $module_code_serialized[0];
				$module_code_serialized = str_replace('[dslc_module]', '', $module_code_serialized);
				$module_code_serialized = str_replace('[/dslc_module]', '', $module_code_serialized);
				$decoded_temp = maybe_unserialize( base64_decode($module_code_serialized) );

				if ( isset($decoded_temp['content']) ) {
					$lc_code_decoded[] = $decoded_temp['content'];
				}
			}

			if ( !empty($lc_code_decoded) ) {
				$lc_code_decoded = implode(" ", $lc_code_decoded);
				update_post_meta( $post_id, 'dslc_search_content', $lc_code_decoded );
			}
		}
	}


	// Extend standard WP search to go through 'dslc_search_content'
	// custom fields each time it looking for a search term
	// http://wordpress.stackexchange.com/questions/5546/how-to-correctly-call-custom-field-dates-into-a-posts-where-filter-using-sql-sta
	add_filter('posts_where', 'lbmn_lc_search_where');
	function lbmn_lc_search_where($where) {
		if ( ! is_admin() && is_search() && ! is_archive() ) {
			global $wpdb;
			$term = get_query_var('s');

			$where .= " OR ( ";
				$where .= " ($wpdb->postmeta.meta_key = 'dslc_search_content')";
				$where .= " AND ($wpdb->postmeta.meta_value  LIKE '%{$term}%')";
				$where .= " AND ($wpdb->posts.post_status = 'publish')";
				$where .= " AND ($wpdb->posts.post_type IN ('page', 'dslc_projects', 'dslc_galleries', 'dslc_staff', 'dslc_downloads', 'dslc_testimonials', 'dslc_partners') )";
			$where .= " ) ";
		}
		return $where;
	}

	// This function needed to make the query to work in the function above
	add_filter( 'posts_join' , 'lbmn_lc_posts_join');
	function lbmn_lc_posts_join($join){
		if ( ! is_admin() && is_search() && ! is_archive() ) {
			global $wpdb;
			$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
		}
		return $join;
	}



	/**
	 * ----------------------------------------------------------------------
	 * Remove duplicate posts from search results
	 * http://codex.wordpress.org/Class_Reference/WP_Query#Filters
	 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
	 */

	function lbmn_search_distinct() {
		return "DISTINCT";
	}
	add_filter('posts_distinct', 'lbmn_search_distinct');


	/**
	 * ----------------------------------------------------------------------
	 * Add support to WordPress SEO plugin.
	 * https://yoast.com/wordpress-seo-content-analysis/
	 *
	 * Now WordPress SEO plugin analyses 'dslc_search_content' custom field,
	 * the same way as standard editor content.
	 *
	 * $post_content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );
	 */

	// apply_filters( 'wpseo_pre_analysis_post_content', $field['value'] );

	// add_filter( 'wpseo_pre_analysis_post_content', 'lbmn_make_yoasts_love_livecomposer', 10, 2 );
	add_filter( 'wpseo_pre_analysis_post_content', 'lbmn_make_yoasts_love_livecomposer');

	function lbmn_make_yoasts_love_livecomposer( $content )
	{
		global $post;
		$cf_with_content = get_post_meta( $post->ID, 'dslc_search_content', true );
	   return $content . ' ' . $cf_with_content;
	   remove_filter('wpseo_pre_analysis_post_content', 'add_custom_to_yoast'); // don't let WP execute this twice
	}

} // if ( defined( 'DS_LIVE_COMPOSER_URL' ) )