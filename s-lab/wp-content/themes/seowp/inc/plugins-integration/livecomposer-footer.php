<?php
/**
 * LiveComposer-based footers ( WP Admin > Appearance > Footers )
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * We use the special content type lbmn_footer to make possible for
 * end user to create complex unlimited footer designs. This file
 * register lbmn_footer content type and extend Live Composer
 * to render custom generated CSS for each page were footer displayed.
 *
 *	 – Register special lbmn_footer content type
 *  – Extend LiveComposer Footer CSS render
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


	/**
	* ----------------------------------------------------------------------
	* Special content type lbmn_footer for extended footer designs
	* User can create footer the same way he create pages using Live Composer
	* In the back-end footer is just a custom content type
	* ( WP Admin > Appearance > Footers )
	*/

	// Register Custom Post Type
	add_action( 'init', 'lbmn_footer_cpt', 0 );
	function lbmn_footer_cpt() {

		$labels = array(
			'name'                => _x( 'Theme Footers', 'Post Type General Name', 'text_domain' ),
			'singular_name'       => _x( 'Theme Footer', 'Post Type Singular Name', 'text_domain' ),
			// 'menu_name'           => __( 'Post Type', 'text_domain' ),
			// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
			'all_items'           => __( 'All Footers', 'text_domain' ),
			'view_item'           => __( 'View Footer', 'text_domain' ),
			'add_new_item'        => __( 'Add New Footer', 'text_domain' ),
			'add_new'             => __( 'Add Footer', 'text_domain' ),
			'edit_item'           => __( 'Edit Footer', 'text_domain' ),
			'update_item'         => __( 'Update Footer', 'text_domain' ),
			// 'search_items'        => __( 'Search Item', 'text_domain' ),
			'not_found'           => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
		);
		$args = array(
			'label'               => __( 'lbmn_footer', 'text_domain' ),
			// 'description'         => __( 'Post Type Description', 'text_domain' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom-fields', ),
			// 'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'menu_icon'           => '',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'lbmn_footer', $args );

	}

	// Create custom Apperance > Footers menu item
	add_action('admin_menu', 'lbmn_footer_add_appearance_menu');
	function lbmn_footer_add_appearance_menu(){
		  add_theme_page( 'Theme Footers', 'Footers', 'edit_theme_options', 'edit.php?post_type=lbmn_footer', '');
	}


	/**
	 * ----------------------------------------------------------------------
	 * Footer LiveComposer CSS render
	 *
	 * By default LiveComposer put CSS in the header only for current page
	 * and ignores footer leaving it unstyled.
	 *
	 * The code below is slightly edited copy of dslc_custom_css() function
	 * that can be found in:
	 * /plugins/ds-lice-composer/includes/display-functions.php
	 */

	add_action( 'wp_footer', 'lbmn_footer_custom_css' );
	function lbmn_footer_custom_css() {

		global $dslc_active;
		global $dslc_css_style;
		global $content_width;
		global $dslc_googlefonts_array;
		global $dslc_post_types;

		$composer_code = '';
		$template_code = '';

		$lc_width = dslc_get_option( 'lc_max_width', 'dslc_plugin_options' );

		if ( empty( $lc_width ) ) {
			$lc_width = $content_width . 'px';
		} else {

			if ( strpos( $lc_width, 'px' ) === false && strpos( $lc_width, '%' ) === false )
				$lc_width = $lc_width . 'px';

		}

		echo "\n<!-- Custom Footer Styles -->\n";
		echo '<style type="text/css">';

			// Get composer code
			$post_id = get_the_ID();
			$footer_post_id = lbmn_get_footerid_by_pageid($post_id);
			$composer_code .= get_post_meta( $footer_post_id, 'dslc_code', true );

			// If no home page set and blog posts listed on the front
			// output LiveComposer CSS for front page too
			if ( is_front_page() && get_option( 'page_on_front', 0 ) == 0 ) {
				$template_post_id = get_theme_mod( 'lbmn_systempage_frontpage_posts', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_FRONTPAGE_POSTS_DEFAULT, 'lbmn_archive' ) );
				$composer_code .= get_post_meta( $template_post_id, 'dslc_code', true );
			}

			// If composer not used on this page stop execution
			if ( $composer_code ) {

				// Replace shortcode names
				$composer_code = str_replace( 'dslc_modules_section', 'dslc_modules_section_gen_css', $composer_code );
				$composer_code = str_replace( 'dslc_modules_area', 'dslc_modules_area_gen_css', $composer_code );
				$composer_code = str_replace( '[dslc_module]', '[dslc_module_gen_css]', $composer_code );
				$composer_code = str_replace( '[/dslc_module]', '[/dslc_module_gen_css]', $composer_code );

				// Do CSS shortcode
				do_shortcode( $composer_code );

				// Google Fonts Import

				$googlefonts_output = '';
				foreach ( $dslc_googlefonts_array as $googlefont) {
					$googlefont = str_replace( ' ', '+', $googlefont );
					if ( $googlefont != '' ) {
						$googlefonts_output .= '@import url("//fonts.googleapis.com/css?family=' . $googlefont . ':100,200,300,400,500,600,700,800,900&subset=latin,latin-ext"); ';
					}
				}
				echo $googlefonts_output;

			}

			// Wrapper width
			echo '.dslc-modules-section-wrapper, .dslca-add-modules-section { width : ' . $lc_width . '; } ';

			// Echo CSS style
			if ( ! $dslc_active && $composer_code )
				// $dslc_css_style = str_replace('#dslc-content', '#site-footer', $dslc_css_style);
				echo $dslc_css_style;

		echo '</style>';
	}

} // if ( defined( 'DS_LIVE_COMPOSER_URL' ) )