<?php
/**
 * Custom meta boxes initialization
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * To create meta boxes we use 'Meta Box' plugin.
 * by http://www.deluxeblogtips.com/
 *
 * In this file we define all the meta boxes used. For now it's only
 * 'Custom footer' dropdown select for pages.
 *
 * See /plugins/meta-box/demo/demo.php for available controls
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

$prefix = 'lbmn_';
global $meta_boxes;
$meta_boxes = array();

// Set array with all the footers listed
// See /inc/functions-extras.php for lbmn_return_all_footers()
$footer_posts = lbmn_return_all_footers();

/**
 * ----------------------------------------------------------------------
 * Page settings meta box
 */

$meta_boxes[] = array(
	// Meta box id, UNIQUE per meta box. Optional since 4.1.5
	'id' => 'lbmn-page-settings',

	// Meta box title - Will appear at the drag and drop handle bar. Required.
	'title' => __( 'Page Design Settings', 'lbmn' ),

	// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
	'pages' => array( 'page' ),

	// Where the meta box appear: normal (default), advanced, side. Optional.
	'context' => 'side',

	// Order of meta box: high (default), low. Optional.
	'priority' => 'high',

	// Auto save: true, false (default). Optional.
	'autosave' => true,

	// List of meta fields
	'fields' => array(
		// CHECKBOX LIST
		array(
			'name' => '',
			'id'   => "{$prefix}page_title_settings",
			'type' => 'checkbox_list',
			'options' => array(
				'menuoverlay' => '<strong>' . __( 'Menu cover content', 'lbmn' ) . '</strong>',
			),
		),

		// SELECT BOX
		array(
			'name'     => __( 'Custom footer', 'lbmn' ),
			'id'       => "{$prefix}custom_footer_id",
			'type'     => 'select',
			'options'  => $footer_posts,
			'multiple'    => false,
			'std'         => '',
			'placeholder' => __( 'Select an Item', 'lbmn' ),
		),
	),
);


/**
* ----------------------------------------------------------------------
* Register defined meta boxes
*/

/**
 * Register meta boxes
 *
 * @return void
 */
function lbmn_register_custom_meta_boxes()
{
	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( !class_exists( 'RW_Meta_Box' ) )
		return;

	global $meta_boxes;
	foreach ( $meta_boxes as $meta_box )
	{
		new RW_Meta_Box( $meta_box );
	}
}
// Hook to 'admin_init' to make sure the meta box class is loaded before
// (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking page template, categories, etc.
add_action( 'admin_init', 'lbmn_register_custom_meta_boxes' );
