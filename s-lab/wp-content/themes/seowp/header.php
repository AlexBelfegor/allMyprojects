<?php
/**
 * The template for displaying the website header.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 * Outputs all head of the page including notifications and site header
 * 	– <head> section
 * 	– Warning messages for the website admin
 * 	– Notification panel
 * 	– Top Bar (menu location: 'topbar' )
 * 	– Site header with Mega Menu
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
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php
header("Last-Modified: " . date('r',strtotime($post->post_modified))); 
?>
<?php
	// Output HTML comment with template file name if LBMN_THEME_DEBUG = 1
	if ( LBMN_THEME_DEBUG ) echo '<!-- FILE: '.__FILE__.' -->';
?>

<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>

<!--Тестируем систему сбора лидов-->
<!-- CarrotQuest BEGIN -->
<!--<script type="text/javascript">
  (function(){ 
    if (typeof carrotquest === 'undefined') {
      var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; 
      s.src = '//cdn.carrotquest.io/api.min.js'; 
      var x = document.getElementsByTagName('head')[0]; x.appendChild(s);
      
      carrotquest = {}; window.carrotquestasync = []; carrotquest.settings = {};
      m = ['connect', 'track', 'identify'];
      function Build(name, args){return function(){window.carrotquestasync.push(name, arguments);} }
      for (var i = 0; i < m.length; i++) carrotquest[m[i]] = Build(m[i]);
    }
  })();
  carrotquest.connect('482-43ac97e5a5264bfa3ce1f831b59a');
</script>-->
<!-- CarrotQuest END -->
                  
<!--конец кода-->
</head>

<body <?php body_class(); ?>>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T7VZTW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T7VZTW');</script>
<!-- End Google Tag Manager -->
<?php
	/**
	* ----------------------------------------------------------------------
	* Warning messages for the website admin
	*
	* TODO: Find a way to set these notices in the WP backend
	* as TF review team doesn't like this approach
	*/
	/*
	global $wp_customize;
	// If user is admin and we are not in theme customiser
	if ( current_user_can( 'manage_options' ) && !isset( $wp_customize ) ) {

		// Show warning message that not all required plugins installed
		if(!is_plugin_active('mega_main_menu/mega_main_menu.php') ||
			!is_plugin_active('ds-live-composer/ds-live-composer.php') ||
			!is_plugin_active('meta-box/meta-box.php') ) {
			echo '<div class="message-popup required-plugin-not-istalled"><div class="message-content welcome-panel"><h3>We\'re almost there</h3><p class="about-description">There are few premium plugins that are included with our theme and need to be installed/activated to make sure everything is working properly.</p> <a href="/wp-admin/themes.php" class="button button-primary button-hero">Open plugins installation page</a></div></div>';

		// Show message if no menu set for 'header-menu' location
		} elseif ( !has_nav_menu( 'header-menu' ) ) {
			echo '<div class="message-popup no-menu-set"><div class="message-content welcome-panel"><a href="#closepopup" class="close-help-popup">&#10005;</a><h3>No main menu assigned</h3><p class="about-description">Please visit <a href="/wp-admin/nav-menus.php?action=locations">Appearance &gt; Menu Locations</a> page to assign menu <br />to "Header" location.</p> <a href="/wp-admin/nav-menus.php?action=locations" class="button">Take me there</a></div></div>';
		}
	}
	*/
?>

