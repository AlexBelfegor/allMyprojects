<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Eventually, some of the functionality here could be replaced
 * by core features.
 *
 * – Return menu id by it's title
 * – Get the custom footer post id for the current page
 * – Custom body classes
 * – Extend wp_title to print a better composed <title> tag
 * – Get image/attachment ID by URL
 * – Turn page comments off by default
 * – Get page ID by it's title
 * – Get page ID by it's path
 * – Return all footer posts as array
 * – Web-fonts output composer
 * – Web-fonts weights helper
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

/**
* ----------------------------------------------------------------------
* Return menu id by it's title
*/
if ( ! function_exists( 'lbmn_get_menuid_by_menutitle' ) ) {
	function lbmn_get_menuid_by_menutitle ( $menu_name ) {

		$menus   = wp_get_nav_menus(); // get all menus
		$menu_id = 0;

		foreach ( $menus as $menu ) {
			$current_menu_name = wp_html_excerpt( $menu->name, 40, '&hellip;' );

			if ( $menu_name == $current_menu_name ) {
				$menu_id = $menu->term_id;
			}
		}

		return $menu_id;
	}
}

/**
 * ----------------------------------------------------------------------
 * Get the custom footer post (lbmn_footer content type) id for the current page
 */
function lbmn_get_footerid_by_pageid ($current_pageid) {
	if ( is_plugin_active('ds-live-composer/ds-live-composer.php') ) {
		// Check if current page has custom footer ID set
		$footer_post_id = '';

		// Fist, make sure rwmb plugin installed and fucntion is available
		// and if not 404 or no search results page otherwise rwmb_meta throw a notice
		if ( have_posts() && function_exists('rwmb_meta') ) {
			$footer_post_id = rwmb_meta( 'lbmn_custom_footer_id', array(), $current_pageid );
		}

		// If no custom footer set get default footer defined in Theme Customizer
		if ( ! $footer_post_id ) {

			// Get footer page ID defined in Theme Customizer
			$footer_post_id = get_theme_mod( 'lbmn_footer_design');

			// If no footer set in Theme Customizer check what default we have set
			// I don't want to run 'get_page_by_title' on every page load
			if ( !$footer_post_id ) {
				// Get default footer custom post id
				$footer_design_default = get_page_by_title(LBMN_FOOTER_DESIGN_TITLE_DEFAULT, 'ARRAY_A', 'lbmn_footer');
				if ( isset($footer_design_default) ) {
					$footer_design_default = $footer_design_default['ID'];
				} else {
					$footer_design_default = '';
				}

				$footer_post_id = get_theme_mod( 'lbmn_footer_design', $footer_design_default );
			}
		}

		/* WPML translated footers support */
		/* http://wpml.org/documentation/support/creating-multilingual-wordpress-themes/language-dependent-ids/ */
		if ( $footer_post_id  && function_exists('icl_object_id')) {
			$footer_post_id = icl_object_id($footer_post_id,'lbmn_footer',true);
		}

		return $footer_post_id;
	} else {
		return false;
	}
}

/**
* ----------------------------------------------------------------------
* Custom <body> classes
*/
function lbmn_body_classes( $classes ) {
	// Fist, make sure rwmb plugin installed and fucntion is available
	// and if not 404 or no search results page otherwise rwmb_meta throw a notice
	if ( have_posts() && function_exists('rwmb_meta') ) {
		// Get 'Menu cover content' metabox value
		if (rwmb_meta( 'lbmn_page_title_settings') == 'menuoverlay' ) {
			$classes[] = 'header-overlay';
		}
	}

	// boxed layout class
	if ( get_theme_mod( 'lbmn_pagelayoutboxed_switch', 0 ) ) {
		$classes[] = 'boxed-page-layout';
	}

	return $classes;
}
add_filter( 'body_class', 'lbmn_body_classes' );

/**
* ----------------------------------------------------------------------
* Filter in a link to a content ID attribute for the next/previous
* image links on image attachment pages
* TODO: do we use it now?
*/
function lbmn_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'lbmn_enhanced_image_navigation', 10, 2 );

/**
* ----------------------------------------------------------------------
* Filters wp_title to print a neat <title> tag based on
* what is being viewed.
*/
function lbmn_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'lbmn' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'lbmn_wp_title', 10, 2 );


