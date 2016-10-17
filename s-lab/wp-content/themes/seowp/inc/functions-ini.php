<?php
/**
 * Functions called on theme initialization
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * – Enqueue scripts and styles for the front-end
 * – Enqueue scripts and styles to load in WP admin area
 * – Register off-canvas mobile menu widgetized area
 * – Set the content width based on the theme's design
 * – Change default image compression value
 * – Clean WordPress Admin Bar from unwanted premium plugin links
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
 * -----------------------------------------------------------------------------
 * Enqueue scripts and styles
 */

function lbmn_scripts() {
	$theme_dir = get_template_directory_uri();
	// JavaScript files output (the ones we don't minify)

	wp_register_script(
		'lbmn-modernizr', // handle
		$theme_dir . '/javascripts/custom.modernizr.js',
		false,          // deps
		'2.6.2',       // ver
		false         // in_footer
	);
	wp_enqueue_script( 'lbmn-modernizr' );
	wp_enqueue_script( 'jquery' ); // wp_enqueue_script( 'jquery-migrate' );


	// JavaScript files output
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || LBMN_SCRIPT_DEBUG ) {

		// Output original scripts if define( 'SCRIPT_DEBUG', true );
		// is set in wp-config.php

		wp_enqueue_script(
			'lbmn-custom-js', // handle
			$theme_dir . '/javascripts/scripts.js',
			array( 'jquery', 'mm_menu_functions' ), // deps
			// mm_menu_functions - is Mega Main Menu plugin script that we need
			// to be loaded before our js code
			'20141107',  // ver
			true        // in_footer
		);

	} else {

		// Output minified scripts if 'SCRIPT_DEBUG' isn't set to true in config.php
		// define( 'SCRIPT_DEBUG', true );

		wp_enqueue_script(
			'lbmn-custom-js', // handle
			$theme_dir . '/javascripts/scripts.min.js',
			array( 'jquery', 'mm_menu_functions' ), // deps
			'20141107',       // ver
			true             // in_footer
		);

	}

	// Custom icon font styles
	wp_enqueue_style( 'lbmn-iconfont', $theme_dir . '/iconfont/style.css');

	// Theme main css style
	wp_enqueue_style( 'lbmn-style', get_stylesheet_uri(), false, '20140107-3' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/**
	 * ----------------------------------------------------------------------
	 * Special styles used when not all required plugins installed
	 */

	if ( !is_plugin_active('ds-live-composer/ds-live-composer.php')  || !get_option( LBMN_THEME_NAME . '_basic_config_done')  ){
		wp_enqueue_style( 'lbmn-livecomposer-alternative-style', $theme_dir . '/design/nopluginscss/nolivecomposeractive.css', false );
	}

	if ( !is_plugin_active('mega_main_menu/mega_main_menu.php') ||  !get_option( LBMN_THEME_NAME . '_basic_config_done') ){
		wp_enqueue_style( 'lbmn-megamainmenu-alternative-style', $theme_dir . '/design/nopluginscss/nomegamenuactive.css', false );
	}


	/**
	 * --------------------------------------------------------------------------
	 * Google Web Fonts
	 * Compose links to the Google Fonts used to be included in the <head>
	 */

	$googlefonts_toload = array();
	$googlefonts_weights = lbmn_get_goolefonts();
	$googlefonts_toload_prepared ='';
	$first_font = true;

	for ( $i=1; $i < 5 ; $i++ ) {
		// if use google font check box is selected
		if ( get_theme_mod( 'lbmn_font_preset_usegooglefont_' . $i, 1 ) ) {
			// If GoogleFont name is set use it
			if ( get_theme_mod( 'lbmn_font_preset_googlefont_' . $i, '' ) ) {
				$prefix = '';
				if ( !$first_font ) {
					$prefix = '|';
				}

				$googlefonts_toload[$i] =
					$prefix .
					get_theme_mod( 'lbmn_font_preset_googlefont_' . $i, '' );

			// If no GoogleFont name set use the default one for this preset
			} else {
				$prefix = '';
				if ( !$first_font ) {
					$prefix = '|';
				}

				$googlefonts_toload[$i] =
					$prefix .
					get_theme_mod(
						'lbmn_font_preset_googlefont_' . $i,
						constant( 'LBMN_FONT_PRESET_GOOGLEFONT_'.$i.'_DEFAULT' )
					);
			}

			// If font set, attach it's weights
			if ( $googlefonts_toload[$i] ) {

				str_replace( ' ', '+', $googlefonts_toload[$i] );

				$first_weight = true;
				foreach ( $googlefonts_weights[$googlefonts_toload[$i]] as $weight ) {

					// filter our italic fonts for speed optimization
					if ( ! stristr($weight, 'italic') ) {

						if ( $first_weight ) {
							$googlefonts_toload[$i] .= ':';
						}

						$googlefonts_toload[$i] .= $weight . ',';
					}

					$first_weight = false;
				}

				if ( substr($googlefonts_toload[$i], -1) == ',' ) {

					$googlefonts_toload[$i] =
					substr_replace( $googlefonts_toload[$i] , "", -1 );
					// remove last character ',' in a string

				}
			}
		}

	}

	foreach ( $googlefonts_toload as $google_font ) {
		$googlefonts_toload_prepared .= $google_font . '|';
	}

	if ( $googlefonts_toload_prepared ) {
		$googlefonts_toload_prepared =
			substr_replace( $googlefonts_toload_prepared , "", -1 );
			// remove last character '|' in a string
		$googlefonts_url = "//fonts.googleapis.com/css?family=".$googlefonts_toload_prepared;
		$googlefonts_ext = '&subset=latin';

		if ( get_theme_mod( 'lbmn_font_characterset_latinextended', 0 ) ) {
			$googlefonts_ext .= ',latin-ext';
		}

		if ( get_theme_mod( 'lbmn_font_characterset_cyrillic', 0 ) ) {
			$googlefonts_ext .= ',cyrillic';
		}

		if ( get_theme_mod( 'lbmn_font_characterset_cyrillicextended', 0 ) ) {
			$googlefonts_ext .= ',cyrillic-ext';
		}

		if ( get_theme_mod( 'lbmn_font_characterset_greek', 0 ) ) {
			$googlefonts_ext .= ',greek';
		}

		if ( get_theme_mod( 'lbmn_font_characterset_greekextended', 0 ) ) {
			$googlefonts_ext .= ',greek-ext';
		}

		if ( get_theme_mod( 'lbmn_font_characterset_vietnamese', 0 ) ) {
			$googlefonts_ext .= ',vietnamese';
		}

		wp_enqueue_style(
			'lbmn-google-fonts',
			$googlefonts_url.$googlefonts_ext
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lbmn_scripts', 100, 1 ); // dflt prrty is 20


/**
 * -----------------------------------------------------------------------------
 * Scripts to load in WP admin area
 */

function lbmn_adminscripts() {
	$theme_dir = get_template_directory_uri();

	// some admin elements improvements
	wp_enqueue_style( 'lbmn-adminstyles', $theme_dir . '/adminstyle.css');
	// icon font css for custom icons selector
	wp_enqueue_style( 'lbmn-iconfont', $theme_dir . '/iconfont/style.css');
	// scripts used here and there in the WP admin area
	wp_enqueue_script(
		'lbmn-wpadmin-js',   // handle
		$theme_dir.'/javascripts/wpadmin-scripts.js',
		array(
			'jquery',
			'jquery-effects-core',
			'jquery-effects-bounce'
		), // deps
		'20141129',     // ver
		true             // in_footer
	);

	global $post;

	// Send object 'lbmnData'
	if ($post) {
		$lbmnData = array(
			'currentPageContent' => get_post_meta( $post->ID, 'dslc_search_content', true ),
			'currentPageLCCode' => get_post_meta( $post->ID, 'dslc_code', true ),
		);
	} else {
		$lbmnData = array();
	}

	wp_localize_script( 'lbmn-wpadmin-js', 'lbmnData', $lbmnData );

}
add_action( 'admin_enqueue_scripts', 'lbmn_adminscripts' );


/**
 * ----------------------------------------------------------------------
 * Register off-canvas mobile menu widgetized area
 */

function lbmn_widgets_init() {
	/*
	Now we create sidebars dynamically with LiveComposer
	so this code is not used any more

	register_sidebar( array(
		'name'          => __( 'Sidebar', 'lbmn' ),
		'id'            => 'sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );
	*/

	register_sidebar( array(
			'name'          => __( 'Mobile: Off-canvas Panel', 'lbmn' ),
			'id'            => 'mobile-offcanvas',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title"><span>',
			'after_title'   => '</span></h3>',
		) );

}
add_action( 'widgets_init', 'lbmn_widgets_init' );

/**
 * -----------------------------------------------------------------------------
 * Set the content width based on the theme's design.
 */
if ( !isset( $content_width ) ) {
	$content_width = LBMN_CONTENT_WIDTH;
}

/**
 * -----------------------------------------------------------------------------
 * Change default image compression value
 * Everything below 95% looks terribly bad
 */
add_filter( 'jpeg_quality', 'lbmn_change_image_quality' );
function lbmn_change_image_quality( $arg ) {
	return 95;
}

/**
 * ----------------------------------------------------------------------
 * Add support for SVG file uploads
 * http://www.trickspanda.com/2014/01/add-svg-upload-support-wordpress/
 */
add_filter( 'upload_mimes', 'lbmn_mime_types' );
function lbmn_mime_types( $mimes ){
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

/**
 * ----------------------------------------------------------------------
 * Clean WordPress Admin Bar from unwanted premium plugin links
 */
function lbmn_admin_bar(){
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('essb'); // Easy Social Share buttons
   $wp_admin_bar->remove_menu('mega_main_menu_options'); // Mega Main Menu
}
add_action( 'wp_before_admin_bar_render', 'lbmn_admin_bar' );
