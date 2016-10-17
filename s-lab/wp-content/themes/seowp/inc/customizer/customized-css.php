<?php
/**
 * Customized CSS code generator
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This file defines the functions responsible for <style> injection
 * with custom colors/fonts settings in the header
 *
 * To optimize WP speed we use Transients API caching provided by WP.
 * Idea and basic implementation by @link https://github.com/aristath used in his
 * open-source theme @link https://github.com/aristath/lbmn
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
 * Generate customized CSS output
 */

function lbmn_customized_css() {
	$styles = '';
	$lbmn_themeoptions = get_option( 'lbmn_theme_options'); // get array of our theme options

	/**
	* ----------------------------------------------------------------------
	* Background
	*/

	$page_background = get_theme_mod( 'lbmn_page_background_color', LBMN_PAGEBACKGROUNDCOLOR_DEFAULT );
	$content_background = get_theme_mod( 'lbmn_content_background_color',LBMN_CONTENT_BACKGROUND_COLOR_DEFAULT );

	$styles .= "body, .global-wrapper {background-color:".$content_background.";}";
	$styles .= "body.boxed-page-layout {background-color:".$page_background.";}";
	// Need !important here for the right Theme Customizer work

	$lbmn_page_background_image = $lbmn_themeoptions['lbmn_page_background_image'];
	$lbmn_page_background_image_opacity = get_theme_mod('lbmn_page_background_image_opacity');
	$lbmn_page_background_image_repeat = get_theme_mod('lbmn_page_background_image_repeat');
	$lbmn_page_background_image_position = get_theme_mod('lbmn_page_background_image_position');
	$lbmn_page_background_image_attachment = get_theme_mod('lbmn_page_background_image_attachment');
	$lbmn_page_background_image_size = get_theme_mod('lbmn_page_background_image_size');
	// $styles .= '/*$lbmn_page_background_image:'.$lbmn_page_background_image.'*/';

	if ( $lbmn_page_background_image ) {
		$styles .= "body.boxed-page-layout:before {background-image:url(".$lbmn_page_background_image.");}";
	}

	if ( isset($lbmn_page_background_image_opacity) ) {
		$styles .= "body.boxed-page-layout:before {opacity:".$lbmn_page_background_image_opacity.";}";
	}

	if ( $lbmn_page_background_image_repeat ) {
		$styles .= "body.boxed-page-layout:before {background-repeat:".$lbmn_page_background_image_repeat.";}";
	}

	if ( $lbmn_page_background_image_position ) {
		$styles .= "body.boxed-page-layout:before {background-position:".$lbmn_page_background_image_position.";}";
	}

	if ( $lbmn_page_background_image_attachment ) {
		$styles .= "body.boxed-page-layout:before {background-attachment:".$lbmn_page_background_image_attachment.";}";
	}

	if ( $lbmn_page_background_image_size ) {
		$styles .= "body.boxed-page-layout:before {background-size:".$lbmn_page_background_image_size.";}";
	}

	$styles .= "";
	$styles .= "";

	/**
	* ----------------------------------------------------------------------
	* Notification panel
	*/
	$notificationpanel_height   = intval(str_replace('px', '', get_theme_mod( 'lbmn_notificationpanel_height', LBMN_NOTIFICATIONPANEL_HEIGHT_DEFAULT ) ));
	$notificationpanel_bgcolor  = get_theme_mod( 'lbmn_notificationpanel_backgroundcolor', LBMN_NOTIFICATIONPANEL_BACKGROUNDCOLOR_DEFAULT );
	$notificationpanel_txtcolor = get_theme_mod( 'lbmn_notificationpanel_textcolor', LBMN_NOTIFICATIONPANEL_TXTCOLOR_DEFAULT );
	$notificationpanel_bgcolor_hover  = get_theme_mod( 'lbmn_notificationpanel_backgroundcolor_hover', LBMN_NOTIFICATIONPANEL_BACKGROUNDCOLOR_HOVER_DEFAULT );
	$notificationpanel_txtcolor_hover = get_theme_mod( 'lbmn_notificationpanel_textcolor_hover', LBMN_NOTIFICATIONPANEL_TXTCOLOR_HOVER_DEFAULT );

	$styles .= ".notification-panel {";
	$styles .= "background-color: $notificationpanel_bgcolor;";
	$styles .= "}";
	$styles .= ".notification-panel, .notification-panel * {color: $notificationpanel_txtcolor; }";

	$styles .= ".notification-panel:before {";
	$styles .= "min-height: ".$notificationpanel_height."px;";
	$styles .= "}";

	$styles .= ".notification-panel:hover {";
	$styles .= "background-color: $notificationpanel_bgcolor_hover;";
	$styles .= "}";
	$styles .= ".notification-panel:hover, .notification-panel:hover * {color: $notificationpanel_txtcolor_hover; }";

	/**
	* ----------------------------------------------------------------------
	* Typography
	*/
	$link_color = get_theme_mod( 'lbmn_typography_link_color', LBMN_TYPOGRAPHY_LINK_COLOR_DEFAULT );
	$link_color_hover = get_theme_mod( 'lbmn_typography_link_hover_color', LBMN_TYPOGRAPHY_LINK_HOVER_COLOR_DEFAULT );

	$styles .= "a {";
	$styles .= "color: ".$link_color.";";
	$styles .= "}";

	$styles .= "a:hover {";
	$styles .= "color: ".$link_color_hover.";";
	$styles .= "}";

	$typography_p_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_p_font', LBMN_TYPOGRAPHY_P_FONT_DEFAULT ) );
	$typography_p_font = str_replace('+', ' ', $typography_p_font['font_family']);
	$typography_p_fontsize = get_theme_mod( 'lbmn_typography_p_fontsize', LBMN_TYPOGRAPHY_P_FONTSIZE_DEFAULT );
	$typography_p_fontweight = get_theme_mod( 'lbmn_typography_p_fontweight', LBMN_TYPOGRAPHY_P_FONTWEIGHT_DEFAULT );
	$typography_p_lineheight = get_theme_mod( 'lbmn_typography_p_lineheight', LBMN_TYPOGRAPHY_P_LINEHEIGHT_DEFAULT );
	$typography_p_marginbottom = get_theme_mod( 'lbmn_typography_p_marginbottom', LBMN_TYPOGRAPHY_P_MARGINBOTTOM_DEFAULT );
	$typography_p_color = get_theme_mod( 'lbmn_typography_p_color', LBMN_TYPOGRAPHY_P_COLOR_DEFAULT );


	$styles .= "body {";
	$styles .= "font-family: ".$typography_p_font.";";
	$styles .= "line-height: ".$typography_p_lineheight."px;";
	$styles .= "font-weight: ".$typography_p_fontweight.";";
	// $styles .= "margin-bottom: ".$typography_p_marginbottom."px;";
	$styles .= "color: ".$typography_p_color.";";
	$styles .= "}";

	$styles .= ".site {";
	$styles .= "font-size: ".$typography_p_fontsize."px;";
	$styles .= "}";

	$styles .= "p {";
	$styles .= "margin-bottom: ".$typography_p_marginbottom."px;";
	$styles .= "}";

	/**
	* ------------------------------
	*/

	$typography_h1_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_h1_font', LBMN_TYPOGRAPHY_H1_FONT_DEFAULT ) );
	$typography_h1_font = str_replace('+', ' ', $typography_h1_font['font_family']);
	$typography_h1_fontsize = get_theme_mod( 'lbmn_typography_h1_fontsize', LBMN_TYPOGRAPHY_H1_FONTSIZE_DEFAULT );
	$typography_h1_fontweight = get_theme_mod( 'lbmn_typography_h1_fontweight', LBMN_TYPOGRAPHY_H1_FONTWEIGHT_DEFAULT );
	$typography_h1_lineheight = get_theme_mod( 'lbmn_typography_h1_lineheight', LBMN_TYPOGRAPHY_H1_LINEHEIGHT_DEFAULT );
	$typography_h1_marginbottom = get_theme_mod( 'lbmn_typography_h1_marginbottom', LBMN_TYPOGRAPHY_H1_MARGINBOTTOM_DEFAULT );
	$typography_h1_color = get_theme_mod( 'lbmn_typography_h1_color', LBMN_TYPOGRAPHY_H1_COLOR_DEFAULT );

	$styles .= "h1 {";
	$styles .= "font-family: ".$typography_h1_font.";";
	$styles .= "font-size: ".$typography_h1_fontsize."px;";
	$styles .= "line-height: ".$typography_h1_lineheight."px;";
	$styles .= "font-weight: ".$typography_h1_fontweight.";";
	$styles .= "margin-bottom: ".$typography_h1_marginbottom."px;";
	$styles .= "color: ".$typography_h1_color.";";
	$styles .= "}";

	/**
	* ------------------------------
	*/

	$typography_h2_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_h2_font', LBMN_TYPOGRAPHY_H2_FONT_DEFAULT ) );
	$typography_h2_font = str_replace('+', ' ', $typography_h2_font['font_family']);
	$typography_h2_fontsize = get_theme_mod( 'lbmn_typography_h2_fontsize', LBMN_TYPOGRAPHY_H2_FONTSIZE_DEFAULT );
	$typography_h2_fontweight = get_theme_mod( 'lbmn_typography_h2_fontweight', LBMN_TYPOGRAPHY_H2_FONTWEIGHT_DEFAULT );
	$typography_h2_lineheight = get_theme_mod( 'lbmn_typography_h2_lineheight', LBMN_TYPOGRAPHY_H2_LINEHEIGHT_DEFAULT );
	$typography_h2_marginbottom = get_theme_mod( 'lbmn_typography_h2_marginbottom', LBMN_TYPOGRAPHY_H2_MARGINBOTTOM_DEFAULT );
	$typography_h2_color = get_theme_mod( 'lbmn_typography_h2_color', LBMN_TYPOGRAPHY_H2_COLOR_DEFAULT );

	$styles .= "h2 {";
	$styles .= "font-family: ".$typography_h2_font.";";
	$styles .= "font-size: ".$typography_h2_fontsize."px;";
	$styles .= "line-height: ".$typography_h2_lineheight."px;";
	$styles .= "font-weight: ".$typography_h2_fontweight.";";
	$styles .= "margin-bottom: ".$typography_h2_marginbottom."px;";
	$styles .= "color: ".$typography_h2_color.";";
	$styles .= "}";

	/**
	* ------------------------------
	*/

	$typography_h3_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_h3_font', LBMN_TYPOGRAPHY_H3_FONT_DEFAULT ) );
	$typography_h3_font = str_replace('+', ' ', $typography_h3_font['font_family']);
	$typography_h3_fontsize = get_theme_mod( 'lbmn_typography_h3_fontsize', LBMN_TYPOGRAPHY_H3_FONTSIZE_DEFAULT );
	$typography_h3_fontweight = get_theme_mod( 'lbmn_typography_h3_fontweight', LBMN_TYPOGRAPHY_H3_FONTWEIGHT_DEFAULT );
	$typography_h3_lineheight = get_theme_mod( 'lbmn_typography_h3_lineheight', LBMN_TYPOGRAPHY_H3_LINEHEIGHT_DEFAULT );
	$typography_h3_marginbottom = get_theme_mod( 'lbmn_typography_h3_marginbottom', LBMN_TYPOGRAPHY_H3_MARGINBOTTOM_DEFAULT );
	$typography_h3_color = get_theme_mod( 'lbmn_typography_h3_color', LBMN_TYPOGRAPHY_H3_COLOR_DEFAULT );

	$styles .= "h3 {";
	$styles .= "font-family: ".$typography_h3_font.";";
	$styles .= "font-size: ".$typography_h3_fontsize."px;";
	$styles .= "line-height: ".$typography_h3_lineheight."px;";
	$styles .= "font-weight: ".$typography_h3_fontweight.";";
	$styles .= "margin-bottom: ".$typography_h3_marginbottom."px;";
	$styles .= "color: ".$typography_h3_color.";";
	$styles .= "}";

	/**
	* ------------------------------
	*/

	$typography_h4_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_h4_font', LBMN_TYPOGRAPHY_H4_FONT_DEFAULT ) );
	$typography_h4_font = str_replace('+', ' ', $typography_h4_font['font_family']);
	$typography_h4_fontsize = get_theme_mod( 'lbmn_typography_h4_fontsize', LBMN_TYPOGRAPHY_H4_FONTSIZE_DEFAULT );
	$typography_h4_fontweight = get_theme_mod( 'lbmn_typography_h4_fontweight', LBMN_TYPOGRAPHY_H4_FONTWEIGHT_DEFAULT );
	$typography_h4_lineheight = get_theme_mod( 'lbmn_typography_h4_lineheight', LBMN_TYPOGRAPHY_H4_LINEHEIGHT_DEFAULT );
	$typography_h4_marginbottom = get_theme_mod( 'lbmn_typography_h4_marginbottom', LBMN_TYPOGRAPHY_H4_MARGINBOTTOM_DEFAULT );
	$typography_h4_color = get_theme_mod( 'lbmn_typography_h4_color', LBMN_TYPOGRAPHY_H4_COLOR_DEFAULT );

	$styles .= "h4 {";
	$styles .= "font-family: ".$typography_h4_font.";";
	$styles .= "font-size: ".$typography_h4_fontsize."px;";
	$styles .= "line-height: ".$typography_h4_lineheight."px;";
	$styles .= "font-weight: ".$typography_h4_fontweight.";";
	$styles .= "margin-bottom: ".$typography_h4_marginbottom."px;";
	$styles .= "color: ".$typography_h4_color.";";
	$styles .= "}";

	/**
	* ------------------------------
	*/

	$typography_h5_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_h5_font', LBMN_TYPOGRAPHY_H5_FONT_DEFAULT ) );
	$typography_h5_font = str_replace('+', ' ', $typography_h5_font['font_family']);
	$typography_h5_fontsize = get_theme_mod( 'lbmn_typography_h5_fontsize', LBMN_TYPOGRAPHY_H5_FONTSIZE_DEFAULT );
	$typography_h5_fontweight = get_theme_mod( 'lbmn_typography_h5_fontweight', LBMN_TYPOGRAPHY_H5_FONTWEIGHT_DEFAULT );
	$typography_h5_lineheight = get_theme_mod( 'lbmn_typography_h5_lineheight', LBMN_TYPOGRAPHY_H5_LINEHEIGHT_DEFAULT );
	$typography_h5_marginbottom = get_theme_mod( 'lbmn_typography_h5_marginbottom', LBMN_TYPOGRAPHY_H5_MARGINBOTTOM_DEFAULT );
	$typography_h5_color = get_theme_mod( 'lbmn_typography_h5_color', LBMN_TYPOGRAPHY_H5_COLOR_DEFAULT );

	$styles .= "h5 {";
	$styles .= "font-family: ".$typography_h5_font.";";
	$styles .= "font-size: ".$typography_h5_fontsize."px;";
	$styles .= "line-height: ".$typography_h5_lineheight."px;";
	$styles .= "font-weight: ".$typography_h5_fontweight.";";
	$styles .= "margin-bottom: ".$typography_h5_marginbottom."px;";
	$styles .= "color: ".$typography_h5_color.";";
	$styles .= "}";

	/**
	* ------------------------------
	*/

	$typography_h6_font = lbmn_output_css_webfont( get_theme_mod( 'lbmn_typography_h6_font', LBMN_TYPOGRAPHY_H6_FONT_DEFAULT ) );
	$typography_h6_font = str_replace('+', ' ', $typography_h6_font['font_family']);
	$typography_h6_fontsize = get_theme_mod( 'lbmn_typography_h6_fontsize', LBMN_TYPOGRAPHY_H6_FONTSIZE_DEFAULT );
	$typography_h6_fontweight = get_theme_mod( 'lbmn_typography_h6_fontweight', LBMN_TYPOGRAPHY_H6_FONTWEIGHT_DEFAULT );
	$typography_h6_lineheight = get_theme_mod( 'lbmn_typography_h6_lineheight', LBMN_TYPOGRAPHY_H6_LINEHEIGHT_DEFAULT );
	$typography_h6_marginbottom = get_theme_mod( 'lbmn_typography_h6_marginbottom', LBMN_TYPOGRAPHY_H6_MARGINBOTTOM_DEFAULT );
	$typography_h6_color = get_theme_mod( 'lbmn_typography_h6_color', LBMN_TYPOGRAPHY_H6_COLOR_DEFAULT );

	$styles .= "h6 {";
	$styles .= "font-family: ".$typography_h6_font.";";
	$styles .= "font-size: ".$typography_h6_fontsize."px;";
	$styles .= "line-height: ".$typography_h6_lineheight."px;";
	$styles .= "font-weight: ".$typography_h6_fontweight.";";
	$styles .= "margin-bottom: ".$typography_h6_marginbottom."px;";
	$styles .= "color: ".$typography_h6_color.";";
	$styles .= "}";

	/**
	* ----------------------------------------------------------------------
	* Call to action panel
	*/
	$calltoaction_height   = intval(str_replace('px', '', get_theme_mod( 'lbmn_calltoaction_height', LBMN_CALLTOACTION_HEIGHT_DEFAULT ) ));
	$calltoaction_bgcolor  = get_theme_mod( 'lbmn_calltoaction_backgroundcolor', LBMN_CALLTOACTION_BACKGROUNDCOLOR_DEFAULT );
	$calltoaction_txtcolor = get_theme_mod( 'lbmn_calltoaction_textcolor', LBMN_CALLTOACTION_TXTCOLOR_DEFAULT );
	$calltoaction_bgcolor_hover  = get_theme_mod( 'lbmn_calltoaction_backgroundcolor_hover', LBMN_CALLTOACTION_BACKGROUNDCOLOR_HOVER_DEFAULT );
	$calltoaction_txtcolor_hover = get_theme_mod( 'lbmn_calltoaction_textcolor_hover', LBMN_CALLTOACTION_TXTCOLOR_HOVER_DEFAULT );

	$calltoaction_fontfamily = lbmn_output_css_webfont(  get_theme_mod( 'lbmn_calltoaction_font', LBMN_CALLTOACTION_FONT_DEFAULT) );
	$calltoaction_fontfamily =  str_replace('+', ' ', $calltoaction_fontfamily['font_family']);
	$calltoaction_fontsize = get_theme_mod( 'lbmn_calltoaction_fontsize', LBMN_CALLTOACTION_FONTSIZE_DEFAULT );
	$calltoaction_fontweight = get_theme_mod( 'lbmn_calltoaction_fontweight', LBMN_CALLTOACTION_FONTWEIGHT_DEFAULT );

	$styles .= "
	.calltoaction-area {
		background-color: $calltoaction_bgcolor;
		height: {$calltoaction_height}px;
		line-height: {$calltoaction_height}px;
	}";

	$styles .= ".calltoaction-area, .calltoaction-area * {color: $calltoaction_txtcolor; }";

	$styles .= ".calltoaction-area:hover {";
	$styles .= "background-color: $calltoaction_bgcolor_hover;";
	$styles .= "}";
	$styles .= ".calltoaction-area:hover, .calltoaction-area:hover * {color: $calltoaction_txtcolor_hover; }";

	// Call to action area custom fonts
	$styles .= ".calltoaction-area__content";
	$styles .= "{";
	$styles .= "font-family:{$calltoaction_fontfamily};";
	$styles .= "font-weight:{$calltoaction_fontweight};";
	$styles .= "font-size:{$calltoaction_fontsize}px;";
	$styles .= "}";

	/**
	* ----------------------------------------------------------------------
	* Form Elements
	*/
	// form normal
	$styles  .= 'input[type="text"], input[type="password"], input[type="date"], input[type="datetime"], input[type="datetime-local"], input[type="month"], input[type="week"], input[type="email"], input[type="number"], input[type="search"], input[type="tel"], input[type="time"], input[type="url"], textarea {';
	$styles  .= "	background: $content_background;";
	$styles  .= "}";

	/**
	 * ----------------------------------------------------------------------
	 * Minimize generated styles
	 */

	// Remove comments
	// $styles = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles);

	// Remove space after colons
	$styles = str_replace(': ', ':', $styles);
	// Remove whitespace
	$styles = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   ', '    '), '', $styles);
	// Wrap CSS into <style> tags
	$styles = "<!-- Dynamically generated styles ". date("Y-m-d H:i") ."  -->\n<style type='text/css' id ='" . LBMN_THEME_NAME . "_customized_css'>\n" . $styles . "\n</style>";
	return $styles;
}

/**
 * Set cache for 24 hours
 */
function lbmn_customized_css_cache() {
	$data = get_transient( 'lbmn_customized_css' );
	if ( $data === false ) {
		$data = lbmn_customized_css();
		set_transient( 'lbmn_customized_css', $data, 3600 * 24 );
	}

	echo "\n<!-- Customized CSS: Start -->\n";
	echo $data;
	echo "\n<!-- Customized CSS: End -->\n";
}
add_action( 'wp_head', 'lbmn_customized_css_cache', 199 );

/**
 * Reset cache when in customizer
 */
function lbmn_customized_css_cache_reset($printcss = true) {
	delete_transient( 'lbmn_customized_css' );

	if ($printcss) {
		lbmn_customized_css_cache();
	}
}
add_action( 'customize_preview_init', 'lbmn_customized_css_cache_reset' );