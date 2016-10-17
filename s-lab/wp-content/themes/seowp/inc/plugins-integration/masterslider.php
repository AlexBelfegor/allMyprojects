<?php
/**
 * Master Slider plugin integration
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * In this file we integrate MasterSlider with our theme:
 * 	– Disable automatic plugin updates
 *  	– Create a function for demo sliders import
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


// Check if MasterSlider is active
if ( defined('MSWP_AVERTA_VERSION') ) {

	// Disabling the auto-update feature
	add_filter( 'masterslider_disable_auto_update', '__return_true' );

} //if ( defined( 'DS_LIVE_COMPOSER_URL' ) )