/**
* ----------------------------------------------------------------------
* Get image/attachment ID by URL
* @link http://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
*/
function lbmn_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;
	$attachment_id = false;

	// If there is no url, return.
	if ( '' == $attachment_url )
		return;

	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

	}

	return $attachment_id;
}

/**
 * ----------------------------------------------------------------------
 * Get page ID by it's title
 */
function lbmn_get_page_by_title ($title='', $content_type='page') {
	$page_id = get_page_by_title($title, 'ARRAY_A', $content_type);
	return $page_id['ID'];
}

/**
 * ----------------------------------------------------------------------
 * Get page ID by it's path
 */
function lbmn_get_page_by_slug ($slug='', $content_type='page') {
	return get_page_by_path($slug, OBJECT, $content_type)->ID;
}

/**
 * ----------------------------------------------------------------------
 * Turn page comments off by default
 */
function lbmn_default_comments_off( $data ) {
    if( $data['post_type'] == 'page' && $data['post_status'] == 'auto-draft' ) {
        $data['comment_status'] = 0;
        $data['ping_status'] = 0;
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'lbmn_default_comments_off' );

/**
 * ----------------------------------------------------------------------
 * Return all footer posts as array (used in Theme Customizer)
 */
function lbmn_return_all_footers () {
	$type = 'lbmn_footer';
	$footer_args = array(
		'posts_per_page'   => -1,
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => $type,
		'post_status'      => 'publish',
		'suppress_filters' => true );

	$footer_posts_array = get_posts( $footer_args );
	$footer_posts = array ();

	foreach ($footer_posts_array as $footer_post) {
		$footer_posts[$footer_post->ID] = $footer_post->post_title;
	}

	return $footer_posts;
}

/**
* ----------------------------------------------------------------------
* Web-fonts output composer
*/
function lbmn_output_css_webfont( $font_preset_no='' ) {
	$css_font  = array( );
	$standard_font_name = '';
	$custom_font_name = '';
	$custom_font_weight = '';
	$custom_font_style = '';

	// Output Google or custom webfont name
	if ( get_theme_mod( 'lbmn_font_preset_usegooglefont_' . $font_preset_no, 1 ) ) {
		$custom_font_name = get_theme_mod( 'lbmn_font_preset_googlefont_' . $font_preset_no, constant('LBMN_FONT_PRESET_GOOGLEFONT_' . $font_preset_no . '_DEFAULT') );
	} elseif ( get_theme_mod( 'lbmn_font_preset_webfont_' . $font_preset_no ) ) {
		$custom_font_name = get_theme_mod( 'lbmn_font_preset_webfont_' . $font_preset_no );
	}


	// Output standard font-family
	/* http://kv5r.com/articles/dev/font-family.asp */

	$standard_font_name = get_theme_mod( 'lbmn_font_preset_standard_' . $font_preset_no, constant('LBMN_FONT_PRESET_STANDARD_' . $font_preset_no . '_DEFAULT') );

	switch ($standard_font_name) {
		case 'arial':
			$standard_font_name = "Arial,'DejaVu Sans','Liberation Sans',Freesans,sans-serif";
			break;
		case 'helvetica':
			$standard_font_name = "Helvetica,Arial,'DejaVu Sans','Liberation Sans',Freesans,sans-serif";
			break;
		case 'lucida-sans-unicode':
			$standard_font_name = "'Lucida Sans Unicode','Lucida Grande','Lucida Sans','DejaVu Sans Condensed',sans-serif";
			break;
		case 'century-gothic':
			$standard_font_name = "'Century Gothic',futura,'URW Gothic L',Verdana,sans-serif";
			break;
		case 'arial-narrow':
			$standard_font_name = "'Arial Narrow','Nimbus Sans L',sans-serif";
			break;
		case 'impact':
			$standard_font_name = "Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif";
			break;
		case 'arial-black':
			$standard_font_name = "'Arial Black',Gadget,sans-serif";
			break;
		case 'cambria':
			$standard_font_name = "Cambria,'Palatino Linotype','Book Antiqua','URW Palladio L',serif";
			break;
		case 'verdana':
			$standard_font_name = "Verdana,Geneva,'DejaVu Sans',sans-serif";
			break;
		case 'constantia':
			$standard_font_name = "Constantia,Georgia,'Nimbus Roman No9 L',serif";
			break;
		case 'bookman-old-style':
			$standard_font_name = "'Bookman Old Style',Bookman,'URW Bookman L','Palatino Linotype',serif";
			break;
		default:
			$standard_font_name = "'Helvetica Neue',Helvetica,Arial,'DejaVu Sans','Liberation Sans',Freesans,sans-serif";
			break;
	}

	$css_font['font_family'] = $standard_font_name;


	if ( $custom_font_name ) {
		$string_to_filter = $custom_font_name;
		$custom_font_name = preg_replace("/(.*)\:(\w*)/", "$1", $string_to_filter);
		$custom_font_name = "'" . $custom_font_name . "', ";

		$string_to_filter = preg_replace("/(.*)\:(\w*)/", "$2", $string_to_filter);
		$custom_font_style = preg_replace("/(italic)/", "$1", $string_to_filter);
		if ( $custom_font_style != 'italic' ) {
			$custom_font_style = '';
		}

		$custom_font_weight = preg_replace("/.*(\d{3}).*/", "$1", $string_to_filter);
		$custom_font_weight = intval($custom_font_weight);
		if ( $custom_font_weight == 0 ) {
			$custom_font_weight = '400';
		}

		$css_font['font_family'] = $custom_font_name . $css_font['font_family'];
		$css_font['font_weight'] = $custom_font_weight;
		$css_font['font_style'] = $custom_font_style;
	}

	return $css_font;
}


/**
* ----------------------------------------------------------------------
* Web fonts and weights helper
*/

// remove font-style like italic;
// remove font-weight mixed with font-style like 400italic
function lbmn_leave_only_fontweights ($font_ext) {
	if ( stristr( strtolower($font_ext), 'italic') ) {
		$font_weight = '';
	} elseif ( stristr( strtolower($font_ext), 'regular') ) {
		$font_weight = '400';
	} else {
		$font_weight = $font_ext;
	}
	return $font_weight;
}

function lbmn_return_font_presets_names() {
	$googlefonts_toload = array();

	for ($i=1; $i < 5 ; $i++) {
		if ( get_theme_mod( 'lbmn_font_preset_usegooglefont_' . $i ) ) {
			$googlefonts_toload[$i] = get_theme_mod( 'lbmn_font_preset_googlefont_' . $i );
		}
	}

	return $googlefonts_toload;
}

function lbmn_return_font_presets_weights($remove_font_styles = true) {
	// Send array of Font Presets to the java script
	$fontPresents = array();
	$actve_font_weights = array();
	$actve_font_family = '';
	$google_fonts = lbmn_get_goolefonts();


	// Go through all Google font presents in Theme Customizer
	for ($i=1; $i < 5 ; $i++) {
		$preset_no = $i;

		// Check if Google font is set
		if ( get_theme_mod( 'lbmn_font_preset_usegooglefont_' . $preset_no ) && get_theme_mod( 'lbmn_font_preset_googlefont_' . $preset_no, '' ) != '' ) {
			// get the font name for the current preset
			$actve_font_family = get_theme_mod( 'lbmn_font_preset_googlefont_' . $preset_no, '' );

			//check if the active font has any width variations
			if ( isset($google_fonts[$actve_font_family]) && count($google_fonts[$actve_font_family]) > 0 ) {
				$actve_font_weights = $google_fonts[$actve_font_family];

				if ($remove_font_styles) {
					$actve_font_weights = array_filter($actve_font_weights, "lbmn_leave_only_fontweights" );
				}
			} else {
				// active Google Font has no width variations
				$actve_font_weights = array(
					'0' => '400',
				);
			}
		} else {
			// if not a google web font used return the full list of font weights
			$actve_font_weights = array(
				'400' => '400 Regular',
				'100' => '100 Thin',
				'200' => '200 Light',
				'300' => '300 Book',
				'500' => '500 Medium',
				'600' => '600 DemiBold',
				'700' => '700 Bold',
				'800' => '800 ExtraBold',
				'900' => '900 Heavy',
			);
		}

		$fontPresetsWeights[$preset_no] = $actve_font_weights;
	}

	return $fontPresetsWeights;
}