<div class="off-canvas-wrap">
<div class="site global-container inner-wrap" id="global-container">
	<div class="global-wrapper">
	<?php do_action( 'before' ); ?>
	<?php
		/**
		 * ----------------------------------------------------------------------
		 * Notification panel
		 */
		// disable notification panel for editing mode in Live Composer
		if ( defined('DS_LIVE_COMPOSER_ACTIVE') && ! DS_LIVE_COMPOSER_ACTIVE ) {

			// Get data from theme customizer
			$notificationpanel_switch 			= get_theme_mod( 'lbmn_notificationpanel_switch', 1 );
			// $notificationpanel_icon 			= get_theme_mod( 'lbmn_notificationpanel_icon', LBMN_NOTIFICATIONPANEL_DEFAULT );
			$notificationpanel_message 		= get_theme_mod( 'lbmn_notificationpanel_message', LBMN_NOTIFICATIONPANEL_MESSAGE_DEFAULT );
			$notificationpanel_buttonurl 		= get_theme_mod( 'lbmn_notificationpanel_buttonurl', LBMN_NOTIFICATIONPANEL_BUTTONURL_DEFAULT );


			// Generate hash to use with cookie, so visitors don't see the same mesage if they closed it once
			$npanel_unique_cookie_key = hash('md4', $notificationpanel_message.$notificationpanel_buttonurl);
			global $wp_customize;

			// empty ( $GLOBALS['wp_customize'] ) -- indicates if we are insite WP Theme Customizer @link http://goo.gl/Zj1fz
			if ( $notificationpanel_switch  || isset( $wp_customize ) ) {
				?>
				<div class='notification-panel' data-stateonload='<?php echo $notificationpanel_switch; ?>' data-uniquehash='<?php echo $npanel_unique_cookie_key;?>'>
					<span class='notification-panel__content'>
						<span class='notification-panel__message'>
							<?php
								// if ( 4 < strlen($notificationpanel_icon) ) {
								// 	echo "<i class='$notificationpanel_icon  notification-panel__icon hide-for-small'></i> ";
								// }
								echo "<span class='notification-panel__message-text'>".$notificationpanel_message."</span>&nbsp;&nbsp;&nbsp;<i class='fa-icon-angle-right notification-panel__cta-icon'></i>";
							?>
						</span>
					</span>
					<?php if ( 4 < strlen($notificationpanel_buttonurl)  ) { ?>
						<a href='<?php echo esc_url( $notificationpanel_buttonurl );?>' class='notification-panel__cta-link'></a>
					<?php } ?>
					<a href="#" class='notification-panel__close'>&#10005;</a>
				</div>
				<?php
			}

		}

		/**
		 * ----------------------------------------------------------------------
		 * Top Bar
		 * menu location 'topbar' with Mega Main Menu inside
		 */
		// disable top bar for editing mode in Live Composer
		if(is_plugin_active('mega_main_menu/mega_main_menu.php') && ( defined('DS_LIVE_COMPOSER_ACTIVE') && ! DS_LIVE_COMPOSER_ACTIVE ) ) {
			// if there is menu attached to the 'topbar' area
			if ( has_nav_menu( 'topbar' ) ) {
				// If tobar bar is enabled or in theme customizer
				if ( get_theme_mod( 'lbmn_topbar_switch', 1 ) || isset( $wp_customize ) ) {
					wp_nav_menu( array(
						'theme_location' => 'topbar',
					) );
				}
			}
		}

		// Prepare custom header classes
		$custom_header_classes = '';

		// Prepare header inner wrappers
		$header_inside_before = '';
		$header_inside_after = '';

		// Add special class if Mega Main Menu plugin is disabled
		if( !is_plugin_active('mega_main_menu/mega_main_menu.php')  || ! LBMN_THEME_CONFUGRATED || DS_LIVE_COMPOSER_ACTIVE  ) {
			$custom_header_classes .= 'mega_main_menu-disabled';
			$header_inside_before = '<div class="default-header-content">';
			$header_inside_after = '</div> <!-- default-header-content -->';
		}
	?>
	<header class="site-header <?php echo $custom_header_classes; ?>" role="banner">
	<?php
		// Show header only if LC isn't active
		if ( defined('DS_LIVE_COMPOSER_ACTIVE') && DS_LIVE_COMPOSER_ACTIVE ) {
			echo 'The header is disabled when editing the page.';
		} else {

			echo $header_inside_before;

			// Add logo if Mega Main Menu plugin is disabled
			// NOTE: normally logo displayed by Mega Main Menu
			echo lbmn_logo();

			/**
			 * ----------------------------------------------------------------------
			 * Site header with Mega Menu
			 * menu location 'header-menu' with Mega Main Menu inside
			 */

			// disable menu for editing mode in Live Composer
			if ( has_nav_menu( 'header-menu' ) ) {
				// If 'header-menu' location has a menu assigned
				wp_nav_menu( array(
					'theme_location' => 'header-menu',
					'container_class' => 'header-top-menu',
				) );
			}

			echo $header_inside_after;
		}
	?>
	</header><!-- #masthead -->
	<div class="site-main">