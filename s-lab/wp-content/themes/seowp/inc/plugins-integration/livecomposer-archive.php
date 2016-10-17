<?php
/**
 * Live Composer: Archive Page Template Support
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * User can create archive page designs for
 * the next archive listing pages:
 * 	– search results list,
 * 	– author posts list,
 * 	– posts by category listing,
 * 	– posts by tab listing,
 *  	– posts by date listing
 *  	– home page with latest posts
 * 	– nothing found page,
 * 	– 404 error page,
 *
 * To change design of these listing pages in other themes you need to edit
 * PHP files. In our theme user has total control over archive pages via
 * Live Composer powered pages of specially created content type (lbmn_archive).
 *
 * These lbmn_archive pages are actually Live Composer - powered pages
 * with archive listing module inside. With this approach we provide
 * a theme user with a possibility to edit/create new archive pages
 * the same way they work with normal pages.
 *
 * In the WP admin there is a special section for this:
 * WP admin > Appearance > System Templates
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
	* Special content type for archive templates
	* User can create archive page designs (search, author, category, tag, date)
	* the same way he creates pages usign Live Composer
	* In the back-end archive page templates is just a custom content type
	*/

	// Register Custom Post Type
	add_action( 'init', 'lbmn_archive_cpt', 0 );
	function lbmn_archive_cpt() {

		$labels = array(
			'name'                => _x( 'System Page Templates', 'Post Type General Name', 'text_domain' ),
			'singular_name'       => _x( 'System Page Template', 'Post Type Singular Name', 'text_domain' ),
			// 'menu_name'           => __( 'Post Type', 'text_domain' ),
			// 'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
			'all_items'           => __( 'All Templates', 'text_domain' ),
			'view_item'           => __( 'View Template', 'text_domain' ),
			'add_new_item'        => __( 'Add New Template', 'text_domain' ),
			'add_new'             => __( 'Add Template', 'text_domain' ),
			'edit_item'           => __( 'Edit Template', 'text_domain' ),
			'update_item'         => __( 'Update Template', 'text_domain' ),
			// 'search_items'        => __( 'Search Item', 'text_domain' ),
			'not_found'           => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
		);
		$args = array(
			'label'               => __( 'lbmn_archive', 'text_domain' ),
			// 'description'         => __( 'Archive page templates used for pages like ', 'text_domain' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom-fields', ),
			// 'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 6,
			'menu_icon'           => '',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true, //do not change to false
			'capability_type'     => 'page',
		);
		register_post_type( 'lbmn_archive', $args );
	}


	// Add custom Apperance > Footers menu item
	add_action('admin_menu', 'lbmn_archives_add_appearance_menu');
	function lbmn_archives_add_appearance_menu(){
	     add_theme_page( 'System Page Templates', 'System Templates', 'edit_theme_options', 'edit.php?post_type=lbmn_archive', '');
	}


	/**
	 * ----------------------------------------------------------------------
	 * Modified code from /ds-live-composer/includes/display-functions.php
	 * to make custom CSS render not only for singular pages but for
	 * archive and 404 page
	 */
	add_action( 'wp_head', 'lbmn_dslc_custom_css', 11 ); //defaulr priority is 10
	function lbmn_dslc_custom_css() {

		// use original 'dslc_custom_css()' for singular posts
		if ( is_singular() )
			return;


		if ( is_404() || is_archive() || is_search() || is_home() ) {

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


			// Get the service page (archive, 404, search results) template id
			// that is set by user using Theme Customizer ('System Page Templates')
			$template_post_id = '';

			if ( is_search() ) {
				$template_post_id = get_theme_mod( 'lbmn_systempage_searchresults', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_SEARCHRESULTS_DEFAULT, 'lbmn_archive' ) );
				if ( !have_posts() ) {
					$template_post_id = get_theme_mod( 'lbmn_systempage_nosearchresults', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_NOSEARCHRESULTS_DEFAULT, 'lbmn_archive' ) );
				}
			}

			if ( is_404() ) {
				$template_post_id = get_theme_mod( 'lbmn_systempage_404', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_404_DEFAULT, 'lbmn_archive' ) );
			}

			if ( is_archive() ) {
				if ( is_category() ) {
					$template_post_id = get_theme_mod( 'lbmn_systempage_category', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_CATEGORY_DEFAULT, 'lbmn_archive' ) );
				} elseif ( is_tag() ) {
					$template_post_id = get_theme_mod( 'lbmn_systempage_tag', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_TAG_DEFAULT, 'lbmn_archive' ) );
				} elseif ( is_date() ) {
					$template_post_id = get_theme_mod( 'lbmn_systempage_date', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_DATE_DEFAULT, 'lbmn_archive' ) );
				} elseif ( is_author() ) {
					$template_post_id = get_theme_mod( 'lbmn_systempage_authors', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_AUTHORS_DEFAULT, 'lbmn_archive' ) );
				}
			}

			// if the current page is the blog home page
			if ( is_home() ) {
				// get 'dslc_code' from the blog home page
				$template_post_id = get_option('page_for_posts');

				// if it's not available use template set for 'frontpage_posts'
				if ( !get_post_meta($template_post_id, 'dslc_code', true ) ) {
					$template_post_id = get_theme_mod( 'lbmn_systempage_frontpage_posts', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_FRONTPAGE_POSTS_DEFAULT, 'lbmn_archive' ) );
				}
			}


			echo '<style type="text/css">';
				// Get composer code
				// $post_id = get_the_ID();
				$composer_code .= get_post_meta( $template_post_id, 'dslc_code', true );

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
					echo $dslc_css_style;

			echo '</style>';
		}
	}


	/**
	 * ----------------------------------------------------------------------
	 * Create a new special module 'Archive Posts Grid' designed
	 * to be used on archive pages for posts listing
	 */

		// Register Module
		add_action('dslc_hook_register_modules',
			create_function('', 'return dslc_register_module( "LBMN_ArchivePostsGrid_LC_Module" );')
		);

		class LBMN_ArchivePostsGrid_LC_Module extends DSLC_Module {

			// Module Attributes
			var $module_id = 'LBMN_ArchivePostsGrid_LC_Module';
			var $module_title = 'Archive Posts Grid';
			var $module_icon = 'circle';
			var $module_category = 'general';

			function options() {

				// Get registered post types
				$post_types = get_post_types( array( 'public' => true ), 'objects' );
				$post_types_choices = array();
				$post_types_choices_default = '';


				// Generate usable array of post types
				foreach ( $post_types as $post_type_id => $post_type ) {

					// Generate array of post typed activated by default
					if ( $post_type->public && !$post_type->exclude_from_search ) {
						$post_types_choices[] = array(
							'label' => $post_type->labels->name,
							'value' => $post_type_id
						);

						$post_types_choices_default .= $post_type_id . ' ';
					}
				}



				// Options
				$dslc_options = array(
					// array(
					// 	'label' => __( 'Post Type', 'dslc_string' ),
					// 	'id' => 'post_type',
					// 	'std' => 'post',
					// 	'type' => 'select',
					// 	'choices' => $post_types_choices
					// ),
					array(
						'label' => __( 'Post Type', 'dslc_string' ),
						'id' => 'post_type',
						'std' => $post_types_choices_default,
						'type' => 'checkbox',
						'choices' => $post_types_choices,
					),
					array(
						'label' => __( 'Type', 'dslc_string' ),
						'id' => 'type',
						'std' => 'grid',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Grid', 'dslc_string' ),
								'value' => 'grid'
							),
							array(
								'label' => __( 'Masonry Grid', 'dslc_string' ),
								'value' => 'masonry'
							),
							array(
								'label' => __( 'Carousel', 'dslc_string' ),
								'value' => 'carousel'
							)
						)
					),
					array(
						'label' => __( 'Orientation', 'dslc_string' ),
						'id' => 'orientation',
						'std' => 'vertical',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Vertical', 'dslc_string' ),
								'value' => 'vertical'
							),
							array(
								'label' => __( 'Horizontal', 'dslc_string' ),
								'value' => 'horizontal'
							)
						)
					),
					// array(
					// 	'label' => __( 'Posts Per Page', 'dslc_string' ),
					// 	'id' => 'amount',
					// 	'std' => '4',
					// 	'type' => 'text',
					// ),
					// array(
					// 	'label' => __( 'Pagination', 'dslc_string' ),
					// 	'id' => 'pagination_type',
					// 	'std' => 'disabled',
					// 	'type' => 'select',
					// 	'choices' => array(
					// 		array(
					// 			'label' => __( 'Disabled', 'dslc_string' ),
					// 			'value' => 'disabled',
					// 		),
					// 		array(
					// 			'label' => __( 'Numbered', 'dslc_string' ),
					// 			'value' => 'numbered',
					// 		)
					// 	),
					// ),
					//



					array(
						'label' => __( 'Posts Per Row', 'dslc_string' ),
						'id' => 'columns',
						'std' => '3',
						'type' => 'select',
						'choices' => $this->posts_per_row_choices,
					),
					array(
						'label' => __( 'Order By', 'dslc_string' ),
						'id' => 'orderby',
						'std' => 'date',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Publish Date', 'dslc_string' ),
								'value' => 'date'
							),
							array(
								'label' => __( 'Modified Date', 'dslc_string' ),
								'value' => 'modified'
							),
							array(
								'label' => __( 'Random', 'dslc_string' ),
								'value' => 'rand'
							),
							array(
								'label' => __( 'Alphabetic', 'dslc_string' ),
								'value' => 'title'
							),
							array(
								'label' => __( 'Comment Count', 'dslc_string' ),
								'value' => 'comment_count'
							),
						)
					),
					array(
						'label' => __( 'Order', 'dslc_string' ),
						'id' => 'order',
						'std' => 'DESC',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Ascending', 'dslc_string' ),
								'value' => 'ASC'
							),
							array(
								'label' => __( 'Descending', 'dslc_string' ),
								'value' => 'DESC'
							)
						)
					),
					array(
						'label' => __( 'Offset', 'dslc_string' ),
						'id' => 'offset',
						'std' => '0',
						'type' => 'text',
					),

					/* Styling */

					array(
						'label' => __( 'Elements', 'dslc_string' ),
						'id' => 'elements',
						'std' => '',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Heading', 'dslc_string' ),
								'value' => 'main_heading'
							),
							array(
								'label' => __( 'Filters', 'dslc_string' ),
								'value' => 'filters'
							),
						),
						'section' => 'styling'
					),

					array(
						'label' => __( 'Post Elements', 'dslc_string' ),
						'id' => 'post_elements',
						'std' => 'thumbnail meta title excerpt button',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Thumbnail', 'dslc_string' ),
								'value' => 'thumbnail',
							),
							array(
								'label' => __( 'Title', 'dslc_string' ),
								'value' => 'title',
							),
							array(
								'label' => __( 'Meta', 'dslc_string' ),
								'value' => 'meta',
							),
							array(
								'label' => __( 'Excerpt', 'dslc_string' ),
								'value' => 'excerpt',
							),
							array(
								'label' => __( 'Button', 'dslc_string' ),
								'value' => 'button',
							),
						),
						'section' => 'styling'
					),

					array(
						'label' => __( 'Carousel Elements', 'dslc_string' ),
						'id' => 'carousel_elements',
						'std' => 'arrows circles',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Arrows', 'dslc_string' ),
								'value' => 'arrows'
							),
							array(
								'label' => __( 'Circles', 'dslc_string' ),
								'value' => 'circles'
							),
						),
						'section' => 'styling'
					),

					/**
					 * Wrapper
					 */

					array(
						'label' => __( 'BG Color', 'dslc_string' ),
						'id' => 'css_wrapper_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Color', 'dslc_string' ),
						'id' => 'css_wrapper_border_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Width', 'dslc_string' ),
						'id' => 'css_wrapper_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Borders', 'dslc_string' ),
						'id' => 'css_wrapper_border_trbl',
						'std' => 'top right bottom left',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'dslc_string' ),
								'value' => 'top'
							),
							array(
								'label' => __( 'Right', 'dslc_string' ),
								'value' => 'right'
							),
							array(
								'label' => __( 'Bottom', 'dslc_string' ),
								'value' => 'bottom'
							),
							array(
								'label' => __( 'Left', 'dslc_string' ),
								'value' => 'left'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
					),
					array(
						'label' => __( 'Border Radius - Top', 'dslc_string' ),
						'id' => 'css_wrapper_border_radius_top',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section' => 'styling',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
						'id' => 'css_wrapper_border_radius_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section' => 'styling',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'css_margin_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Vertical', 'dslc_string' ),
						'id' => 'css_wrapper_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Padding Horizontal', 'dslc_string' ),
						'id' => 'css_wrapper_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-posts',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
					),

					/**
					 * Separator
					 */

					array(
						'label' => __( 'Enable/Disable', 'dslc_string' ),
						'id' => 'separator_enabled',
						'std' => 'enabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Enabled', 'dslc_string' ),
								'value' => 'enabled'
							),
							array(
								'label' => __( 'Disabled', 'dslc_string' ),
								'value' => 'disabled'
							),
						),
						'section' => 'styling',
						'tab' => 'Row Separator'
					),
					array(
						'label' => __( 'Color', 'dslc_string' ),
						'id' => 'css_sep_border_color',
						'std' => '#ededed',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-post-separator',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Row Separator'
					),
					array(
						'label' => __( 'Height', 'dslc_string' ),
						'id' => 'css_sep_height',
						'std' => '32',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-post-separator',
						'affect_on_change_rule' => 'margin-bottom,padding-bottom',
						'ext' => 'px',
						'min' => 0,
						'max' => 300,
						'section' => 'styling',
						'tab' => 'Row Separator'
					),
					array(
						'label' => __( 'Style', 'dslc_string' ),
						'id' => 'css_sep_style',
						'std' => 'dashed',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Invisible', 'dslc_string' ),
								'value' => 'none'
							),
							array(
								'label' => __( 'Solid', 'dslc_string' ),
								'value' => 'solid'
							),
							array(
								'label' => __( 'Dashed', 'dslc_string' ),
								'value' => 'dashed'
							),
							array(
								'label' => __( 'Dotted', 'dslc_string' ),
								'value' => 'dotted'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-post-separator',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => 'Row Separator'
					),

					/**
					 * Thumbnail
					 */

					array(
						'label' => __( 'BG Color', 'dslc_string' ),
						'id' => 'css_thumb_bg_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'Thumbnail'
					),
					array(
						'label' => __( 'Border Color', 'dslc_string' ),
						'id' => 'css_thumb_border_color',
						'std' => '#e6e6e6',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Thumbnail'
					),
					array(
						'label' => __( 'Border Width', 'dslc_string' ),
						'id' => 'css_thumb_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Thumbnail'
					),
					array(
						'label' => __( 'Borders', 'dslc_string' ),
						'id' => 'css_thumb_border_trbl',
						'std' => 'top right bottom left',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'dslc_string' ),
								'value' => 'top'
							),
							array(
								'label' => __( 'Right', 'dslc_string' ),
								'value' => 'right'
							),
							array(
								'label' => __( 'Bottom', 'dslc_string' ),
								'value' => 'bottom'
							),
							array(
								'label' => __( 'Left', 'dslc_string' ),
								'value' => 'left'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => 'Thumbnail',
					),
					array(
						'label' => __( 'Border Radius - Top', 'dslc_string' ),
						'id' => 'css_thumb_border_radius_top',
						'std' => '4',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb, .dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section' => 'styling',
						'tab' => 'Thumbnail',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
						'id' => 'css_thumb_border_radius_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb, .dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section' => 'styling',
						'tab' => 'Thumbnail',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'thumb_margin',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'tab' => 'Thumbnail',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Margin Right', 'dslc_string' ),
						'id' => 'thumb_margin_right',
						'std' => '20',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'styling',
						'tab' => 'Thumbnail',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Padding Vertical', 'dslc_string' ),
						'id' => 'css_thumb_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Thumbnail'
					),
					array(
						'label' => __( 'Padding Horizontal', 'dslc_string' ),
						'id' => 'css_thumb_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Thumbnail'
					),
					array(
						'label' => __( 'Resize - Height', 'dslc_string' ),
						'id' => 'thumb_resize_height',
						'std' => '',
						'type' => 'text',
						'section' => 'styling',
						'tab' => 'thumbnail'
					),
					array(
						'label' => __( 'Resize - Width', 'dslc_string' ),
						'id' => 'thumb_resize_width_manual',
						'std' => '',
						'type' => 'text',
						'section' => 'styling',
						'tab' => 'thumbnail',
					),
					array(
						'label' => __( 'Resize - Width', 'dslc_string' ),
						'id' => 'thumb_resize_width',
						'std' => '',
						'type' => 'text',
						'section' => 'styling',
						'tab' => 'thumbnail',
						'visibility' => 'hidden'
					),
					array(
						'label' => __( 'Width', 'dslc_string' ),
						'id' => 'thumb_width',
						'std' => '100',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'width',
						'section' => 'styling',
						'tab' => 'Thumbnail',
						'min' => 1,
						'max' => 100,
						'ext' => '%'
					),

					/**
					 * Main
					 */

					array(
						'label' => __( 'Location', 'dslc_string' ),
						'id' => 'main_location',
						'std' => 'bellow',
						'type' => 'select',
						'section' => 'styling',
						'tab' => 'Main',
						'choices' => array(
							array(
								'label' => __( 'Bellow Thumbnail', 'dslc_string' ),
								'value' => 'bellow'
							),
							array(
								'label' => __( 'Inside Thumbnail ( hover )', 'dslc_string' ),
								'value' => 'inside'
							),
							array(
								'label' => __( 'Inside Thumbnail ( always visible )', 'dslc_string' ),
								'value' => 'inside_visible'
							),
						),
					),
					array(
						'label' => __( ' BG Color', 'dslc_string' ),
						'id' => 'css_main_bg_color',
						'std' => '#ffffff',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'Main'
					),
					array(
						'label' => __( 'Border Color', 'dslc_string' ),
						'id' => 'css_main_border_color',
						'std' => '#e8e8e8',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Main'
					),
					array(
						'label' => __( 'Border Width', 'dslc_string' ),
						'id' => 'css_main_border_width',
						'std' => '1',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Main'
					),
					array(
						'label' => __( 'Borders', 'dslc_string' ),
						'id' => 'css_main_border_trbl',
						'std' => 'right bottom left',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'dslc_string' ),
								'value' => 'top'
							),
							array(
								'label' => __( 'Right', 'dslc_string' ),
								'value' => 'right'
							),
							array(
								'label' => __( 'Bottom', 'dslc_string' ),
								'value' => 'bottom'
							),
							array(
								'label' => __( 'Left', 'dslc_string' ),
								'value' => 'left'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => 'Main',
					),
					array(
						'label' => __( 'Border Radius - Top', 'dslc_string' ),
						'id' => 'css_main_border_radius_top',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
						'section' => 'styling',
						'tab' => 'Main',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Border Radius - Bottom', 'dslc_string' ),
						'id' => 'css_main_border_radius_bottom',
						'std' => '4',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
						'section' => 'styling',
						'tab' => 'Main',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Padding Vertical', 'dslc_string' ),
						'id' => 'css_main_padding_vertical',
						'std' => '25',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Main'
					),
					array(
						'label' => __( 'Padding Horizontal', 'dslc_string' ),
						'id' => 'css_main_padding_horizontal',
						'std' => '25',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Main'
					),
					array(
						'label' => __( 'Minimum Height', 'dslc_string' ),
						'id' => 'css_main_min_height',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'min-height',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Main',
						'min' => 0,
						'max' => 500
					),
					array(
						'label' => __( 'Text Align', 'dslc_string' ),
						'id' => 'css_main_text_align',
						'std' => 'center',
						'type' => 'select',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'text-align',
						'section' => 'styling',
						'tab' => 'Main',
						'choices' => array(
							array(
								'label' => __( 'Left', 'dslc_string' ),
								'value' => 'left',
							),
							array(
								'label' => __( 'Center', 'dslc_string' ),
								'value' => 'center',
							),
							array(
								'label' => __( 'Right', 'dslc_string' ),
								'value' => 'right',
							),
							array(
								'label' => __( 'Justify', 'dslc_string' ),
								'value' => 'justify',
							),
						)
					),

					/**
					 * Main Inner
					 */

					array(
						'label' => __( 'Position', 'dslc_string' ),
						'id' => 'main_position',
						'std' => 'center',
						'type' => 'select',
						'section' => 'styling',
						'tab' => 'Main Inner',
						'choices' => array(
							array(
								'label' => __( 'Top Left', 'dslc_string' ),
								'value' => 'topleft'
							),
							array(
								'label' => __( 'Top Right', 'dslc_string' ),
								'value' => 'topright'
							),
							array(
								'label' => __( 'Center', 'dslc_string' ),
								'value' => 'center'
							),
							array(
								'label' => __( 'Bottom Left', 'dslc_string' ),
								'value' => 'bottomleft'
							),
							array(
								'label' => __( 'Bottom Right', 'dslc_string' ),
								'value' => 'bottomright'
							),
						)
					),
					array(
						'label' => __( 'Margin', 'dslc_string' ),
						'id' => 'css_main_inner_margin',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main-inner',
						'affect_on_change_rule' => 'margin',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Main Inner'
					),
					array(
						'label' => __( 'Width', 'dslc_string' ),
						'id' => 'css_main_inner_width',
						'std' => '100',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main-inner',
						'affect_on_change_rule' => 'width',
						'section' => 'styling',
						'ext' => '%',
						'tab' => 'Main Inner'
					),

					/* Title Options */

					array(
						'label' => __( 'Color', 'dslc_string' ),
						'id' => 'title_color',
						'std' => '#4d4d4d',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Title'
					),
					array(
						'label' => __( 'Color - Hover', 'dslc_string' ),
						'id' => 'title_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2:hover,.dslc-cpt-post-title h2 a:hover',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Title'
					),
					array(
						'label' => __( 'Font Size', 'dslc_string' ),
						'id' => 'title_font_size',
						'std' => '17',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'styling',
						'tab' => 'Title',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Font Weight', 'dslc_string' ),
						'id' => 'css_title_font_weight',
						'std' => '500',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'font-weight',
						'section' => 'styling',
						'tab' => 'Title',
						'ext' => '',
						'min' => 100,
						'max' => 900,
						'increment' => 100
					),
					array(
						'label' => __( 'Font Family', 'dslc_string' ),
						'id' => 'css_title_font_family',
						'std' => 'Open Sans',
						'type' => 'font',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'font-family',
						'section' => 'styling',
						'tab' => 'Title',
					),
					array(
						'label' => __( 'Line Height', 'dslc_string' ),
						'id' => 'title_line_height',
						'std' => '29',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'line-height',
						'section' => 'styling',
						'tab' => 'Title',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'title_margin',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'tab' => 'Title',
						'ext' => 'px'
					),

					/**
					 * Meta
					 */

					array(
						'label' => __( 'Border Color', 'dslc_string' ),
						'id' => 'css_meta_border_color',
						'std' => '#e5e5e5',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Border Width', 'dslc_string' ),
						'id' => 'css_meta_border_width',
						'std' => '1',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Borders', 'dslc_string' ),
						'id' => 'css_meta_border_trbl',
						'std' => 'top bottom',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'dslc_string' ),
								'value' => 'top'
							),
							array(
								'label' => __( 'Right', 'dslc_string' ),
								'value' => 'right'
							),
							array(
								'label' => __( 'Bottom', 'dslc_string' ),
								'value' => 'bottom'
							),
							array(
								'label' => __( 'Left', 'dslc_string' ),
								'value' => 'left'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Color', 'dslc_string' ),
						'id' => 'css_meta_color',
						'std' => '#a8a8a8',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Font Size', 'dslc_string' ),
						'id' => 'css_meta_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'font-size',
						'section' => 'styling',
						'tab' => 'Meta',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Font Family', 'dslc_string' ),
						'id' => 'css_meta_font_family',
						'std' => 'Libre Baskerville',
						'type' => 'font',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'font-family',
						'section' => 'styling',
						'tab' => 'Meta',
					),
					array(
						'label' => __( 'Font Weight', 'dslc_string' ),
						'id' => 'css_meta_font_weight',
						'std' => '400',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'font-weight',
						'section' => 'styling',
						'tab' => 'Meta',
						'ext' => '',
						'min' => 100,
						'max' => 900,
						'increment' => 100
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'css_meta_margin_bottom',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Padding Vertical', 'dslc_string' ),
						'id' => 'css_meta_padding_vertical',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Padding Horizontal', 'dslc_string' ),
						'id' => 'css_meta_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Meta'
					),

					array(
						'label' => __( 'Link - Color', 'dslc_string' ),
						'id' => 'css_meta_link_color',
						'std' => '#5890e5',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta a',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Meta'
					),
					array(
						'label' => __( 'Link - Hover - Color', 'dslc_string' ),
						'id' => 'css_meta_link_color_hover',
						'std' => '#5890e5',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta a:hover',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Meta'
					),

					/**
					 * Excerpt
					 */

					array(
						'label' => __( 'Excerpt or Content', 'dslc_string' ),
						'id' => 'excerpt_or_content',
						'std' => 'excerpt',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Excerpt', 'dslc_string' ),
								'value' => 'excerpt'
							),
							array(
								'label' => __( 'Content', 'dslc_string' ),
								'value' => 'content'
							),
						),
						'section' => 'styling',
						'tab' => 'Excerpt'
					),
					array(
						'label' => __( 'Color', 'dslc_string' ),
						'id' => 'css_excerpt_color',
						'std' => '#a6a6a6',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Excerpt'
					),
					array(
						'label' => __( 'Font Size', 'dslc_string' ),
						'id' => 'css_excerpt_font_size',
						'std' => '13',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'font-size',
						'section' => 'styling',
						'tab' => 'Excerpt',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Font Weight', 'dslc_string' ),
						'id' => 'css_excerpt_font_weight',
						'std' => '500',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'font-weight',
						'section' => 'styling',
						'tab' => 'Excerpt',
						'ext' => '',
						'min' => 100,
						'max' => 900,
						'increment' => 100
					),
					array(
						'label' => __( 'Font Family', 'dslc_string' ),
						'id' => 'css_excerpt_font_family',
						'std' => 'Bitter',
						'type' => 'font',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'font-family',
						'section' => 'styling',
						'tab' => 'Excerpt',
					),
					array(
						'label' => __( 'Line Height', 'dslc_string' ),
						'id' => 'css_excerpt_line_height',
						'std' => '23',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt, .dslc-cpt-post-excerpt p',
						'affect_on_change_rule' => 'line-height',
						'section' => 'styling',
						'tab' => 'Excerpt',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'excerpt_margin',
						'std' => '22',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'excerpt'
					),
					array(
						'label' => __( 'Max Length ( amount of words )', 'dslc_string' ),
						'id' => 'excerpt_length',
						'std' => '20',
						'type' => 'text',
						'section' => 'styling',
						'tab' => 'Excerpt'
					),

					/**
					 * Button
					 */

					array(
						'label' => __( 'Text', 'dslc_string' ),
						'id' => 'button_text',
						'std' => 'CONTINUE READING',
						'type' => 'text',
						'section' => 'styling',
						'tab' => 'Button',
					),
					array(
						'label' => __( 'BG Color', 'dslc_string' ),
						'id' => 'css_button_bg_color',
						'std' => '#5890e5',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'BG Color - Hover', 'dslc_string' ),
						'id' => 'css_button_bg_color_hover',
						'std' => '#4b7bc2',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a:hover',
						'affect_on_change_rule' => 'background-color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Border Width', 'dslc_string' ),
						'id' => 'css_button_border_width',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'border-width',
						'section' => 'styling',
						'tab' => 'Button',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Borders', 'dslc_string' ),
						'id' => 'css_button_border_trbl',
						'std' => 'top right bottom left',
						'type' => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Top', 'dslc_string' ),
								'value' => 'top'
							),
							array(
								'label' => __( 'Right', 'dslc_string' ),
								'value' => 'right'
							),
							array(
								'label' => __( 'Bottom', 'dslc_string' ),
								'value' => 'bottom'
							),
							array(
								'label' => __( 'Left', 'dslc_string' ),
								'value' => 'left'
							),
						),
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'border-style',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Border Color', 'dslc_string' ),
						'id' => 'css_button_border_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Border Color - Hover', 'dslc_string' ),
						'id' => 'css_button_border_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a:hover',
						'affect_on_change_rule' => 'border-color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Border Radius', 'dslc_string' ),
						'id' => 'css_button_border_radius',
						'std' => '3',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'border-radius',
						'section' => 'styling',
						'tab' => 'Button',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Color', 'dslc_string' ),
						'id' => 'css_button_color',
						'std' => '#ffffff',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Color - Hover', 'dslc_string' ),
						'id' => 'css_button_color_hover',
						'std' => '#ffffff',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a:hover',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Font Size', 'dslc_string' ),
						'id' => 'css_button_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'styling',
						'tab' => 'Button',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Font Weight', 'dslc_string' ),
						'id' => 'css_button_font_weight',
						'std' => '800',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'font-weight',
						'section' => 'styling',
						'tab' => 'Button',
						'ext' => '',
						'min' => 100,
						'max' => 900,
						'increment' => 100
					),
					array(
						'label' => __( 'Font Family', 'dslc_string' ),
						'id' => 'css_button_font_family',
						'std' => 'Lato',
						'type' => 'font',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'font-family',
						'section' => 'styling',
						'tab' => 'Button',
					),
					array(
						'label' => __( 'Padding Vertical', 'dslc_string' ),
						'id' => 'css_button_padding_vertical',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Padding Horizontal', 'dslc_string' ),
						'id' => 'css_button_padding_horizontal',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Icon', 'dslc_string' ),
						'id' => 'button_icon_id',
						'std' => '',
						'type' => 'icon',
						'section' => 'styling',
						'tab' => 'Button',
					),
					array(
						'label' => __( 'Icon - Color', 'dslc_string' ),
						'id' => 'css_button_icon_color',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a .dslc-icon',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Icon - Hover - Color', 'dslc_string' ),
						'id' => 'css_button_icon_color_hover',
						'std' => '',
						'type' => 'color',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a:hover .dslc-icon',
						'affect_on_change_rule' => 'color',
						'section' => 'styling',
						'tab' => 'Button'
					),
					array(
						'label' => __( 'Icon - Margin Right', 'dslc_string' ),
						'id' => 'css_button_icon_margin',
						'std' => '5',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a .dslc-icon',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'styling',
						'ext' => 'px',
						'tab' => 'Button'
					),

					/**
					 * Responsive Tablet
					 */

					array(
						'label' => __( 'Responsive', 'dslc_string' ),
						'id' => 'css_res_t',
						'std' => 'disabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Disabled', 'dslc_string' ),
								'value' => 'disabled'
							),
							array(
								'label' => __( 'Enabled', 'dslc_string' ),
								'value' => 'enabled'
							),
						),
						'section' => 'responsive',
						'tab' => 'tablet',
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_t_margin_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Separator - Height', 'dslc_string' ),
						'id' => 'css_res_t_sep_height',
						'std' => '32',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-post-separator',
						'affect_on_change_rule' => 'margin-bottom,padding-bottom',
						'ext' => 'px',
						'min' => 1,
						'max' => 300,
						'section' => 'responsive',
						'tab' => 'tablet',
					),
					array(
						'label' => __( 'Thumbnail - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_t_thumb_margin',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Thumbnail - Margin Right', 'dslc_string' ),
						'id' => 'css_res_t_thumb_margin_right',
						'std' => '20',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Thumbnail - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_t_thumb_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Thumbnail - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_t_thumb_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Main - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_t_main_padding_vertical',
						'std' => '25',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Main - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_t_main_padding_horizontal',
						'std' => '25',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Title - Font Size', 'dslc_string' ),
						'id' => 'css_res_t_title_font_size',
						'std' => '17',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Title - Line Height', 'dslc_string' ),
						'id' => 'css_res_t_title_line_height',
						'std' => '29',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2, .dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'line-height',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Title - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_t_title_margin',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Meta - Font Size', 'dslc_string' ),
						'id' => 'css_res_t_meta_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Meta - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_t_meta_margin_bottom',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Meta - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_t_meta_padding_vertical',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Meta - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_t_meta_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Excerpt - Font Size', 'dslc_string' ),
						'id' => 'css_res_t_excerpt_font_size',
						'std' => '13',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Excerpt - Line Height', 'dslc_string' ),
						'id' => 'css_res_t_excerpt_line_height',
						'std' => '23',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt, .dslc-cpt-post-excerpt p',
						'affect_on_change_rule' => 'line-height',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Excerpt - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_t_excerpt_margin',
						'std' => '22',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Button - Font Size', 'dslc_string' ),
						'id' => 'css_res_t_button_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'tablet',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Button - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_t_button_padding_vertical',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Button - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_t_button_padding_horizontal',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),
					array(
						'label' => __( 'Button - Icon - Margin Right', 'dslc_string' ),
						'id' => 'css_res_t_button_icon_margin',
						'std' => '5',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a .dslc-icon',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'tablet'
					),

					/**
					 * Responsive Phone
					 */

					array(
						'label' => __( 'Responsive', 'dslc_string' ),
						'id' => 'css_res_p',
						'std' => 'disabled',
						'type' => 'select',
						'choices' => array(
							array(
								'label' => __( 'Disabled', 'dslc_string' ),
								'value' => 'disabled'
							),
							array(
								'label' => __( 'Enabled', 'dslc_string' ),
								'value' => 'enabled'
							),
						),
						'section' => 'responsive',
						'tab' => 'phone',
					),
					array(
						'label' => __( 'Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_p_margin_bottom',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px',
					),
					array(
						'label' => __( 'Separator - Height', 'dslc_string' ),
						'id' => 'css_res_p_sep_height',
						'std' => '32',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-post-separator',
						'affect_on_change_rule' => 'margin-bottom,padding-bottom',
						'ext' => 'px',
						'min' => 1,
						'max' => 300,
						'section' => 'responsive',
						'tab' => 'phone',
					),
					array(
						'label' => __( 'Thumbnail - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_p_thumb_margin',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Thumbnail - Margin Right', 'dslc_string' ),
						'id' => 'css_res_p_thumb_margin_right',
						'std' => '20',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Thumbnail - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_p_thumb_padding_vertical',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Thumbnail - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_p_thumb_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-thumb-inner',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Main - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_p_main_padding_vertical',
						'std' => '25',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Main - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_p_main_padding_horizontal',
						'std' => '25',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-main',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Title - Font Size', 'dslc_string' ),
						'id' => 'css_res_p_title_font_size',
						'std' => '17',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Title - Line Height', 'dslc_string' ),
						'id' => 'css_res_p_title_line_height',
						'std' => '29',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title h2,.dslc-cpt-post-title h2 a',
						'affect_on_change_rule' => 'line-height',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Title - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_p_title_margin',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-title',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Meta - Font Size', 'dslc_string' ),
						'id' => 'css_res_p_meta_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Meta - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_p_meta_margin_bottom',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Meta - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_p_meta_padding_vertical',
						'std' => '16',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Meta - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_p_meta_padding_horizontal',
						'std' => '0',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-meta',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Excerpt - Font Size', 'dslc_string' ),
						'id' => 'css_res_p_excerpt_font_size',
						'std' => '13',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Excerpt - Line Height', 'dslc_string' ),
						'id' => 'css_res_p_excerpt_line_height',
						'std' => '23',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt, .dslc-cpt-post-excerpt p',
						'affect_on_change_rule' => 'line-height',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Excerpt - Margin Bottom', 'dslc_string' ),
						'id' => 'css_res_p_excerpt_margin',
						'std' => '22',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-excerpt',
						'affect_on_change_rule' => 'margin-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Button - Font Size', 'dslc_string' ),
						'id' => 'css_res_p_button_font_size',
						'std' => '11',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'font-size',
						'section' => 'responsive',
						'tab' => 'phone',
						'ext' => 'px'
					),
					array(
						'label' => __( 'Button - Padding Vertical', 'dslc_string' ),
						'id' => 'css_res_p_button_padding_vertical',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'padding-top,padding-bottom',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Button - Padding Horizontal', 'dslc_string' ),
						'id' => 'css_res_p_button_padding_horizontal',
						'std' => '12',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a',
						'affect_on_change_rule' => 'padding-left,padding-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),
					array(
						'label' => __( 'Button - Icon - Margin Right', 'dslc_string' ),
						'id' => 'css_res_p_button_icon_margin',
						'std' => '5',
						'type' => 'slider',
						'refresh_on_change' => false,
						'affect_on_change_el' => '.dslc-cpt-post-read-more a .dslc-icon',
						'affect_on_change_rule' => 'margin-right',
						'section' => 'responsive',
						'ext' => 'px',
						'tab' => 'phone'
					),

				);

				// lbmn_debug_console( $this->pagination_options );
				// $dslc_options = array_merge( $dslc_options, $this->heading_options );
				// $dslc_options = array_merge( $dslc_options, $this->filters_options );
				// $dslc_options = array_merge( $dslc_options, $this->carousel_arrows_options );
				// $dslc_options = array_merge( $dslc_options, $this->carousel_circles_options );
				// $dslc_options = array_merge( $dslc_options, $this->pagination_options );
				// $dslc_options = array_merge( $dslc_options, $this->animation_options );

				$dslc_options = array_merge( $dslc_options, $this->shared_options('heading_options') );
				$dslc_options = array_merge( $dslc_options, $this->shared_options('filters_options') );
				$dslc_options = array_merge( $dslc_options, $this->shared_options('carousel_arrows_options') );
				$dslc_options = array_merge( $dslc_options, $this->shared_options('carousel_circles_options') );
				$dslc_options = array_merge( $dslc_options, $this->shared_options('pagination_options') );
				$dslc_options = array_merge( $dslc_options, $this->shared_options('animation_options') );


				// lbmn_debug_console( $dslc_options );

				return apply_filters( 'dslc_module_options', $dslc_options, $this->module_id );

			}

			// // Module Output
			function output( $options ) {

				global $dslc_active;

				if ( $dslc_active && is_user_logged_in() && current_user_can( DS_LIVE_COMPOSER_CAPABILITY ) )
					$dslc_is_admin = true;
				else
					$dslc_is_admin = false;

				$this->module_start( $options );

				/* CUSTOM START */

				if ( ! isset( $options['excerpt_length'] ) ) $options['excerpt_length'] = 20;

				/**
				 * Query
				 */

					// Fix for pagination
					if( is_front_page() && get_option( 'page_on_front', 0 ) != 0 ) {
						$currentpage = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
						global $paged;
						$paged = $currentpage;
					} else {
						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
					}


					// General args
					$args = array(
						'paged' => $paged,
						'post_type' => explode(' ', trim($options['post_type'])),  //Lumberman changed
						// 'posts_per_page' => $options['amount'],
						'order' => $options['order'],
						'orderby' => $options['orderby'],
						'offset' => $options['offset']
					);

	/**
	 * ----------------------------------------------------------------------
	 * Lumberman Extension START
	 */

					// Prepare WP_Query argument for search listing
					if ( is_search() ) {
						$args['s'] = get_query_var('s');

						global $posttypes_to_search;
						$posttypes_to_search = $args['post_type'];
					}

					// Prepare WP_Query argument for archive listing
					if ( is_archive() ) {
						if ( is_category() ) {
							$args['cat'] = get_query_var('cat');
						} elseif ( is_tag() ) {
							$args['tag_id'] = get_query_var('tag_id');
						} elseif ( is_author() ) {
							$args['author'] = get_query_var('author');
						} elseif ( is_date() ) {
							$args['m'] = get_query_var('m');
						}
					}

					// Custom posts per page doesn't work outofthebox,
					// so use global settings
					$args['posts_per_page'] = $options['amount'] = get_option( 'posts_per_page' );


					// Time based archive pages are available only for standard posts
					if ( is_date() || is_front_page() ) {
						$args['post_type'] = array('post');
					}
					// else {
						// Get all registered (not searchable content types)
						// $registered_post_types = get_post_types( array('public' => true, 'publicly_queryable' => true, 'exclude_from_search' => false ));
						// Remove some custom types to not display
						// unset($registered_post_types['dslc_templates']);
						// Add WP_Query argument to search across all the post types
						// $args['post_type'] = array_values( $registered_post_types );
					// }


	/**
	 * ----------------------------------------------------------------------
	 */

					// Category args
					if ( isset( $options['categories'] ) && $options['categories'] != '' ) {
						$cats_array = explode( ' ', $options['categories']);
						$args['category__in'] = $cats_array;
					}


					// Debug:
					// lbmn_debug_console( $args );

					// Do the query
					$dslc_query = new WP_Query( $args );

					// Debug:
					// echo $GLOBALS['wp_query']->request;
					// $customPosts = new WP_Query($yourArgs);
					// echo "Last SQL-Query: {$dslc_query->request}";

				/**
				 * Unnamed
				 */

					$columns_class = 'dslc-col dslc-' . $options['columns'] . '-col ';
					$count = 0;
					$real_count = 0;
					$increment = $options['columns'];
					$max_count = 12;

				/**
				 * Elements to show
				 */

					// Main Elements
					$elements = $options['elements'];
					if ( ! empty( $elements ) )
						$elements = explode( ' ', trim( $elements ) );
					else
						$elements = array();


					// Post Elements
					$post_elements = $options['post_elements'];
					if ( ! empty( $post_elements ) )
						$post_elements = explode( ' ', trim( $post_elements ) );
					else
						$post_elements = 'all';

					// Carousel Elements
					$carousel_elements = $options['carousel_elements'];
					if ( ! empty( $carousel_elements ) )
						$carousel_elements = explode( ' ', trim( $carousel_elements ) );
					else
						$carousel_elements = array();

				/**
				 * Classes generation
				 */

					// Posts container
					$container_class = 'dslc-posts dslc-cpt-posts dslc-clearfix dslc-cpt-posts-type-' . $options['type'] .' dslc-posts-orientation-' . $options['orientation'] . ' ';
					if ( $options['type'] == 'masonry' )
						$container_class .= 'dslc-init-masonry ';
					elseif ( $options['type'] == 'grid' )
						$container_class .= 'dslc-init-grid ';

					// Post
					$element_class = 'dslc-post dslc-cpt-post ';
					if ( $options['type'] == 'masonry' )
						$element_class .= 'dslc-masonry-item ';
					elseif ( $options['type'] == 'carousel' )
						$element_class .= 'dslc-carousel-item ';

				/**
				 * What is shown
				 */

					$show_header = false;
					$show_heading = false;
					$show_filters = false;
					$show_carousel_arrows = false;
					$show_view_all_link = false;

					if ( in_array( 'main_heading', $elements ) )
						$show_heading = true;

					if ( ( $elements == 'all' || in_array( 'filters', $elements ) ) && $options['type'] !== 'carousel' )
						$show_filters = true;

					if ( $options['type'] == 'carousel' && in_array( 'arrows', $carousel_elements ) )
						$show_carousel_arrows = true;

					if ( $show_heading || $show_filters || $show_carousel_arrows )
						$show_header = true;

				/**
				 * Carousel Items
				 */

					switch ( $options['columns'] ) {
						case 12 :
							$carousel_items = 1;
							break;
						case 6 :
							$carousel_items = 2;
							break;
						case 4 :
							$carousel_items = 3;
							break;
						case 3 :
							$carousel_items = 4;
							break;
						case 2 :
							$carousel_items = 6;
							break;
						default:
							$carousel_items = 6;
							break;
					}

				/**
				 * Heading ( output )
				 */
					if ( $show_header ) :
						?>
							<div class="dslc-module-heading">

								<!-- Heading -->

								<?php if ( $show_heading ) : ?>

									<h2 class="dslca-editable-content" data-id="main_heading_title" data-type="simple" <?php /* if ( $dslc_is_admin ) echo 'contenteditable'; */?> >
									<?php
										if ( $dslc_is_admin ) :
											echo __( 'This heading will be automatically generated by a theme' , 'lbmn' );

										elseif ( is_category() ) :
											printf( __( '%s', 'lbmn' ), '<span>' . single_cat_title( '', false ) . '</span>' );

										elseif ( is_tag() ) :
											printf( __( 'Метка: %s', 'lbmn' ), '<span>' . single_tag_title( '', false ) . '</span>' );

										elseif ( is_author() ) :
											/* Queue the first post, that way we know
											 * what author we're dealing with (if that is the case).
											*/
											the_post();
											printf( __( 'Author Archives: %s', 'lbmn' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
											/* Since we called the_post() above, we need to
											 * rewind the loop back to the beginning that way
											 * we can run the loop properly, in full.
											 */
											rewind_posts();

										elseif ( is_day() ) :
											printf( __( 'Daily Archives: %s', 'lbmn' ), '<span>' . get_the_date() . '</span>' );

										elseif ( is_month() ) :
											printf( __( 'Monthly Archives: %s', 'lbmn' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

										elseif ( is_year() ) :
											printf( __( 'Yearly Archives: %s', 'lbmn' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

										elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
											_e( 'Asides', 'lbmn' );

										elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
											_e( 'Images', 'lbmn');

										elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
											_e( 'Videos', 'lbmn' );

										elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
											_e( 'Quotes', 'lbmn' );

										elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
											_e( 'Links', 'lbmn' );

										elseif ( is_search() ) : // special title for search result page
											// get number of posts found for search query
											// $newSearch = new WP_Query("s=$s & showposts=-1");
											$search_results_count = $dslc_query->found_posts;

											_e( 'You are searching for: ', 'lbmn' );
											echo '<strong>' . get_query_var('s') . '</strong>. <br />';

											if ( 1 == $search_results_count ) {
												_e( 'There is one post that match your criteria...', 'lbmn' );
											} elseif ( 1 < $search_results_count ) {
												printf( __( 'Here are %s posts that match your criteria...', 'lbmn' ), '<span>' . $search_results_count . '</span>' );
											} else {
												_e( 'Looks like nothing was found. Sorry.', 'lbmn' ). $search_results_count;
											}

										elseif ( is_front_page() ) :
											echo get_bloginfo( 'description' );

										else :
											_e( 'Archives', 'lbmn' );

										endif;
										// echo $options['main_heading_title'];
									?>
									</h2>

									<!-- View all

									<?php if ( isset( $options['view_all_link'] ) && $options['view_all_link'] !== '' ) : ?>

										<span class="dslc-module-heading-view-all"><a href="<?php echo $options['view_all_link']; ?>" class="dslca-editable-content" data-id="main_heading_link_title" data-type="simple" <?php if ( $dslc_is_admin ) echo 'contenteditable'; ?> ><?php echo $options['main_heading_link_title']; ?></a></span>

									<?php endif; ?>-->

								<?php endif; ?>

								<!-- Filters -->

								<?php

									if ( $show_filters ) {

										$cats_array = array();

										if ( $dslc_query->have_posts() ) {

											while ( $dslc_query->have_posts() ) {

												$dslc_query->the_post();

												$post_cats = get_the_category( get_the_ID() );
												if ( ! empty( $post_cats ) ) {
													foreach( $post_cats as $post_cat ) {
														$cats_array[$post_cat->slug] = $post_cat->name;
													}
												}

											}

										}

										?>

											<div class="dslc-post-filters">

												<span class="dslc-post-filter dslc-active" data-id=" "><?php _e( 'All', 'Post Filter', 'dslc_string' ); ?></span>

												<?php foreach ( $cats_array as $cat_slug => $cat_name ) : ?>
													<span class="dslc-post-filter dslc-inactive" data-id="<?php echo $cat_slug; ?>"><?php echo $cat_name; ?></span>
												<?php endforeach; ?>

											</div><!-- .dslc-post-filters -->

										<?php

									}

								?>

								<!-- Carousel -->

								<?php if ( $show_carousel_arrows ) : ?>
									<span class="dslc-carousel-nav fr">
										<span class="dslc-carousel-nav-inner">
											<a href="#" class="dslc-carousel-nav-prev"><span class="dslc-icon-chevron-left dslc-init-center"></span></a>
											<a href="#" class="dslc-carousel-nav-next"><span class="dslc-icon-chevron-right dslc-init-center"></span></a>
										</span>
									</span><!-- .carousel-nav -->
								<?php endif; ?>

							</div><!-- .dslc-module-heading -->
						<?php

					endif;

				/**
				 * Posts ( output )
				 */

					if ( $dslc_query->have_posts() ) {

						?><div class="<?php echo $container_class; ?>"><?php

							?><div class="dslc-posts-inner"><?php

								if ( $options['type'] == 'carousel' ) :

									?><div class="dslc-loader"></div><div class="dslc-carousel" data-columns="<?php echo $carousel_items; ?>" data-pagination="<?php if ( in_array( 'circles', $carousel_elements ) ) echo 'true'; else echo 'false'; ?>"><?php

								endif;

								while ( $dslc_query->have_posts() ) : $dslc_query->the_post(); $count += $increment; $real_count++;

									if ( $count == $max_count ) {
										$count = 0;
										$extra_class = ' dslc-last-col';
									} elseif ( $count == $increment ) {
										$extra_class = ' dslc-first-col';
									} else {
										$extra_class = '';
									}

									if ( ! has_post_thumbnail() )
										$extra_class .= ' dslc-post-no-thumb';

									$post_cats = get_the_category( get_the_ID() );
									$post_cats_data = '';
									if ( ! empty( $post_cats ) ) {
										foreach( $post_cats as $post_cat ) {
											$post_cats_data .= $post_cat->slug . ' ';
										}
									}

									?>

									<div class="<?php echo $element_class . $columns_class . $extra_class; ?>" data-cats="<?php echo $post_cats_data; ?>">

										<?php if ( $post_elements == 'all' || in_array( 'thumbnail', $post_elements ) ) : ?>

											<?php
												/**
												 * Manual Resize
												 */

												$manual_resize = false;
												if ( isset( $options['thumb_resize_height'] ) && ! empty( $options['thumb_resize_height'] ) || isset( $options['thumb_resize_width_manual'] ) && ! empty( $options['thumb_resize_width_manual'] ) ) {

													$manual_resize = true;
													$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
													$thumb_url = $thumb_url[0];

													$resize_width = false;
													$resize_height = false;

													if ( isset( $options['thumb_resize_width_manual'] ) && ! empty( $options['thumb_resize_width_manual'] ) ) {
														$resize_width = $options['thumb_resize_width_manual'];
													}

													if ( isset( $options['thumb_resize_height'] ) && ! empty( $options['thumb_resize_height'] ) ) {
														$resize_height = $options['thumb_resize_height'];
													}

												}
											?>

											<div class="dslc-post-thumb dslc-cpt-post-thumb dslc-on-hover-anim">

												<div class="dslc-cpt-post-thumb-inner dslca-post-thumb">
													<?php if ( $manual_resize ) : ?>
														<a href="<?php the_permalink(); ?>"><img src="<?php $res_img = dslc_aq_resize( $thumb_url, $resize_width, $resize_height, true ); echo $res_img; ?>" /></a>
													<?php else : ?>
														<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
													<?php endif; ?>
												</div><!-- .dslc-cpt-post-thumb-inner -->

												<?php if ( ( $options['main_location'] == 'inside' || $options['main_location'] == 'inside_visible' ) && ( $post_elements == 'all' || in_array( 'title', $post_elements ) || in_array( 'meta', $post_elements ) || in_array( 'excerpt', $post_elements ) || in_array( 'button', $post_elements ) ) ) : ?>

													<div class="dslc-post-main dslc-cpt-post-main <?php if ( $options['main_location'] == 'inside_visible' ) echo 'dslc-cpt-post-main-visible'; ?> dslc-on-hover-anim-target dslc-anim-<?php echo $options['css_anim_hover']; ?>" data-dslc-anim="<?php echo $options['css_anim_hover'] ?>">

														<div class="dslc-cpt-post-main-inner dslc-init-<?php echo $options['main_position']; ?>">

															<?php if ( $post_elements == 'all' || in_array( 'title', $post_elements ) ) : ?>

																<div class="dslc-cpt-post-title">
																	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
																</div><!-- .dslc-cpt-post-title -->

															<?php endif; ?>

															<?php if ( $post_elements == 'all' || in_array( 'meta', $post_elements ) ) : ?>

																<div class="dslc-cpt-post-meta">

																	<div class="dslc-cpt-post-meta-author">
																		<?php _e( 'By', 'dslc_string'); ?> <?php the_author_posts_link(); ?>
																	</div><!-- .dslc-cpt-post-meta-author -->

																	<div class="dslc-cpt-post-meta-date">
																		<?php the_time( get_option( 'date_format' ) ); ?>
																	</div><!-- .dslc-cpt-post-meta-date -->

																</div><!-- .dslc-cpt-post-meta -->

															<?php endif; ?>

															<?php if ( $post_elements == 'all' || in_array( 'excerpt', $post_elements ) ) : ?>

																<div class="dslc-cpt-post-excerpt">
																	<?php if ( $options['excerpt_or_content'] == 'content' ) : ?>
																		<?php the_content(); ?>
																	<?php else : ?>
																		<?php echo do_shortcode( wp_trim_words( get_the_excerpt(), $options['excerpt_length'] ) ); ?>
																	<?php endif; ?>
																</div><!-- .dslc-cpt-post-excerpt -->

															<?php endif; ?>

															<?php if ( $post_elements == 'all' || in_array( 'button', $post_elements ) ) : ?>

																<div class="dslc-cpt-post-read-more">
																	<a href="<?php the_permalink(); ?>">
																		<?php if ( isset( $options['button_icon_id'] ) && $options['button_icon_id'] != '' ) : ?>
																			<span class="dslc-icon dslc-icon-<?php echo $options['button_icon_id']; ?>"></span>
																		<?php endif; ?>
																		<?php echo $options['button_text']; ?>
																	</a>
																</div><!-- .dslc-cpt-post-read-more -->

															<?php endif; ?>

														</div><!-- .dslc-cpt-post-main-inner -->

													</div><!-- .dslc-cpt-post-main -->

												<?php endif; ?>

											</div><!-- .dslc-cpt-post-thumb -->

										<?php endif; ?>

										<?php if ( $options['main_location'] == 'bellow' && ( $post_elements == 'all' || in_array( 'title', $post_elements ) || in_array( 'meta', $post_elements ) || in_array( 'excerpt', $post_elements ) || in_array( 'button', $post_elements ) ) ) : ?>

											<div class="dslc-post-main dslc-cpt-post-main">

												<?php if ( $post_elements == 'all' || in_array( 'title', $post_elements ) ) : ?>

													<div class="dslc-cpt-post-title">
														<h2><a href="<?php the_permalink(); ?>"><?php trim_title_chars(40, '...'); ?></a></h2>
													</div><!-- .dslc-cpt-post-title -->

												<?php endif; ?>

												<?php if ( $post_elements == 'all' || in_array( 'meta', $post_elements ) ) : ?>

													<div class="dslc-cpt-post-meta">

														<div class="dslc-cpt-post-meta-author">
															<?php _e( 'By', 'dslc_string'); ?> <?php the_author_posts_link(); ?>
														</div><!-- .dslc-cpt-post-meta-author -->

														<div class="dslc-cpt-post-meta-date">
															<?php the_time( get_option( 'date_format' ) ); ?>
														</div><!-- .dslc-cpt-post-meta-date -->

													</div><!-- .dslc-cpt-post-meta -->

												<?php endif; ?>

												<?php if ( $post_elements == 'all' || in_array( 'excerpt', $post_elements ) ) : ?>

													<div class="dslc-cpt-post-excerpt">
														<?php if ( $options['excerpt_or_content'] == 'content' ) : ?>
															<?php the_content(); ?>
														<?php else : ?>
															<?php
																// Lumberman edit start
																if ( post_type_supports( get_post_type(), 'excerpt' )  ) {
																	echo do_shortcode( wp_trim_words( get_the_excerpt(), $options['excerpt_length'] ) );
																} else {
																	// show 'dslc_search_content' metabox as excerpt for the content types
																	// that have no 'excerpt' feature enabled
																	echo do_shortcode( wp_trim_words( get_post_meta( get_the_ID(), 'dslc_search_content', true ), $options['excerpt_length'] ) );
																}
																// Lumberman edit end
															?>
														<?php endif; ?>
													</div><!-- .dslc-cpt-post-excerpt -->

												<?php endif; ?>

												<?php if ( $post_elements == 'all' || in_array( 'button', $post_elements ) ) : ?>

													<div class="dslc-cpt-post-read-more">
														<a href="<?php the_permalink(); ?>">
															<?php if ( isset( $options['button_icon_id'] ) && $options['button_icon_id'] != '' ) : ?>
																<span class="dslc-icon dslc-icon-<?php echo $options['button_icon_id']; ?>"></span>
															<?php endif; ?>
															<?php echo $options['button_text']; ?>
														</a>
													</div><!-- .dslc-cpt-post-read-more -->

												<?php endif; ?>

											</div><!-- .dslc-cpt-post-main -->

										<?php endif; ?>

									</div><!-- .dslc-cpt-post -->

									<?php

									// Row Separator
									if ( $options['type'] == 'grid' &&
										  $count == 0 &&
										  $real_count != $dslc_query->found_posts &&
										  $options['separator_enabled'] == 'enabled' ) {
											echo '<div class="dslc-post-separator"></div>';
									}

								endwhile;

								if ( $options['type'] == 'carousel' ) :

									?></div><?php

								endif;

							?></div><!--.dslc-posts-inner --><?php

						?></div><!-- .dslc-cpt-posts --><?php

					} else {

						if ( $dslc_is_admin ) :
							?><div class="dslc-notification dslc-red">You do not have any posts of that post type at the moment. <span class="dslca-refresh-module-hook dslc-icon dslc-icon-refresh"></span></span></div><?php
						endif;

					}

				/**
				 * Pagination
				 */

					// if ( isset( $options['pagination_type'] ) && $options['pagination_type'] == 'numbered' ) {
					// Always show pagination for archive listings
						
						$num_pages = $dslc_query->max_num_pages;
						dslc_post_pagination( array('pages' => $num_pages ) );
					// }


				wp_reset_query();

				$this->module_end( $options );

			}
		}


} //if ( defined( 'DS_LIVE_COMPOSER_URL' ) )