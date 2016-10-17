<?php
/**
 * The main template file.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This is the most generic template file in a WordPress theme
 * it is used to display a page when nothing more specific matches a query.
 *
 * Beside the main page output it this template file outputs
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
 * WP admin > Appearance > System Templates.
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

// Output header.php content
get_header();

// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
if ( LBMN_THEME_DEBUG ) echo '<!-- FILE: '.__FILE__.' -->';

if ( defined( 'DS_LIVE_COMPOSER_URL' ) && LBMN_THEME_CONFUGRATED ){
	/**
	 * ----------------------------------------------------------------------
	 * Output LiveComposer page as template for system pages
	 *
	 * User can create archive page designs (search, author, category, tag, date)
	 * the same way he creates pages using Live Composer
	 * In the back-end archive page templates is just a custom content type
	 */

	// if search results page
	if ( is_search() ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_searchresults', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_SEARCHRESULTS_DEFAULT, 'lbmn_archive' ) );

		// if no search results returned
		if ( !have_posts() ) {
			$template_post_id = get_theme_mod( 'lbmn_systempage_nosearchresults', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_NOSEARCHRESULTS_DEFAULT, 'lbmn_archive' ) );
		}
	}

	// if category, tag, date, author posts listing
	if ( is_category() ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_category', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_CATEGORY_DEFAULT, 'lbmn_archive' ) );
	} elseif ( is_tag() ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_tag', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_TAG_DEFAULT, 'lbmn_archive' ) );
	} elseif ( is_date() ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_date', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_DATE_DEFAULT, 'lbmn_archive' ) );
	} elseif ( is_author() ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_authors', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_AUTHORS_DEFAULT, 'lbmn_archive' ) );
	}

	// if 404 error page
	if ( is_404() ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_404', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_404_DEFAULT, 'lbmn_archive' ) );
	}

	// if "Front page displays" is set to "Your latest posts" in WP Settings > Reading
	if ( is_front_page() && get_option( 'page_on_front', 0 ) == 0 ) {
		$template_post_id = get_theme_mod( 'lbmn_systempage_frontpage_posts', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_FRONTPAGE_POSTS_DEFAULT, 'lbmn_archive' ) );
	}

	// if the current page is the blog home page
	if ( is_home() ) {
		// LC is not active
		if (! DS_LIVE_COMPOSER_ACTIVE ) {
			// get 'dslc_code' from the blog home page
			$template_post_id = get_option('page_for_posts');

			// if it's not available use template set for 'frontpage_posts'
			if ( !get_post_meta($template_post_id, 'dslc_code', true ) ) {
				$template_post_id = get_theme_mod( 'lbmn_systempage_frontpage_posts', lbmn_get_page_by_title( LBMN_SYSTEMPAGE_FRONTPAGE_POSTS_DEFAULT, 'lbmn_archive' ) );
			}
		// LC is active
		// TODO: do we still need it?
		} else {
			$page_id = get_option( 'page_on_front', 0 );
			$template_post_id = $page_id;
		}
	}

	// extract LC code from 'dslc_code' meta tag
	$composer_code = get_post_meta($template_post_id, 'dslc_code', true );
	$composer_content = '';


	// if composer code not empty
	if ( $composer_code ) {
		// generate the composer output
		$composer_content = do_shortcode( $composer_code );
	}

	// output generated code
	global $dslc_active;
	if ( $composer_code ) {
		if ( isset($dslc_active) ) {
			echo '<div id="dslc-content" class="dslc-content dslc-clearfix">' . do_action( 'dslc_output_prepend') . $composer_content . do_action( 'dslc_output_append') . '</div>';
		}
	}

	// posts pagination rendered by Live Composer
	$nextpostlink = get_next_posts_link();
	$previouspostlink = get_previous_posts_link();

} else {
	// LiveComposer isn't active

	// NOTE: normally all content displayed by LiveComposer
	// this code used only if Live Composer not active
	// to provide a basic theme functionality

	echo '<div id="content" class="site-content" role="main">';
	if ( have_posts() ) {
		if ( is_home() ) {
			echo '<h1 class="blog-description">' . get_bloginfo( 'description' ) . '</h1>';
		}
		/* Start the Loop */
		while ( have_posts() ) {
			the_post();
			get_template_part( 'content' ); // Request content.php file
		}

		echo lbmn_pagination();
	} else {
		// get_template_part( 'no-results', 'index' );
	}
	echo '</div><!-- #content -->';

}

// Output footer.php
get_footer();