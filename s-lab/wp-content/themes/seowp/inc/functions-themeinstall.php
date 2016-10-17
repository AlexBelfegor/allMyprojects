<?php
/**
 * Functions used on theme installation
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Our theme has advanced installation process with quick setup wizard.
 * We try to do all hard work automatically:
 * - Install bundled plugins
 * - Configure basic settings
 * 	> create system templates
 * 	> create basic menu and activate MegaMainMenu for it
 * 	> regenerate custom css
 * 	> setup LiveComposer tutorial pages
 * 	> setup default settings for bundled plugins
 * - Import demo content
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
* Perform custom fucntions on theme activation
* http://wordpress.stackexchange.com/a/80320/34582
*/

/**
* ----------------------------------------------------------------------
* Theme has been just activated
*/
// update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);

if ( is_admin() && $pagenow == "themes.php" ) {
	// Update theme option '_required_plugins_installed'
	// if URL has ?plugins=installed variable set
	if ( isset($_GET['plugins']) && $_GET['plugins'] == 'installed' ) {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?basic_setup=completed variable set
	if ( isset($_GET['basic_setup']) && $_GET['basic_setup'] == 'completed' ) {
		update_option( LBMN_THEME_NAME . '_basic_config_done', true);
		define ('LBMN_THEME_CONFUGRATED', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?demoimport=completed variable set
	if ( isset($_GET['demoimport']) && $_GET['demoimport'] == 'completed' ) {
		update_option( LBMN_THEME_NAME . '_democontent_imported', true);
	}

	if ( !get_option( LBMN_THEME_NAME . '_hide_quicksetup' ) ) {
		add_action( 'admin_notices', 'lbmn_setmessage_themeinstall' );
	}
}

/**
 * ----------------------------------------------------------------------
 * Check if required plugins were manually installed
 */

function lbmn_required_plugins_install_check() {
	if ( ! get_option( LBMN_THEME_NAME . '_required_plugins_installed' ) ) {
	// Proceed only if '_required_plugins_installed' not already market as true

		$current_tgmpa_message = '';
		$current_tgmpa_messages = get_settings_errors('tgmpa');

		foreach ($current_tgmpa_messages as $message) {
			$current_tgmpa_message = $message['message'];
		}

		// If message has no link to install-required-plugins page then all
		// required plugins has been installed
		if ( ! stristr($current_tgmpa_message, 'install-required-plugins') ) {
			// Update theme option '_required_plugins_installed'
			update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);
		}
	}
}
add_action( 'admin_footer', 'lbmn_required_plugins_install_check' );
// get_settings_errors() do not return any results earlier than 'admin_footer'


/**
 * ----------------------------------------------------------------------
 * Output Theme Installer HTML
 */

function lbmn_setmessage_themeinstall() {
?>
<img src="<?php echo includes_url() . 'images/spinner.gif' ?>" class="theme-installer-spinner" style="position:fixed; left:50%; top:50%;" />
<style type="text/css">.lumberman-message.quick-setup{display:none;}</style>
<div class="updated lumberman-message quick-setup">
	<div class="message-container">
	<p class="before-header"><?php echo LBMN_THEME_NAME_DISPLAY; ?> Quick Setup</p>
	<h4>Thank you for creating with <a href="<?php echo LBMN_DEVELOPER_URL; ?>" target="_blank"><?php echo LBMN_DEVELOPER_NAME_DISPLAY; ?></a>!</h4>
	<h5>Just a few steps left to release the full power of our theme.</h5>


	<?php
		//Check for GZIP support

		if ( !is_callable( 'gzopen' ) ) {
			echo '<span class="error">Your server doesn\'t support file compression (GZIP). Please <a href="' . LBMN_SUPPORT_URL . '">contact us</a> for alternative installation package.</span>';
		}
	?>

	<!-- Step 1 -->
		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_required_plugins_installed') ) {
				echo '<p id="theme-setup-step-1" class="submit step-plugins">';
			} else {
				echo '<p id="theme-setup-step-1" class="submit step-plugins step-completed">';
			}
		?>
		<span class="step"><span class="number">1</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

		<span class="step-body"><a href="<?php echo add_query_arg( array('page' => 'install-required-plugins'), admin_url('themes.php') ); ?>" class="button button-primary" id="do_plugins-install">Install required plugins</a>
		<?php /*<span class="step-body"><a href="<?php echo add_query_arg( array('page' => 'install-required-plugins', 'autoinstall' => '1'), admin_url('themes.php') ); ?>" class="button button-primary" id="do_plugins-install">Install required plugins</a> */ ?>
		<?php /* ajax verison <span class="step-body"><a href="#" class="button button-primary" id="do_plugins-install">Install required plugins</a> */ ?>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Installs Page Builder, Mega Menus, Slider, etc.
		</span></span><br />
		<span class="error" style="display:none">Automatic plugin installation failed. Please try to <a href="/wp-admin/themes.php?page=install-required-plugins">install required plugins manually</a>.</span>
		</p>

	<!-- Step 2 -->

		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_basic_config_done') ) {
				echo '<p id="theme-setup-step-2" class="submit step-basic_config">';
			} else {
				echo '<p id="theme-setup-step-2" class="submit step-basic_config step-completed">';
			}
		?>
		<span class="step"><span class="number">2</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body"><a href="#" class="button button-primary" id="do_basic-config" data-ajax-nonce="<?php echo wp_create_nonce( 'wie_import' ); ?>" >Integrate installed plugins</a>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Configures the plugins to work with our theme.
		</span></span><br />
		<span class="error" style="display:none">Something went wrong (<a href="#" class="show-error-log">show log</a>). Please <a href="<?php echo LBMN_SUPPORT_URL; ?>">contact us</a> for help.</span>
		</p>

	<!-- Step 3 -->

		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_democontent_imported') ) {
				echo '<p id="theme-setup-step-3" class="submit step-demoimport">';
			} else {
				echo '<p id="theme-setup-step-3" class="submit step-demoimport step-completed">';
			}
		?>
		<span class="step"><span class="number">3</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body">
		<a href="#" class="button button-primary" id="do_demo-import">Import all demo content</a>
		<span class="step-description">
		Optional step to recreate theme demo website<br />
		on your server.
		</span></span><br />
		<span class="import-progress"> <span class="progress-indicator"></span> </span>
		<!--
		<span style="margin-right:15px;">OR</span>
		<a href="#" class="button button-secondary" id="do_basic-demo-import">Create only 3 basic pages </a>
		</p>
		-->

	<!-- Step 4 -->
		<p class="submit step-tour">
		<span class="step"><span class="number">4</span></span> 
		<span class="step-body">
			<a href="<?php echo add_query_arg('theme_tour', 'true', admin_url('themes.php') ); ?>" class="button  button-primary">Take a quick tour</a> 
			<span class="step-description">2 minutes interactive introduction<br /> 
			to our theme basic controls.  </span>
		</span>
		</p>


	<p class="submit action-skip"> <a class="skip button-primary" href="<?php echo add_query_arg('hide_quicksetup', 'true', admin_url('themes.php') ); ?>">Hide this message</a></p></div>
</div>
<style type="text/css">.theme-installer-spinner{display:none;}</style>
<style type="text/css">.lumberman-message.quick-setup{display:block;}</style>
<?php
}

/**
* ----------------------------------------------------------------------
* Start basic theme settings setup process
*/
add_action( 'admin_notices', 'pvt_wordpress_content_importer' );
function pvt_wordpress_content_importer() {
	$theme_dir = get_template_directory();

	if ( is_admin() && isset($_GET['importcontent']) ) {


		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				include $class_wp_importer;
			}
		}
		if ( ! class_exists('pvt_WP_Import') ) {
			$class_wp_import = $theme_dir . '/inc/importer/wordpress-importer.php';
			if ( file_exists( $class_wp_import ) ) {
				include $class_wp_import;
			}
		}
		if ( class_exists( 'WP_Importer' ) && class_exists( 'pvt_WP_Import' ) ) {
			$importer = new pvt_WP_Import();
			$files_to_import = array();

			// Live Composer has links to images hard-coded, so before importing
			// media we need to check that the Settings > Media >
			// 'Organize my uploads into month- and year-based folders' unchecked
			// as on demo server. After import is done we set back original state
			// of this setting.
			$setting_original_useyearmonthfolders = get_option( 'uploads_use_yearmonth_folders');
			update_option( 'uploads_use_yearmonth_folders', 0 );

			if ( $_GET['importcontent'] == 'basic-templates' ) {
				$import_path = $theme_dir . '/design/basic-config/';
				$files_to_import[] = $import_path . 'seowp-templates.xml.gz';
				$files_to_import[] = $import_path . 'seowp-themefooters.xml.gz';
				$files_to_import[] = $import_path . 'seowp-systempagetemplates.xml.gz';
				$files_to_import[] = $import_path . 'seowp-livecomposer-tutorials.xml.gz';
			}

			if ( $_GET['importcontent'] == 'alldemocontent' ) {
				$import_path = $theme_dir . '/design/demo-content/';

				$files_array = array(
					array(
						'seowp-homepages.xml.gz',
						'seowp-predesignedpages-1.xml.gz',
					),
					array(
						'seowp-predesignedpages-2.xml.gz',
						'seowp-predesignedpages-3.xml.gz',
					),
					array(
						'seowp-predesignedpages-4.xml.gz',
						'seowp-predesignedpages-5.xml.gz',
					),
					array(
						'seowp-predesignedpages-6.xml.gz',
						'seowp-predesignedpages-7.xml.gz',
					),
					array(
						'seowp-downloads.xml.gz',
						'seowp-partners.xml.gz',
					),
					array(
						'seowp-staff.xml.gz',
						'seowp-testimonials.xml.gz',
					),
					array(
						'seowp-posts.xml.gz',
						'seowp-projects.xml.gz',
					),
					array( // 0
						'seowp-media-homepage.xml.gz',
					),
					array(
						'seowp-media-menuimages.xml.gz',
					),
					array(
						'seowp-media-sliderimages.xml.gz',
					),
					array(
						'seowp-media-clientlogos.xml.gz',
					),
					array(
						'seowp-media-blogpostthumbs.xml.gz',
					),
					array(
						'seowp-media-footerimages.xml.gz',
					),
					array( // 6 !!
						'seowp-media-staffavatars.xml.gz',
					),
					array(
						'seowp-media-servicepage.xml.gz',
					),
					array(
						'seowp-media-sectionbackgrounds.xml.gz',
					),
					array(
						'seowp-media-ebookcovers.xml.gz',
					),
					array(
						'seowp-media-projectthumbs.xml.gz',
					),
				);

				if(isset($_GET['importcontent_part'])){
					foreach($files_array[$_GET['importcontent_part']] as $file_name){
						$files_to_import[] = $import_path . $file_name;
					}
					if(isset($files_array[($_GET['importcontent_part']+1)]))
						echo '<input type="hidden" name="importcontent_part" id="importcontent_part" value="'.($_GET['importcontent_part']+1).'" />';
				}
			}

			// Start Import

			if ( file_exists( $class_wp_importer ) ) {
				// Import included images
				$importer->fetch_attachments = true;

				foreach ($files_to_import as $import_file) {
					if( is_file($import_file) ) {
						ob_start();
							$importer->import( $import_file );

							$log = ob_get_contents();
						ob_end_clean();

						// output log in the hidden div
						echo '<div class="ajax-log">';
						echo $log;
						echo '</div>';


						if ( stristr($log, 'error') || !stristr($log, 'All done.') ) {
							// Set marker div that will be fildered by ajax request
							echo '<div class="ajax-request-error"></div>';

							// output log in the div
							echo '<div class="ajax-error-log">';
							echo $log;
							echo '</div>';
						}

					} else {
						// Set marker div that will be fildered by ajax request
						echo '<div class="ajax-request-error"></div>';

						// output log in the div
						echo '<div class="ajax-error-log">';
						echo "Can't open file: " . $import_file . "</ br>";
						echo '</div>';
					}
				}

			} else {
				// Set marker div that will be fildered by ajax request
				echo '<div class="ajax-request-error"></div>';

				// output log in the div
				echo '<div class="ajax-error-log">';
				echo "Failed to load: " . $class_wp_import . "</ br>";
				echo '</div>';
			}

			// Set 'Organize my uploads into month- and year-based folders' setting
			// to its original state
			update_option( 'uploads_use_yearmonth_folders', $setting_original_useyearmonthfolders );

		}

		/**
		 * ----------------------------------------------------------------------
		 * Basic configuration:
		 * Post import actions
		 */

		if ( $_GET['importcontent'] == 'basic-templates' ) {

			// 1. Import Menus
			// 2. Activate Mega Main Menu for menu locations
			// 3. Import Widgets
			// 4. Demo description for author
			// 5. Tutorial Pages for LiveComposer
			// 6. Newsletter Sign-Up Plugin Settings
			// 7. Rotating Tweets Default Options Setup
			// 8. Regenerate Custom CSS

			// Path to the folder with basic import files
			$import_path_basic_config = $theme_dir . '/design/basic-config/';

			// 1:
			// Import Top Bar menu
			// if no menu set for 'topbar' location
			if( !has_nav_menu('topbar') ) {
				if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
					wpfw_import_menu($import_path_basic_config . 'seowp-menu-topbar.txt', 'topbar');
				}
			}

			// Import Mega Main Menu menu
			// if no menu set for 'header-menu' location
			if( !has_nav_menu('header-menu') ) {
				if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
					wpfw_import_menu($import_path_basic_config . 'seowp-menu-megamainmenu.txt', 'header-menu');
				}
			}

			$locations = get_nav_menu_locations();
			set_theme_mod('nav_menu_locations', $locations);

			// Import Mobile Off-Canvas Menu
			if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
				wpfw_import_menu($import_path_basic_config . 'seowp-menu-mobile-offcanvas.txt');
			}

			// 2: Activate Mega Main Menu for 'topbar' and 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				lbmn_activate_mainmegamenu_locations ();
			}

			// Predefine Custom Sidebars in LiveComposer
			// First set new sidebars in options table
			update_option(
				'dslc_plugin_options_widgets_m',
				array(
					'sidebars' => 'Sidebar,404 Page Widgets,Comment Form Area',
				)
			);
			// Then run LiveComposer function that creates sidebars dynamically
			dslc_sidebars();

			// 3: Import widgets
			$files_with_widgets_to_import = array();
			$files_with_widgets_to_import[] = $import_path_basic_config . 'seowp-widgets.wie';

			// Remove default widgets from 'mobile-offcanvas' widget area
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			if (is_array($sidebars_widgets['mobile-offcanvas'])) {
				$sidebars_widgets['mobile-offcanvas'] = NULL;
			}
			update_option( 'sidebars_widgets', $sidebars_widgets );

			// There are dynamic values in 'seowp-widgets.wie' that needs to be replaced
			// before import processing
			global $widget_strings_replace;
			$widget_strings_replace = array(
				'TOREPLACE_OFFCANVAS_MENUID' => lbmn_get_menuid_by_menutitle ( 'Mobile Off-canvas Menu' ),
			);

			foreach ($files_with_widgets_to_import as $file) {
				pvt_import_data( $file );
			}

			// 4: Put some demo description into current user info field
			// that used in the blog user boxes
			$user_ID = get_current_user_id();
			$user_info = get_userdata( $user_ID );

			if ( !$user_info->description ) {
				update_user_meta(
					$user_ID,
					'description',
					'This is author biographical info, ' .
					'that can be used to tell more about you, your iterests, ' .
					'background and experience. ' .
					'You can change it on <a href="/wp-admin/profile.php">Admin &gt; Users &gt; Your Profile &gt; Biographical Info</a> page."'
				);
			}

			// 5: Predefine Tutorial Pages in LiveComposer
			update_option(
				'dslc_plugin_options_tuts',
				array(
					'lc_tut_chapter_one' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-1'),
					'lc_tut_chapter_two' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-2'),
					'lc_tut_chapter_three' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-3'),
					'lc_tut_chapter_four' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-4'),
				)
			);

			// 6: Newsletter Sign-Up Plugin Form Elements
			update_option(
				'nsu_form',
				array(
					'email_label' => '',
					'email_default_value' => 'Your email address...',
					'email_label' => '',
					'redirect_to' => get_site_url() . '/index.php?pagename=/lbmn_archive/thanks-for-signing-up/',
				)
			);

			// 7: Rotating Tweets Default Options Setup
			update_option(
				'rotatingtweets-api-settings',
				array(
					'key' => 'mxutw2QpMFxuXvW0puvEqKwuL',
					'secret' => 'xqmBL6MWYDkt2yjkbKe0VTlqdkMvsePkif6Z1zzqYFrpguEor4',
					'token' => '95497077-RlX1hhwHC1uz8NcmCCIdbbUm9zqH3wB4Wb44gvaTM',
					'token_secret' => '7LwXbdxJM1i32SUijrMteRNENysr09GfBiEp6RYKPpHRD',
					'cache_delay' => 86400,
					'js_in_footer' => 1,

				)
			);

			// Add custom Mega Main Menu options
			$mmm_options = get_option( 'mega_main_menu_options' );

			// Add custom Additional Mega Menu styles
			$mmm_options['additional_styles_presets'] = array(
				'1' => array(
							'style_name' => "Call to action item",
							'text_color' => "rgba(255,255,255,1)",
							'font' => array(
														"font_size" => "15",
														"font_weight" => "600",
													),
							'icon' => array(
														"font_size" => "16",
													),
							'bg_gradient' => array(
														"color1" => "#A1C627",
														"start" => "0",
														"color2" => "#A1C627",
														"end" => "100",
														"orientation" => "top",
													),
							"text_color_hover" => "rgba(255,255,255,1)",
							"bg_gradient_hover" => array(
														"color1" => "#56AEE3",
														"start" => "0",
														"color2" => "#56AEE3",
														"end" => "100",
														"orientation" => "top",
													),
						 ),
				'2' => array(
							'style_name' => "Dropdown Heading",
							'text_color' => "rgba(0,0,0,1)",
							'font' => array(
														"font_size" => "15",
														"font_weight" => "400",
													),
							'bg_gradient' => array(
														"color1" => "",
														"start" => "0",
														"color2" => "",
														"end" => "100",
														"orientation" => "top",
													),
							"text_color_hover" => "rgba(0,0,0,1)",
							"bg_gradient_hover" => array(
														"color1" => "",
														"start" => "0",
														"color2" => "",
														"end" => "100",
														"orientation" => "top",
													),
						 ),
			);

			// Add custom icons
			$mmm_options['set_of_custom_icons'] = array(
				'1' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-spain.png'),
						 ),
				'2' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-italy.png'),
						 ),
				'3' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-france.png'),
						 ),
				'4' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-uk.png'),
						 ),
				'5' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-us.png'),
						 ),
				'6' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-austria.png'),
						 ),
				'7' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-belgium.png'),
						 ),
				'8' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-germany.png'),
						 ),
				'9' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-netherlands.png'),
						 ),
				'10' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-poland.png'),
						 ),
				'11' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-portugal.png'),
						 ),
				'12' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-romania.png'),
						 ),
				'13' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-russia.png'),
						 ),
				'14' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-ukraine.png'),
						 ),
			);

			// Put Mega Main Menu options back
			update_option( 'mega_main_menu_options', $mmm_options );

			// 8: Regenerate Custom CSS
			lbmn_customized_css_cache_reset(false); // refresh custom css without printig css (false)

			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				// call the function that normaly starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}
		} // if $_GET['importcontent']


		/**
		 * ----------------------------------------------------------------------
		 * Demo Content: Full
		 */

		if ( ( $_GET['importcontent'] == 'alldemocontent' ) && ( $_GET['importcontent_part'] == 16 ) ) {
			$import_path_demo_content = $theme_dir . '/design/demo-content/';

			// Import Demo Mega Menu menu
			if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
				wpfw_import_menu($import_path_demo_content . 'seowp-demomegamenu.txt', 'header-menu');
			}

			$locations = get_nav_menu_locations();
			set_theme_mod('nav_menu_locations', $locations);

			// Activate Mega Main Menu for 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				lbmn_activate_mainmegamenu_locations ();
			}

			// Import Nex-Forms plugin data
			if(is_plugin_active('nex-forms/main.php')){
				lbmn_debug_console( 'Import Nex-Forms plugin data' );
				global $wpdb;

				// Import 'Contact Us' form
				$wpdb->insert(
					$wpdb->prefix . 'wap_nex_forms',
					array(
						'Id' => 1,  // TODO: last form id in the db + 1
						'plugin' => 'shared',
						'publish' => 0,
						'added' => '0000-00-00 00:00',
						'last_update' => '2014-07-06 22:18:40',
						'title' => 'Contact Us',
						'description' => NULL,
						'mail_to' => 'info@example.com',
						'confirmation_mail_body' => 'Thank you for connecting with us. We will respond to you shortly.',
						'confirmation_mail_subject' => 'Thank you for connecting with us',
						'from_address' => 'info@example.com',
						'from_name' => 'WP Website',
						'on_screen_confirmation_message' => 'Thank you for connecting with us.',
						'confirmation_page' => '',
						'form_fields' =>	 '<div class=\"run_animation hidden\" style=\"\">false</div><div class=\"animation_time hidden\" style=\"\">60</div><div class=\"form_field grid grid-system ui-draggable dropped\" style=\"display: block;\">'.
						'<div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\">2 Cols  <i class=\"label label-success\">6 6</i></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><div class=\"row\"><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable drag\"><div class=\"form_field text ui-draggable dropped required text_only\" style=\"display: block;\" id=\"_48449\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Name</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\" style=\"display: none;\"><label id=\"title\" class=\"title ve_title\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon btn-xs glyphicon-star\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label style_bold\" style=\"color: rgb(0, 0, 0);\">Name</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\">Sub text</small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" name=\"_name\" placeholder=\"Name\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control required text_only input-lg\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Only text are allowed\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\" type=\"text\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div><div class=\"form_field text ui-draggable required email dropped\" style=\"display: block;\" id=\"_37971\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Email</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\" style=\"display: none;\"><label id=\"title\" class=\"title ve_title\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label style_bold\" style=\"color: rgb(0, 0, 0);\">Email</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\">Sub text</small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" name=\"email\" placeholder=\"Email\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control required input-lg email\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Invalid e-mail format\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\" type=\"text\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable drag\"><div class=\"form_field text ui-draggable dropped\" style=\"display: block;\" id=\"_67643\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Company</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\" style=\"display: none;\"><label id=\"title\" class=\"title ve_title\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs hidden\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label style_bold\" style=\"color: rgb(0, 0, 0);\">Company</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\">Sub text</small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" name=\"company\" placeholder=\"Company\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control input-lg\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Only text are allowed\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\" type=\"text\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div><div class=\"form_field text ui-draggable phone_number dropped\" style=\"display: block;\" id=\"_26453\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Phone</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\" style=\"display: none;\"><label id=\"title\" class=\"title ve_title\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs hidden\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label style_bold\" style=\"color: rgb(0, 0, 0);\">Phone</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\">Sub text</small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" name=\"phone\" placeholder=\"Phone\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control input-lg phone_number\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Invalid phone number format\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\" type=\"text\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div><div class=\"form_field textarea ui-draggable required dropped\" style=\"display: block; position: relative; z-index: 100;\" id=\"_41512\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-align-justify\"></i>&nbsp;&nbsp;<span class=\"field_title\">Message</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row\"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\" style=\"display: none;\"><label id=\"title\" class=\"title ve_title\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Message</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\">Sub text</small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><textarea name=\"message\" id=\"textarea\" placeholder=\"Message\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" class=\"error_message svg_ready the_input_element textarea pre-format form-control input-lg required\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" title=\"\" style=\"color: rgb(85, 85, 85); border-color: rgb(220, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\"></textarea><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div><div class=\"form_field grid grid-system ui-draggable dropped\" style=\"display: block;\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\">2 Cols  <i class=\"label label-success\">6 6</i></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><div class=\"row\"><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable\"><div class=\"form_field submit-button ui-draggable dropped\" style=\"display: block;\" id=\"_55759\"><div class=\"draggable_object input-group \" style=\"display: none;\"><button class=\"btn btn-success btn-sm form-control\"><i class=\" glyphicon glyphicon glyphicon-send\"></i>&nbsp;&nbsp;<span class=\"field_title\">Submit Button</span></button><span class=\"input-group-addon\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><button class=\"nex-submit svg_ready the_input_element btn btn-primary input-lg\" style=\"color: rgb(255, 255, 255); border-color: rgb(86, 172, 227); outline-offset: 0px; outline-width: 1px; background: rgb(86, 172, 227);\" data-onfocus-color=\"#000000\" data-original-title=\"\" title=\"\">Submit</button><br></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable\"><div class=\"form_field check-group ui-draggable dropped\" style=\"display: block;\" id=\"_57100\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-check\"></i>&nbsp;&nbsp;<span class=\"field_title\">Newsletter subscribe</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder radio-group no-pre-suffix\"><div class=\"row\"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\" style=\"display: none;\"><label id=\"title\" class=\"title ve_title\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon btn-xs hidden glyphicon-star\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Newsletter subscribe</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\">Sub text</small></label></div>'.
						'<div class=\"the-radios error_message col-sm-12\" id=\"the-radios\" data-checked-color=\"alert-success\" data-checked-class=\"fa-check\" data-unchecked-class=\"\" data-placement=\"bottom\" data-content=\"Please select one\" title=\"\" data-original-title=\"\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><label class=\"checkbox-inline\" for=\"subscribe_me_to_the_the_newsletter\" data-svg=\"demo-input-1\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"svg_ready has-pretty-child\"><div class=\"clearfix prettycheckbox labelright  blue has-pretty-child\"><input class=\"check the_input_element\" name=\"newsletter_subscribe[]\" id=\"subscribe_me_to_the_the_newsletter\" value=\"Subscribe me to the the newsletter\" style=\"display: none; color: rgb(85, 85, 85); border-color: rgb(187, 187, 187); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-onfocus-color=\"#000000\" type=\"checkbox\"><a class=\"\" data-original-title=\"\" title=\"\" style=\"outline-offset: 0px; outline-width: 1px;\"></a></div><span class=\"input-label check-label\">Subscribe me to the the newsletter</span></span></label></div><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div>'.
						'<div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div>',
						'visual_settings' => NULL,
						'google_analytics_conversion_code' => NULL,
						'colour_scheme' => NULL,
						'send_user_mail' => NULL,
						'user_email_field' => '',
						'on_form_submission' => 'message',
						'date_sent' => NULL,
						'is_form' => '1',
						'is_template' => '1',
					)
				);

				// Import 'Free analysis request' form
				$wpdb->insert(
					$wpdb->prefix . 'wap_nex_forms',
					array(
						'Id' => 2,  // TODO: last form id in the db + 2
						'plugin' => 'shared',
						'publish' => 0,
						'added' => '0000-00-00 00:00',
						'last_update' => '2014-07-06 22:18:40',
						'title' => 'Free analysis request',
						'description' => NULL,
						'mail_to' => 'info@example.com',
						'confirmation_mail_body' => 'Thank you for connecting with us. We will respond to you shortly.',
						'confirmation_mail_subject' => 'Thank you for connecting with us',
						'from_address' => 'info@example.com',
						'from_name' => 'WP Website',
						'on_screen_confirmation_message' => 'Thank you for connecting with us.',
						'confirmation_page' => '',
						'form_fields' => '<div class=\"run_animation hidden\" style=\"\">false</div><div class=\"animation_time hidden\" style=\"\">60</div><div class=\"form_field text ui-draggable dropped required url\" style=\"display: block; position: relative; z-index: 100;\" id=\"_61848\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Website</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\"><label id=\"title\" class=\"title ve_title align_left input-lg\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon btn-xs glyphicon-star\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Website</span><br><small class=\"sub-text\" style=\"color: rgb(216, 216, 216);\">URL to review</small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" type=\"text\" name=\"website\" placeholder=\"\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control input-lg required url\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Invalid url format\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\"><span class=\"help-block hidden\" style=\"color: rgb(115, 115, 115); outline-offset: 0px; outline-width: 1px;\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div><div class=\"form_field grid grid-system ui-draggable dropped\" style=\"display: block; position: relative; z-index: 100;\" id=\"_48105\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\">2 Cols  <i class=\"label label-success\">6 6</i></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><div class=\"row\"><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable drag\"><div class=\"form_field text ui-draggable dropped required\" style=\"display: block;\" id=\"_48822\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Name</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\"><label id=\"title\" class=\"title ve_title input-lg\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon btn-xs glyphicon-star\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Name</span><br><small class=\"sub-text\" style=\"color: rgb(216, 216, 216);\"></small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" type=\"text\" name=\"_name\" placeholder=\"\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control required input-lg\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\"><span class=\"help-block hidden\" style=\"color: rgb(115, 115, 115); outline-offset: 0px; outline-width: 1px;\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable drag\"><div class=\"form_field text ui-draggable dropped email required\" style=\"display: block;\" id=\"_55206\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Email</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\"><label id=\"title\" class=\"title ve_title input-lg\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Email</span><br><small class=\"sub-text\" style=\"color: rgb(216, 216, 216);\"></small></label></div><div class=\"col-sm-12\">'.
						'<div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" type=\"text\" name=\"email\" placeholder=\"\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control input-lg email required\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Invalid e-mail format\" title=\"\" style=\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div><div class=\"form_field grid grid-system ui-draggable dropped\" style=\"display: block; position: relative; z-index: 100;\" id=\"_91512\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\">2 Cols  <i class=\"label label-success\">6 6</i></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><div class=\"row\"><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable\"><div class=\"form_field text ui-draggable dropped phone_number\" style=\"display: block;\" id=\"_39150\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Phone</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\"><label id=\"title\" class=\"title ve_title input-lg\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs hidden\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Phone</span><br><small class=\"sub-text\" style=\"color: rgb(216, 216, 216);\"></small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" type=\"text\" name=\"phone\" placeholder=\"\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control input-lg phone_number\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"Invalid phone number format\" title=\"\" style=\"color: rgb(51, 51, 51); outline-offset: 0px; outline-width: 1px; border-color: rgb(221, 221, 221); background: rgb(255, 255, 255);\" data-original-title=\"\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable\"><div class=\"form_field text ui-draggable dropped\" style=\"display: block;\" id=\"_97690\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-minus\"></i>&nbsp;&nbsp;<span class=\"field_title\">Company</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\"><label id=\"title\" class=\"title ve_title input-lg align_left\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs hidden\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Company</span><br><small class=\"sub-text\" style=\"color: rgb(216, 216, 216);\"></small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><input id=\"ve_text\" type=\"text\" name=\"company\" placeholder=\"\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" maxlength=\"200\" class=\"error_message svg_ready the_input_element text pre-format form-control input-lg\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" data-secondary-message=\"\" title=\"\" style=\"color: rgb(51, 51, 51); outline-offset: 0px; outline-width: 1px; border-color: rgb(221, 221, 221); background: rgb(255, 255, 255);\" data-original-title=\"\"><span class=\"help-block hidden\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div><div class=\"form_field textarea ui-draggable dropped\" style=\"display: block; position: relative; z-index: 100;\" id=\"_96266\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\" glyphicon glyphicon-align-justify\"></i>&nbsp;&nbsp;<span class=\"field_title\">Details</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row\"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-2 label_container\"><label id=\"title\" class=\"title ve_title input-lg\" style=\"outline-offset: 0px; outline-width: 1px;\"><span class=\"is_required glyphicon glyphicon-star btn-xs hidden\" style=\"color: rgb(0, 0, 0);\"></span><span class=\"the_label\" style=\"color: rgb(0, 0, 0);\">Details</span><br><small class=\"sub-text style_italic\" style=\"color: rgb(153, 153, 153);\"></small></label></div><div class=\"col-sm-12\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><textarea name=\"details\" id=\"textarea\" placeholder=\"Ideas, special requirements, etc.\" data-maxlength-color=\"label label-success\" data-maxlength-position=\"bottom\" data-maxlength-show=\"false\" data-default-value=\"\" class=\"error_message svg_ready the_input_element textarea pre-format form-control\" data-onfocus-color=\"#66afe9\" data-drop-focus-swadow=\"1\" data-placement=\"bottom\" data-content=\"Please enter a value\" title=\"\" style=\"color: rgb(85, 85, 85); border-color: rgb(204, 204, 204); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\" data-original-title=\"\"></textarea><span class=\"help-block hidden\" style=\"color: rgb(115, 115, 115); outline-offset: 0px; outline-width: 1px;\">Help text...</span></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div><div class=\"form_field grid grid-system ui-draggable dropped\" style=\"display: block; position: relative; z-index: 100;\" id=\"_52745\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\">2 Cols  <i class=\"label label-success\">6 6</i></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input-inner\" data-svg=\"demo-input-1\"><div class=\"row\"><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable\"><div class=\"form_field submit-button ui-draggable dropped\" style=\"display: block;\" id=\"_55853\"><div class=\"draggable_object input-group \" style=\"display: none;\"><button class=\"btn btn-success btn-sm form-control\"><i class=\" glyphicon glyphicon glyphicon-send\"></i>&nbsp;&nbsp;<span class=\"field_title\">Submit Button</span></button><span class=\"input-group-addon\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder no-pre-suffix\"><div class=\"row \"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-12\"><div class=\"input-inner align_left\" data-svg=\"demo-input-1\"><button class=\"nex-submit svg_ready the_input_element btn btn-primary input-lg align_left\" style=\"color: rgb(255, 255, 255); border-color: rgb(86, 172, 227); outline-offset: 0px; outline-width: 1px; background: rgb(86, 172, 227);\" data-onfocus-color=\"#000000\" value=\"Hear from an expert\" data-default-value=\"Hear from an expert\" data-original-title=\"\" title=\"\">Hear from an expert</button><br></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div><div class=\"input_holder col-sm-6\"><div class=\"panel grid-system panel-default\"><div class=\"panel-body ui-droppable ui-sortable\"><div class=\"form_field paragraph ui-draggable dropped\" style=\"display: block;\" id=\"_83290\"><div class=\"draggable_object input-group\" style=\"display: none;\"><button class=\"btn btn-default btn-sm form-control\"><i class=\"fa fa-align-justify\"></i>&nbsp;&nbsp;<span class=\"field_title\">Paragraph</span></button><span class=\"input-group-addon btn-default\"><i class=\" glyphicon glyphicon-move\"></i></span></div><div id=\"form_object\" class=\"form_object\" style=\"\"><div class=\"input_holder\"><div class=\"input-inner svg_ready\" data-svg=\"demo-input-1\"><div class=\"row\"><div class=\"col-sm-12\" id=\"field_container\"><div class=\"row\"><div class=\"col-sm-12\"><div class=\"input-group date svg_ready\"><p class=\"the_input_element\" style=\"outline-offset: 0px; outline-width: 1px; color: rgb(170, 170, 170); border-color: rgb(47, 111, 143); background: rgb(255, 255, 255);\" data-onfocus-color=\"#000000\">Please let us know in the \"Details\" filed the best time to contact you.</p><div style=\"clear:both;\"></div></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-info btn-sm edit \" title=\"Edit Field Attributes\"><i class=\"glyphicon glyphicon-pencil\"></i></div><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div></div></div></div></div></div></div><div class=\"field_settings bs-callout bs-callout-info\" style=\"display: none;\"><div class=\"btn btn-danger btn-sm delete \" title=\"Delete field\"><i class=\"glyphicon glyphicon-remove\"></i></div></div></div></div></div>',
						'visual_settings' => NULL,
						'google_analytics_conversion_code' => NULL,
						'colour_scheme' => NULL,
						'send_user_mail' => NULL,
						'user_email_field' => '',
						'on_form_submission' => 'message',
						'date_sent' => NULL,
						'is_form' => '1',
						'is_template' => '1',
					)
				);

			}

			lbmn_debug_console( 'ESSB Easy Social Share Buttons' );
			// ESSB Easy Social Share Buttons
			// Setup 'Social Fans Counter' settings
			if(is_plugin_active('easy-social-share-buttons/easy-social-share-buttons.php')){
				update_option(
					'essb-fans-options',
					array(
						'social' => array(
							'facebook' => array(
									'id' => 'envato',
									'text' => 'Fans',
									),
							'twitter' => array(
									'id' => '@lumbermandesign',
									'text' => 'Followers',
									'key' => 'mxutw2QpMFxuXvW0puvEqKwuL',
									'secret' => 'xqmBL6MWYDkt2yjkbKe0VTlqdkMvsePkif6Z1zzqYFrpguEor4',
									'token' => '95497077-RlX1hhwHC1uz8NcmCCIdbbUm9zqH3wB4Wb44gvaTM',
									'tokensecret' => '7LwXbdxJM1i32SUijrMteRNENysr09GfBiEp6RYKPpHRD',
									),
							'google' => array(
									'id' => '+themeforest',
									'text' => 'Followers',
									'type' => 'Page',
									'counter_type' => 'circledByCount+plusOneCount',
									),
							'youtube' => array(
								'id' => 'UCJr72fY4cTaNZv7WPbvjaSw',
								'text' => 'Subscribers',
								'type' => 'User',
								),
							'dribbble' => array(
								'id' => 'lumbermandesigns',
								'text' => 'Followers',
								),
							'pinterest' => array(
								'id' => 'vladesigner',
								'text' => 'Followers',
								),
						),
						'cache' => 24,
						'data' => array(
								'facebook' => 47742,
								'twitter' => 656,
								'google' => '3676',
								'youtube' => 1898,
								'dribbble' => 5,
								'pinterest' => '9',
						),
						'sort' => array(
								0 => 'facebook',
								1 => 'twitter',
								2 => 'google',
								3 => 'youtube',
								4 => 'dribbble',
								5 => 'pinterest',
						),

					)
				);
			}

			lbmn_debug_console( 'Import pre-defined Maps' );
			// Import pre-defined Maps
			// Check if Maps plugin is active
			if(is_plugin_active('sb-google-map/sb-google-map.php')){
				global $wpdb;

				$wpdb->insert(
					$wpdb->prefix . 'sb_google_map',
					array(
						'map_id' => 1, // TODO: last map id in the db + 1
						'map_title' => 'Map on Contact Us page',
						'markers' => 'a:1:{i:0;a:8:{s:7:"address";s:25:"Cambridge, United Kingdom";s:8:"latitude";d:52.205337;s:9:"longitude";d:0.12181699999999999;s:21:"textfordirectionslink";s:0:"";s:4:"icon";s:6:"blue-1";s:9:"animation";s:4:"DROP";s:10:"infowindow";s:2:"no";s:7:"content";s:53:"Visit us for a warm talk and a cup of great espresso!";}}',
						'width_height' => 'a:4:{s:5:"width";s:3:"100";s:9:"widthtype";s:1:"%";s:6:"height";s:3:"360";s:10:"heighttype";s:2:"px";}',
						'map_styles' => 'style-1',
						'zoom_settings' => 'a:7:{s:4:"zoom";s:2:"14";s:11:"zoomcontrol";s:3:"yes";s:20:"zoomcontrol_position";s:8:"TOP_LEFT";s:16:"zoomcontrolstyle";s:7:"DEFAULT";s:9:"draggable";s:3:"yes";s:11:"scrollwheel";s:2:"no";s:11:"centerpoint";a:3:{s:7:"address";s:0:"";s:8:"latitude";i:0;s:9:"longitude";i:0;}}',
						'map_controls' => 'a:11:{s:10:"pancontrol";s:3:"yes";s:19:"pancontrol_position";s:8:"TOP_LEFT";s:12:"scalecontrol";s:3:"yes";s:17:"streetviewcontrol";s:3:"yes";s:26:"streetviewcontrol_position";s:8:"TOP_LEFT";s:14:"maptypecontrol";s:3:"yes";s:23:"maptypecontrol_position";s:9:"TOP_RIGHT";s:7:"maptype";s:7:"ROADMAP";s:19:"maptypecontrolstyle";s:7:"DEFAULT";s:25:"overviewmapcontrolvisible";s:2:"no";s:18:"overviewmapcontrol";s:3:"yes";}',
						'nearest_places' => 'a:4:{s:10:"searchtype";s:8:"disabled";s:12:"searchradius";s:3:"500";s:19:"searchiconanimation";s:4:"NONE";s:19:"searchdirectiontext";s:15:"view directions";}',
						'map_layers' => 'a:5:{s:7:"weather";s:2:"no";s:7:"traffic";s:2:"no";s:7:"transit";s:2:"no";s:7:"bicycle";s:2:"no";s:9:"panoramio";s:2:"no";}',
						'miscellaneous' => 'a:2:{s:14:"reloadonresize";s:2:"no";s:8:"language";s:2:"en";}',
					)
				);





				$wpdb->insert(
					$wpdb->prefix . 'sb_google_map',
					array(
						'map_id' => 2, // TODO: last map id in the db + 1
						'map_title' => 'Map on Contact Us page (second address)',
						'markers' => 'a:1:{i:0;a:8:{s:7:"address";s:19:"Toronto, ON, Canada";s:8:"latitude";d:43.653225999999997;s:9:"longitude";d:-79.383184299999996;s:21:"textfordirectionslink";s:0:"";s:4:"icon";s:7:"green-1";s:9:"animation";s:4:"DROP";s:10:"infowindow";s:2:"no";s:7:"content";s:30:"Come to our office in Toronto!";}}',
						'width_height' => 'a:4:{s:5:"width";s:3:"100";s:9:"widthtype";s:1:"%";s:6:"height";s:3:"360";s:10:"heighttype";s:2:"px";}',
						'map_styles' => 'style-1',
						'zoom_settings' => 'a:7:{s:4:"zoom";s:2:"13";s:11:"zoomcontrol";s:3:"yes";s:20:"zoomcontrol_position";s:8:"TOP_LEFT";s:16:"zoomcontrolstyle";s:7:"DEFAULT";s:9:"draggable";s:3:"yes";s:11:"scrollwheel";s:2:"no";s:11:"centerpoint";a:3:{s:7:"address";s:0:"";s:8:"latitude";i:0;s:9:"longitude";i:0;}}',
						'map_controls' => 'a:11:{s:10:"pancontrol";s:3:"yes";s:19:"pancontrol_position";s:8:"TOP_LEFT";s:12:"scalecontrol";s:3:"yes";s:17:"streetviewcontrol";s:3:"yes";s:26:"streetviewcontrol_position";s:8:"TOP_LEFT";s:14:"maptypecontrol";s:3:"yes";s:23:"maptypecontrol_position";s:9:"TOP_RIGHT";s:7:"maptype";s:7:"ROADMAP";s:19:"maptypecontrolstyle";s:7:"DEFAULT";s:25:"overviewmapcontrolvisible";s:2:"no";s:18:"overviewmapcontrol";s:3:"yes";}',
						'nearest_places' => 'a:4:{s:10:"searchtype";s:8:"disabled";s:12:"searchradius";s:3:"500";s:19:"searchiconanimation";s:4:"NONE";s:19:"searchdirectiontext";s:15:"view directions";}',
						'map_layers' => 'a:5:{s:7:"weather";s:2:"no";s:7:"traffic";s:2:"no";s:7:"transit";s:2:"no";s:7:"bicycle";s:2:"no";s:9:"panoramio";s:2:"no";}',
						'miscellaneous' => 'a:2:{s:14:"reloadonresize";s:2:"no";s:8:"language";s:2:"en";}',
					)
				);

			}


			// Import pre-designed MasterSlider Slides
			// Check if MasterSlider is active

			// http://support.averta.net/envato/support/ticket/regenerate-custom-css-programatically/#post-16478
			if ( defined('MSWP_AVERTA_VERSION') ) {
				global $wpdb;

				$wpdb->insert(
					$wpdb->prefix . 'masterslider_sliders',
					array(
						'ID' => 1, // TODO: last slider id in the db + 1
						'title' => 'SEO Agency Flat',
						'type' => 'custom',
						'slides_num' => 4,
						'date_created' => '2014-06-25 12:53:53',
						'date_modified' => '2014-06-30 19:16:35',
						'params' => 
						"eyJtZXRhIjp7IlNldHRpbmdzIWlkcyI6IjEiLCJTZXR0aW5ncyFuZXh0SWQiOjIsIlNsaWRlIWlkcyI6IjEsNCw1IiwiU2xpZGUhbmV4dElkIjo2LCJTdHlsZSFpZHMiOiIzLDQsNSw3LDEzLDIxLDIyLDIzLDM0LDM4LDM5LDQ3LDQ4LDQ5LDUxLDU1LDU2LDU3LDU4LDU5LDYwLDYxLDYyLDYzLDY0LDY1LDc5LDgwLDgxLDgyLDgzLDg0LDg1LDg2LDg3LDg4LDg5LDkwIiwiU3R5bGUhbmV4dElkIjo5MSwiRWZmZWN0IWlkcyI6IjUsNiw3LDgsOSwxMCwxMywxNCwyNSwyNiw0MSw0Miw0Myw0NCw0NSw0Niw2Nyw2OCw3NSw3Niw3Nyw3OCw5Myw5NCw5NSw5Niw5Nyw5OCwxMDEsMTAyLDEwOSwxMTAsMTExLDExMiwxMTMsMTE0LDExNSwxMTYsMTE3LDExOCwxMTksMTIwLDEyMSwxMjIsMTIzLDEyNCwxMjUsMTI2LDEyNywxMjgsMTI5LDEzMCwxNTcsMTU4LDE1OSwxNjAsMTYxLDE2MiwxNjMsMTY0LDE2NSwxNjYsMTY3LDE2OCwxNjksMTcwLDE3MSwxNzIsMTczLDE3NCwxNzUsMTc2LDE3NywxNzgsMTc5LDE4MCIsIkVmZmVjdCFuZXh0SWQiOjE4MSwiTGF5ZXIhaWRzIjoiMyw0LDUsNywxMywyMSwyMiwyMywzNCwzOCwzOSw0Nyw0OCw0OSw1MSw1NSw1Niw1Nyw1OCw1OSw2MCw2MSw2Miw2Myw2NCw2NSw3OSw4MCw4MSw4Miw4Myw4NCw4NSw4Niw4Nyw4OCw4OSw5MCIsIkxheWVyIW5leHRJZCI6OTEsIkNvbnRyb2whaWRzIjoiMSwzIiwiQ29udHJvbCFuZXh0SWQiOjZ9LCJNU1BhbmVsLlNldHRpbmdzIjp7IjEiOiJ7XCJpZFwiOlwiMVwiLFwic25hcHBpbmdcIjp0cnVlLFwibmFtZVwiOlwiRmxhdCBEZXNpZ24gU3R5bGVcIixcIndpZHRoXCI6MTIwMCxcImhlaWdodFwiOjM2MCxcIndyYXBwZXJXaWR0aFwiOjEwMCxcIndyYXBwZXJXaWR0aFVuaXRcIjpcIiVcIixcImF1dG9Dcm9wXCI6ZmFsc2UsXCJ0eXBlXCI6XCJjdXN0b21cIixcInNsaWRlcklkXCI6XCIxXCIsXCJsYXlvdXRcIjpcImZ1bGx3aWR0aFwiLFwiYXV0b0hlaWdodFwiOmZhbHNlLFwidHJWaWV3XCI6XCJmYWRlXCIsXCJzcGVlZFwiOjUsXCJzcGFjZVwiOjAsXCJzdGFydFwiOjEsXCJncmFiQ3Vyc29yXCI6dHJ1ZSxcInN3aXBlXCI6dHJ1ZSxcIm1vdXNlXCI6dHJ1ZSxcIndoZWVsXCI6ZmFsc2UsXCJhdXRvcGxheVwiOnRydWUsXCJsb29wXCI6dHJ1ZSxcInNodWZmbGVcIjpmYWxzZSxcInByZWxvYWRcIjoxLFwib3ZlclBhdXNlXCI6ZmFsc2UsXCJlbmRQYXVzZVwiOmZhbHNlLFwiaGlkZUxheWVyc1wiOmZhbHNlLFwiZGlyXCI6XCJ2XCIsXCJwYXJhbGxheE1vZGVcIjpcIm9mZlwiLFwidXNlRGVlcExpbmtcIjpmYWxzZSxcImRlZXBMaW5rXCI6XCJtcy0xXCIsXCJkZWVwTGlua1R5cGVcIjpcInBhdGhcIixcInNjcm9sbFBhcmFsbGF4TW92ZVwiOjMwLFwic2Nyb2xsUGFyYWxsYXhCR01vdmVcIjo1MCxcInNjcm9sbFBhcmFsbGF4RmFkZVwiOnRydWUsXCJjZW50ZXJDb250cm9sc1wiOnRydWUsXCJpbnN0YW50U2hvd0xheWVyc1wiOmZhbHNlLFwiY2xhc3NOYW1lXCI6XCJcIixcInNraW5cIjpcIm1zLXNraW4tZGVmYXVsdFwiLFwibXNUZW1wbGF0ZVwiOlwiY3VzdG9tXCIsXCJtc1RlbXBsYXRlQ2xhc3NcIjpcIlwiLFwidXNlZEZvbnRzXCI6XCJcIn0ifSwiTVNQYW5lbC5TbGlkZSI6eyIxIjoie1wiaWRcIjpcIjFcIixcInRpbWVsaW5lX2hcIjozOTYsXCJvcmRlclwiOjAsXCJkdXJhdGlvblwiOjksXCJmaWxsTW9kZVwiOlwiZmlsbFwiLFwiYmdDb2xvclwiOlwiIzA4OGFkYVwiLFwiYmd2X2ZpbGxtb2RlXCI6XCJmaWxsXCIsXCJiZ3ZfbG9vcFwiOnRydWUsXCJiZ3ZfbXV0ZVwiOnRydWUsXCJiZ3ZfYXV0b3BhdXNlXCI6ZmFsc2UsXCJsYXllcl9pZHNcIjpbMyw0LDUsNywxMyw0Nyw0OCw0OSw2MSw2Miw2M119IiwiNCI6IntcImlkXCI6NCxcInRpbWVsaW5lX2hcIjo0MTIsXCJvcmRlclwiOjEsXCJkdXJhdGlvblwiOjksXCJmaWxsTW9kZVwiOlwiZmlsbFwiLFwiYmdDb2xvclwiOlwiIzVhYTUxN1wiLFwiYmd2X2ZpbGxtb2RlXCI6XCJmaWxsXCIsXCJiZ3ZfbG9vcFwiOnRydWUsXCJiZ3ZfbXV0ZVwiOnRydWUsXCJiZ3ZfYXV0b3BhdXNlXCI6ZmFsc2UsXCJsYXllcl9pZHNcIjpbMjEsMjIsMjMsMzQsMzgsMzksNTEsNTUsNTYsNTcsNTgsNTksNjBdfSIsIjUiOiJ7XCJpZFwiOjUsXCJ0aW1lbGluZV9oXCI6Mzk2LFwib3JkZXJcIjoyLFwiZHVyYXRpb25cIjo5LFwiZmlsbE1vZGVcIjpcImZpbGxcIixcImJnQ29sb3JcIjpcIiNhZDNjZGNcIixcImJndl9maWxsbW9kZVwiOlwiZmlsbFwiLFwiYmd2X2xvb3BcIjp0cnVlLFwiYmd2X211dGVcIjp0cnVlLFwiYmd2X2F1dG9wYXVzZVwiOmZhbHNlLFwibGF5ZXJfaWRzXCI6WzY0LDY1LDc5LDgwLDgxLDgyLDgzLDg0LDg1LDg2LDg3LDg4LDg5LDkwXX0ifSwiTVNQYW5lbC5TdHlsZSI6eyIzIjoie1wiaWRcIjozLFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTExXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6MjAsXCJsZXR0ZXJTcGFjaW5nXCI6MCxcImxpbmVIZWlnaHRcIjpcIjMwcHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcIlwifSIsIjQiOiJ7XCJpZFwiOjQsXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtOVwiLFwiZm9udFdlaWdodFwiOlwiMzAwXCIsXCJmb250U2l6ZVwiOjQ4LFwibGV0dGVyU3BhY2luZ1wiOi0xLFwibGluZUhlaWdodFwiOlwiNDhweFwiLFwiY29sb3JcIjpcIiNmZmZmZmZcIixcImN1c3RvbVwiOlwiZm9udC13ZWlnaHQ6MjAwO1wifSIsIjUiOiJ7XCJpZFwiOjUsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjciOiJ7XCJpZFwiOjcsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjEzIjoie1wiaWRcIjoxMyxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiMjEiOiJ7XCJpZFwiOjIxLFwidHlwZVwiOlwiY3VzdG9tXCIsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi0xLTIxXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6MjAsXCJsZXR0ZXJTcGFjaW5nXCI6MCxcImxpbmVIZWlnaHRcIjpcIjMwcHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcIlwifSIsIjIyIjoie1wiaWRcIjoyMixcInR5cGVcIjpcImN1c3RvbVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtY24tMS0yMlwiLFwiZm9udFdlaWdodFwiOlwiMzAwXCIsXCJmb250U2l6ZVwiOjQ4LFwibGV0dGVyU3BhY2luZ1wiOi0xLFwibGluZUhlaWdodFwiOlwiNDhweFwiLFwiY29sb3JcIjpcIiNmZmZmZmZcIixcImN1c3RvbVwiOlwiZm9udC13ZWlnaHQ6MjAwO1wifSIsIjIzIjoie1wiaWRcIjoyMyxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiMzQiOiJ7XCJpZFwiOjM0LFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJiYWNrZ3JvdW5kQ29sb3JcIjpudWxsLFwicGFkZGluZ1RvcFwiOjQsXCJwYWRkaW5nUmlnaHRcIjo2LFwicGFkZGluZ0JvdHRvbVwiOjQsXCJwYWRkaW5nTGVmdFwiOjYsXCJib3JkZXJUb3BcIjoyLFwiYm9yZGVyUmlnaHRcIjoyLFwiYm9yZGVyQm90dG9tXCI6MixcImJvcmRlckxlZnRcIjoyLFwiYm9yZGVyQ29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC40NilcIixcImJvcmRlclJhZGl1c1wiOjQsXCJib3JkZXJTdHlsZVwiOlwic29saWRcIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCIzOCI6IntcImlkXCI6MzgsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjM5Ijoie1wiaWRcIjozOSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNDciOiJ7XCJpZFwiOjQ3LFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJiYWNrZ3JvdW5kQ29sb3JcIjpudWxsLFwicGFkZGluZ1RvcFwiOjQsXCJwYWRkaW5nUmlnaHRcIjo2LFwicGFkZGluZ0JvdHRvbVwiOjQsXCJwYWRkaW5nTGVmdFwiOjYsXCJib3JkZXJUb3BcIjoyLFwiYm9yZGVyUmlnaHRcIjoyLFwiYm9yZGVyQm90dG9tXCI6MixcImJvcmRlckxlZnRcIjoyLFwiYm9yZGVyQ29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC40NilcIixcImJvcmRlclJhZGl1c1wiOjQsXCJib3JkZXJTdHlsZVwiOlwic29saWRcIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCI0OCI6IntcImlkXCI6NDgsXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTNcIixcImJhY2tncm91bmRDb2xvclwiOm51bGwsXCJwYWRkaW5nVG9wXCI6NCxcInBhZGRpbmdSaWdodFwiOjYsXCJwYWRkaW5nQm90dG9tXCI6NCxcInBhZGRpbmdMZWZ0XCI6NixcImJvcmRlclRvcFwiOjIsXCJib3JkZXJSaWdodFwiOjIsXCJib3JkZXJCb3R0b21cIjoyLFwiYm9yZGVyTGVmdFwiOjIsXCJib3JkZXJDb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjQ2KVwiLFwiYm9yZGVyUmFkaXVzXCI6NCxcImJvcmRlclN0eWxlXCI6XCJzb2xpZFwiLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJmb250U2l6ZVwiOjEyLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCIsXCJjb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjYpXCIsXCJjdXN0b21cIjpcIlwifSIsIjQ5Ijoie1wiaWRcIjo0OSxcInR5cGVcIjpcImNvcHlcIixcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC0xM1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImZvbnRTaXplXCI6MTIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIixcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNilcIixcImN1c3RvbVwiOlwiXCJ9IiwiNTEiOiJ7XCJpZFwiOjUxLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI1NSI6IntcImlkXCI6NTUsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjU2Ijoie1wiaWRcIjo1NixcInR5cGVcIjpcImNvcHlcIixcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC0xM1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImZvbnRTaXplXCI6MTIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIixcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNilcIixcImN1c3RvbVwiOlwiXCJ9IiwiNTciOiJ7XCJpZFwiOjU3LFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJiYWNrZ3JvdW5kQ29sb3JcIjpudWxsLFwicGFkZGluZ1RvcFwiOjQsXCJwYWRkaW5nUmlnaHRcIjo2LFwicGFkZGluZ0JvdHRvbVwiOjQsXCJwYWRkaW5nTGVmdFwiOjYsXCJib3JkZXJUb3BcIjoyLFwiYm9yZGVyUmlnaHRcIjoyLFwiYm9yZGVyQm90dG9tXCI6MixcImJvcmRlckxlZnRcIjoyLFwiYm9yZGVyQ29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC40NilcIixcImJvcmRlclJhZGl1c1wiOjQsXCJib3JkZXJTdHlsZVwiOlwic29saWRcIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCI1OCI6IntcImlkXCI6NTgsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjU5Ijoie1wiaWRcIjo1OSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNjAiOiJ7XCJpZFwiOjYwLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI2MSI6IntcImlkXCI6NjEsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjYyIjoie1wiaWRcIjo2MixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNjMiOiJ7XCJpZFwiOjYzLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI2NCI6IntcImlkXCI6NjQsXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTJcIixcImZvbnRXZWln" .
						"aHRcIjpcIjMwMFwiLFwiZm9udFNpemVcIjoyMCxcInRleHRBbGlnblwiOlwiY2VudGVyXCIsXCJsZXR0ZXJTcGFjaW5nXCI6MCxcImxpbmVIZWlnaHRcIjpcIjMwcHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcIlwifSIsIjY1Ijoie1wiaWRcIjo2NSxcInR5cGVcIjpcImN1c3RvbVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtY24tMS02NVwiLFwiZm9udFdlaWdodFwiOlwiMzAwXCIsXCJmb250U2l6ZVwiOjQ4LFwidGV4dEFsaWduXCI6XCJjZW50ZXJcIixcImxldHRlclNwYWNpbmdcIjotMSxcImxpbmVIZWlnaHRcIjpcIjQ4cHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcImZvbnQtd2VpZ2h0OjIwMDtcIn0iLCI3OSI6IntcImlkXCI6NzksXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTNcIixcImJhY2tncm91bmRDb2xvclwiOm51bGwsXCJwYWRkaW5nVG9wXCI6NCxcInBhZGRpbmdSaWdodFwiOjYsXCJwYWRkaW5nQm90dG9tXCI6NCxcInBhZGRpbmdMZWZ0XCI6NixcImJvcmRlclRvcFwiOjIsXCJib3JkZXJSaWdodFwiOjIsXCJib3JkZXJCb3R0b21cIjoyLFwiYm9yZGVyTGVmdFwiOjIsXCJib3JkZXJDb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjQ2KVwiLFwiYm9yZGVyUmFkaXVzXCI6NCxcImJvcmRlclN0eWxlXCI6XCJzb2xpZFwiLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJmb250U2l6ZVwiOjEyLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCIsXCJjb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjYpXCIsXCJjdXN0b21cIjpcIlwifSIsIjgwIjoie1wiaWRcIjo4MCxcInR5cGVcIjpcImNvcHlcIixcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC0xM1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImZvbnRTaXplXCI6MTIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIixcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNilcIixcImN1c3RvbVwiOlwiXCJ9IiwiODEiOiJ7XCJpZFwiOjgxLFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJiYWNrZ3JvdW5kQ29sb3JcIjpudWxsLFwicGFkZGluZ1RvcFwiOjQsXCJwYWRkaW5nUmlnaHRcIjo2LFwicGFkZGluZ0JvdHRvbVwiOjQsXCJwYWRkaW5nTGVmdFwiOjYsXCJib3JkZXJUb3BcIjoyLFwiYm9yZGVyUmlnaHRcIjoyLFwiYm9yZGVyQm90dG9tXCI6MixcImJvcmRlckxlZnRcIjoyLFwiYm9yZGVyQ29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC40NilcIixcImJvcmRlclJhZGl1c1wiOjQsXCJib3JkZXJTdHlsZVwiOlwic29saWRcIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCI4MiI6IntcImlkXCI6ODIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjgzIjoie1wiaWRcIjo4MyxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiODQiOiJ7XCJpZFwiOjg0LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI4NSI6IntcImlkXCI6ODUsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjg2Ijoie1wiaWRcIjo4NixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiODciOiJ7XCJpZFwiOjg3LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI4OCI6IntcImlkXCI6ODgsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjg5Ijoie1wiaWRcIjo4OSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiOTAiOiJ7XCJpZFwiOjkwLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0ifSwiTVNQYW5lbC5FZmZlY3QiOnsiNSI6IntcImlkXCI6NSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCI2Ijoie1wiaWRcIjo2LFwiZmFkZVwiOnRydWV9IiwiNyI6IntcImlkXCI6NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCI4Ijoie1wiaWRcIjo4LFwiZmFkZVwiOnRydWV9IiwiOSI6IntcImlkXCI6OSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCIxMCI6IntcImlkXCI6MTAsXCJmYWRlXCI6dHJ1ZX0iLCIxMyI6IntcImlkXCI6MTMsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxNTB9IiwiMTQiOiJ7XCJpZFwiOjE0LFwiZmFkZVwiOnRydWV9IiwiMjUiOiJ7XCJpZFwiOjI1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MjUwLFwic2tld1lcIjoyNX0iLCIyNiI6IntcImlkXCI6MjYsXCJmYWRlXCI6dHJ1ZX0iLCI0MSI6IntcImlkXCI6NDEsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxNTB9IiwiNDIiOiJ7XCJpZFwiOjQyLFwiZmFkZVwiOnRydWV9IiwiNDMiOiJ7XCJpZFwiOjQzLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTUwfSIsIjQ0Ijoie1wiaWRcIjo0NCxcImZhZGVcIjp0cnVlfSIsIjQ1Ijoie1wiaWRcIjo0NSxcImZhZGVcIjp0cnVlLFwicm90YXRlWVwiOjkwfSIsIjQ2Ijoie1wiaWRcIjo0NixcImZhZGVcIjp0cnVlfSIsIjY3Ijoie1wiaWRcIjo2NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjY4Ijoie1wiaWRcIjo2OCxcImZhZGVcIjp0cnVlfSIsIjc1Ijoie1wiaWRcIjo3NSxcImZhZGVcIjp0cnVlLFwicm90YXRlWVwiOjkwfSIsIjc2Ijoie1wiaWRcIjo3NixcImZhZGVcIjp0cnVlfSIsIjc3Ijoie1wiaWRcIjo3NyxcImZhZGVcIjp0cnVlLFwicm90YXRlWVwiOjkwfSIsIjc4Ijoie1wiaWRcIjo3OCxcImZhZGVcIjp0cnVlfSIsIjkzIjoie1wiaWRcIjo5MyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjk0Ijoie1wiaWRcIjo5NCxcImZhZGVcIjp0cnVlfSIsIjk1Ijoie1wiaWRcIjo5NSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjk2Ijoie1wiaWRcIjo5NixcImZhZGVcIjp0cnVlfSIsIjk3Ijoie1wiaWRcIjo5NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjk4Ijoie1wiaWRcIjo5OCxcImZhZGVcIjp0cnVlfSIsIjEwMSI6IntcImlkXCI6MTAxLFwiZmFkZVwiOmZhbHNlLFwidHJhbnNsYXRlWVwiOjUwMH0iLCIxMDIiOiJ7XCJpZFwiOjEwMixcImZhZGVcIjp0cnVlfSIsIjEwOSI6IntcImlkXCI6MTA5LFwiZmFkZVwiOnRydWUsXCJzY2FsZVhcIjowLFwic2NhbGVZXCI6MH0iLCIxMTAiOiJ7XCJpZFwiOjExMCxcImZhZGVcIjp0cnVlLFwic2NhbGVYXCI6MCxcInNjYWxlWVwiOjB9IiwiMTExIjoie1wiaWRcIjoxMTEsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjo0MH0iLCIxMTIiOiJ7XCJpZFwiOjExMixcImZhZGVcIjp0cnVlfSIsIjExMyI6IntcImlkXCI6MTEzLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NDB9IiwiMTE0Ijoie1wiaWRcIjoxMTQsXCJmYWRlXCI6dHJ1ZX0iLCIxMTUiOiJ7XCJpZFwiOjExNSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6MjV9IiwiMTE2Ijoie1wiaWRcIjoxMTYsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTAwLFwicm90YXRlWlwiOi0yNX0iLCIxMTciOiJ7XCJpZFwiOjExNyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjEwMCxcInJvdGF0ZVpcIjoyNX0iLCIxMTgiOiJ7XCJpZFwiOjExOCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjExOSI6IntcImlkXCI6MTE5LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTEwMCxcInJvdGF0ZVpcIjotMzV9IiwiMTIwIjoie1wiaWRcIjoxMjAsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTAwLFwicm90YXRlWlwiOi0yNX0iLCIxMjEiOiJ7XCJpZFwiOjEyMSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjEyMiI6IntcImlkXCI6MTIyLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTE1MCxcInJvdGF0ZVpcIjotMjV9IiwiMTIzIjoie1wiaWRcIjoxMjMsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxMDAsXCJyb3RhdGVaXCI6MjV9IiwiMTI0Ijoie1wiaWRcIjoxMjQsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTUwLFwicm90YXRlWlwiOi0yNX0iLCIxMjUiOiJ7XCJpZFwiOjEyNSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjEyNiI6IntcImlkXCI6MTI2LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTE1MCxcInJvdGF0ZVpcIjotMjV9IiwiMTI3Ijoie1wiaWRcIjoxMjcsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxNTB9IiwiMTI4Ijoie1wiaWRcIjoxMjgsXCJmYWRlXCI6dHJ1ZX0iLCIxMjkiOiJ7XCJpZFwiOjEyOSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCIxMzAiOiJ7XCJpZFwiOjEzMCxcImZhZGVcIjp0cnVlfSIsIjE1NyI6IntcImlkXCI6MTU3LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NDB9IiwiMTU4Ijoie1wiaWRcIjoxNTgsXCJmYWRlXCI6dHJ1ZX0iLCIxNTkiOiJ7XCJpZFwiOjE1OSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjE2MCI6IntcImlkXCI6MTYwLFwiZmFkZVwiOnRydWV9IiwiMTYxIjoie1wiaWRcIjoxNjEsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjo0MH0iLCIxNjIiOiJ7XCJpZFwiOjE2MixcImZhZGVcIjp0cnVlfSIsIjE2MyI6IntcImlkXCI6MTYzLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTAwLFwicm90YXRlWlwiOi0yNX0iLCIxNjQiOiJ7XCJpZFwiOjE2NCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xNTAsXCJyb3RhdGVaXCI6LTI1fSIsIjE2NSI6IntcImlkXCI6MTY1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTAwLFwicm90YXRlWlwiOjI1fSIsIjE2NiI6IntcImlkXCI6MTY2LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTE1MCxcInJvdGF0ZVpcIjotMjV9IiwiMTY3Ijoie1wiaWRcIjoxNjcsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTAwLFwicm90YXRlWlwiOi0yNX0iLCIxNjgiOiJ7XCJpZFwiOjE2OCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xNTAsXCJyb3RhdGVaXCI6LTI1fSIsIjE2OSI6IntcImlkXCI6MTY5LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTEzMCxcInNjYWxlWFwiOjAuNSxcInNjYWxlWVwiOjAuNX0iLCIxNzAiOiJ7XCJpZFwiOjE3MCxcImZhZGVcIjp0cnVlfSIsIjE3MSI6IntcImlkXCI6MTcxLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTEzMCxcInRyYW5zbGF0ZVlcIjotODAsXCJzY2FsZVhcIjowLjUsXCJzY2FsZVlcIjowLjV9IiwiMTcyIjoie1wiaWRcIjoxNzIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMTMwLFwidHJhbnNsYXRlWVwiOi04MCxcInNjYWxlWFwiOjEuNSxcInNjYWxlWVwiOjEuNX0iLCIxNzMiOiJ7XCJpZFwiOjE3MyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOjEzMCxcInNjYWxlWFwiOjAuNSxcInNjYWxlWVwiOjAuNX0iLCIxNzQiOiJ7XCJpZFwiOjE3NCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOi0xNzAsXCJzY2FsZVhcIjoxLjUsXCJzY2FsZVlcIjoxLjV9IiwiMTc1Ijoie1wiaWRcIjoxNzUsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjoxMzAsXCJ0cmFuc2xhdGVZXCI6LTgwLFwic2NhbGVYXCI6MC41LFwic2NhbGVZXCI6MC41fSIsIjE3NiI6IntcImlkXCI6MTc2LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTEzMCxcInRyYW5zbGF0ZVlcIjotODAsXCJzY2FsZVhcIjoxLjUsXCJzY2FsZVlcIjoxLjV9IiwiMTc3Ijoie1wiaWRcIjoxNzcsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjoxMzAsXCJ0cmFuc2xhdGVZXCI6ODAsXCJzY2FsZVhcIjowLjUsXCJzY2FsZVlcIjowLjV9IiwiMTc4Ijoie1wiaWRcIjoxNzgsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMTMwLFwidHJhbnNsYXRlWVwiOi04MCxcInNjYWxlWFwiOjEuNSxcInNjYWxlWVwiOjEuNX0iLCIxNzkiOiJ7XCJpZFwiOjE3OSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOi0xMzAsXCJ0cmFuc2xhdGVZXCI6ODAsXCJzY2FsZVhcIjowLjUsXCJzY2FsZVlcIjowLjV9IiwiMTgwIjoie1wiaWRcIjoxODAsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMTMwLFwidHJhbnNsYXRlWVwiOi04MCxcInNjYWxlWFwiOjEuNSxcInNjYWxlWVwiOjEuNX0ifSwiTVNQYW5lbC5MYXllciI6eyIzIjoie1wiaWRcIjozLFwibmFtZVwiOlwiU2Vjb25kYXJ5IFRleHRcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZ" .
						"KDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjUsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJEb25lYyBzZWQgb2RpbyBkdWkuIEZ1c2NlIGRhcGlidXMsIHRlbGx1cyBhYyBjdXJzdXMgY29tbW9kbywgdG9ydG9yIG1hdXJpcyBjb25kaW1lbnR1bSBuaWJoLCB1dCBmZXJtZW50dW0gbWFzc2EganVzdG8uXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6MjAwLFwid2lkdGhcIjo1MDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTFcIixcInNob3dEdXJhdGlvblwiOjEuMjg3NSxcInNob3dEZWxheVwiOjAuMixcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6MyxcInNob3dFZmZlY3RcIjo1LFwiaGlkZUVmZmVjdFwiOjZ9IiwiNCI6IntcImlkXCI6NCxcIm5hbWVcIjpcIkhlYWRpbmdcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjYsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJHdWFyYW50ZWVkIGluY3JlYXNlIG9mwqB5b3VyIHdlYnNpdGUgc2FsZXNcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwidXNlQWN0aW9uXCI6ZmFsc2UsXCJzY3JvbGxEdXJhdGlvblwiOjIsXCJvZmZzZXRYXCI6MCxcIm9mZnNldFlcIjo3NSxcIndpZHRoXCI6NTAwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTlcIixcInNob3dEdXJhdGlvblwiOjEuMjg3NSxcInNob3dEZWxheVwiOjAsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sMTUwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjQsXCJzaG93RWZmZWN0XCI6NyxcImhpZGVFZmZlY3RcIjo4fSIsIjUiOiJ7XCJpZFwiOjUsXCJuYW1lXCI6XCJNYWNCb29rXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgxNTBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24tbWFjYm9vay0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjQsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLW1hY2Jvb2sucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoyOTksXCJvZmZzZXRZXCI6MjgsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYnJcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjYyNSxcInNob3dEZWxheVwiOjAuMzc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDE1MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjo1LFwic2hvd0VmZmVjdFwiOjksXCJoaWRlRWZmZWN0XCI6MTB9IiwiNyI6IntcImlkXCI6NyxcIm5hbWVcIjpcImlNYWNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9mYWx0LWlsbHVzdHJhdGlvbi1pbWFjLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MyxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmFsdC1pbGx1c3RyYXRpb24taW1hYy5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjQ5LFwib2Zmc2V0WVwiOjI5LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcImJyXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MS44ODc1LFwic2hvd0RlbGF5XCI6MC43LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDE1MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjo3LFwic2hvd0VmZmVjdFwiOjEzLFwiaGlkZUVmZmVjdFwiOjE0fSIsIjEzIjoie1wiaWRcIjoxMyxcIm5hbWVcIjpcImdyYXBoXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgyNTBweCkgc2tld1koMjVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LWdyYXBoLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NyxcInR5cGVcIjpcImltYWdlX" .
						"CIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtZ3JhcGgucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjExLFwib2Zmc2V0WVwiOi0yOSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJiclwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNjI1LFwic2hvd0RlbGF5XCI6MS4yLFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDI1MCxuLG4sbixuLG4sbixuLG4sMjUsbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6MTMsXCJzaG93RWZmZWN0XCI6MjUsXCJoaWRlRWZmZWN0XCI6MjZ9IiwiMjEiOiJ7XCJpZFwiOjIxLFwibmFtZVwiOlwiU2Vjb25kYXJ5IFRleHRcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjYsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJEb25lYyBzZWQgb2RpbyBkdWkuIEZ1c2NlIGRhcGlidXMsIHRlbGx1cyBhYyBjdXJzdXMgY29tbW9kbywgdG9ydG9yIG1hdXJpcyBjb25kaW1lbnR1bSBuaWJoLCB1dCBmZXJtZW50dW0gbWFzc2EganVzdG8uXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6MjAwLFwid2lkdGhcIjo1MDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidHJcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi0xLTIxXCIsXCJzaG93RHVyYXRpb25cIjoxLjI4NzUsXCJzaG93RGVsYXlcIjowLjE1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDE1MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiYnRuQ2xhc3NcIjpcIm1zLWJ0biBtcy1kZWZhdWx0LWJ0blwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjIxLFwic2hvd0VmZmVjdFwiOjQxLFwiaGlkZUVmZmVjdFwiOjQyfSIsIjIyIjoie1wiaWRcIjoyMixcIm5hbWVcIjpcIkhlYWRpbmdcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjcsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJNb2JpbGUtT3JpZW50ZWTCoFxcblBQQ8KgQ2FtcGFpZ25zXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6NzUsXCJ3aWR0aFwiOjUwMCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0clwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcImNsYXNzTmFtZVwiOlwibXNwLWNuLTEtMjJcIixcInNob3dEdXJhdGlvblwiOjEuMjg3NSxcInNob3dEZWxheVwiOjAsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sMTUwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOm51bGwsXCJzdHlsZU1vZGVsXCI6MjIsXCJzaG93RWZmZWN0XCI6NDMsXCJoaWRlRWZmZWN0XCI6NDR9IiwiMjMiOiJ7XCJpZFwiOjIzLFwibmFtZVwiOlwiaXBhZFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZVkoOTBkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWlwYWQtMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo1LFwidHlwZVwiOlwiaW1hZ2VcIixcIm5vU3dpcGVcIjpmYWxzZSxcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1pcGFkLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJ1c2VBY3Rpb25cIjpmYWxzZSxcInNjcm9sbER1cmF0aW9uXCI6MixcIm9mZnNldFhcIjozOTAsXCJvZmZzZXRZXCI6NTUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYmxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjA1LFwic2hvd0RlbGF5XCI6MS42Mzc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbixuLG4sOTAsbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOm51bGwsXCJzdHlsZU1vZGVsXCI6MjMsXCJzaG93RWZmZWN0XCI6NDUsXCJoaWRlRWZmZWN0XCI6NDZ9IiwiMzQiOiJ7XCJpZFwiOjM0LFwibmFtZVwiOlwiTGFiZWwgM1wiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxMCxcInR5cGVcIjpcInRleHRcIixcIm5vU3dpcGVcIjpmYWxzZSxcImNvbnRlbnRcIjpcIlNNTVwiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJ1c2VBY3Rpb25cIjpmYWxzZSxcInNjcm9sbER1cmF0aW9uXCI6MixcIm9mZnNldFhcIjoyODksXCJvZmZzZXRZXCI6NTQsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTNcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjozLjE3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiYnRuQ2xhc3NcIjpcIm1zLWJ0biBtcy1kZWZhdWx0LWJ0blwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjM0LFwic2hvd0VmZmVjdFwiOjY3LFwiaGlkZUVmZmVjdFwiOjY4fSIsIjM4Ijoie1wiaWRcIjozOCxcIm5hbWVcIjpcImlwaG9uZVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZVkoOTBkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWlwaG9uZS05M3gxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MyxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24taXBob25lLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6OTksXCJvZmZzZXRZXCI6NTUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYmxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjA1LFwic2hvd0RlbGF5XCI6MS4xMjUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sbixuLG4sbiw5MCxuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjozOCxcInNob3dFZmZlY3RcIjo3NSxcImhpZGVFZmZlY3RcIjo3Nn0iLCIzOSI6IntcImlkXCI6MzksXCJuYW1lXCI6XCJpcGFkbWluaVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZVkoOTBkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWlwYWRtaW5pLTEyN3gxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NCxcInR5cGVcIjpcImltYWdlXCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24taXBhZG1pbmkucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjIyOCxcIm9mZnNldFlcIjo1NSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJibFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuMDUsXCJzaG93RGVsYXlcIjoxLjM3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbixuLG4sbixuLDkwLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiYnRuQ2xhc3NcIjpcIm1zLWJ0biBtcy1kZWZhdWx0LWJ0blwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjM5LFwic2hvd0VmZmVjdFwiOjc3LFwiaGlkZUVmZmVjdFwiOjc4fSIsIjQ3Ijoie1wiaWRcIjo0NyxcIm5hbWVcIjpcIkxhYmVsIDNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDQwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNld" .
						"FhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTAsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJDUk9cIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwidXNlQWN0aW9uXCI6ZmFsc2UsXCJzY3JvbGxEdXJhdGlvblwiOjIsXCJvZmZzZXRYXCI6NjgzLFwib2Zmc2V0WVwiOjgzLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJzaG93RHVyYXRpb25cIjoxLFwic2hvd0RlbGF5XCI6My4yMzc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDQwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjQ3LFwic2hvd0VmZmVjdFwiOjkzLFwiaGlkZUVmZmVjdFwiOjk0fSIsIjQ4Ijoie1wiaWRcIjo0OCxcIm5hbWVcIjpcIkxhYmVsIDFcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDQwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6OCxcInR5cGVcIjpcInRleHRcIixcIm5vU3dpcGVcIjpmYWxzZSxcImNvbnRlbnRcIjpcIlNFT1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJ1c2VBY3Rpb25cIjpmYWxzZSxcInNjcm9sbER1cmF0aW9uXCI6MixcIm9mZnNldFhcIjo1NjEsXCJvZmZzZXRZXCI6ODQsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTNcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjoxLjk4NzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sNDAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NDgsXCJzaG93RWZmZWN0XCI6OTUsXCJoaWRlRWZmZWN0XCI6OTZ9IiwiNDkiOiJ7XCJpZFwiOjQ5LFwibmFtZVwiOlwiTGFiZWwgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo5LFwidHlwZVwiOlwidGV4dFwiLFwibm9Td2lwZVwiOmZhbHNlLFwiY29udGVudFwiOlwiU01NXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjYxOSxcIm9mZnNldFlcIjo4NCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC0xM1wiLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjIuNTc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDQwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjQ5LFwic2hvd0VmZmVjdFwiOjk3LFwiaGlkZUVmZmVjdFwiOjk4fSIsIjUxIjoie1wiaWRcIjo1MSxcIm5hbWVcIjpcIkhhbmRcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDUwMHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOmZhbHNlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24taGFuZC1zbWFsbGVyLTExMHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTIsXCJ0eXBlXCI6XCJpbWFnZVwiLFwibm9Td2lwZVwiOmZhbHNlLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWhhbmQtc21hbGxlci5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwidXNlQWN0aW9uXCI6ZmFsc2UsXCJzY3JvbGxEdXJhdGlvblwiOjIsXCJvZmZzZXRYXCI6MjgwLFwib2Zmc2V0WVwiOjE0NCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjo3LjA1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQoZmFsc2Usbiw1MDAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjo1MSxcInNob3dFZmZlY3RcIjoxMDEsXCJoaWRlRWZmZWN0XCI6MTAyfSIsIjU1Ijoie1wiaWRcIjo1NSxcIm5hbWVcIjpcIlRvdWNoXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgc2NhbGVYKDApIHNjYWxlWSgwKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHNjYWxlWCgwKSBzY2FsZVkoMCkgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zbGlkZXItZWxlbWVudC10b3VjaC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxMSxcInR5cGVcIjpcImltYWdlXCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtdG91Y2gucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjI5MSxcIm9mZnNldFlcIjoxMjMsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjowLjQ2MjUsXCJzaG93RGVsYXlcIjo3Ljc3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbixuLG4sbixuLG4sbiwwLDAsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOnRydWUsXCJoaWRlRHVyYXRpb25cIjowLjMzNzUsXCJoaWRlRGVsYXlcIjowLjA1LFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbixuLG4sbixuLDAsMCxuLG4sbixuLG4pXCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOjQsXCJzdHlsZU1vZGVsXCI6NTUsXCJzaG93RWZmZWN0XCI6MTA5LFwiaGlkZUVmZmVjdFwiOjExMH0iLCI1NiI6IntcImlkXCI6NTYsXCJuYW1lXCI6XCJMYWJlbCAxXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSg0MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjgsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJQUENcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwidXNlQWN0aW9uXCI6ZmFsc2UsXCJzY3JvbGxEdXJhdGlvblwiOjIsXCJvZmZzZXRYXCI6MTEyLFwib2Zmc2V0WVwiOjU0LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJzaG93RHVyYXRpb25cIjoxLFwic2hvd0RlbGF5XCI6MS45ODc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDQwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOjQsXCJzdHlsZU1vZGVsXCI6NTYsXCJzaG93RWZmZWN0XCI6MTExLFwiaGlkZUVmZmVjdFwiOjExMn0iLCI1NyI6IntcImlkXCI6NTcsXCJuYW1lXCI6XCJMYWJlbCAyXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSg0MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjksXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJSZXNwb25zaXZlIEFkc1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJ1c2VBY3Rpb25cIjpmYWxzZSxcInNjcm9sbER1cmF0aW9uXCI6MixcIm9mZnNldFhcIjoxNjksXCJvZmZzZXRZXCI6NTQsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTNcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjoyLjU2MjUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sNDAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjo1NyxcInNob3dFZmZlY3RcIjoxMTMsXCJoaWRlRWZmZWN0XCI6MTE0fSIsIjU4Ijoie1wiaWRcIjo1OCxcIm5hbWVcIjpcIndlYiAzXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTAwcHgpIHJvdGF0ZVooMjVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhb" .
						"nNsYXRlWSgtMTAwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0zLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MSxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTMucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjozNjYsXCJvZmZzZXRZXCI6LTY3LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6Ny43Mzc1LFwic2hvd0RlbGF5XCI6My4zODc1LFwic2hvd0Vhc2VcIjpcImxpbmVhclwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xMDAsbixuLG4sbiwyNSxuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6My41MTI1LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJsaW5lYXJcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTAwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjQsXCJzdHlsZU1vZGVsXCI6NTgsXCJzaG93RWZmZWN0XCI6MTE1LFwiaGlkZUVmZmVjdFwiOjExNn0iLCI1OSI6IntcImlkXCI6NTksXCJuYW1lXCI6XCJ3ZWIgMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTAwcHgpIHJvdGF0ZVooMjVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTAwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0yLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MCxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTIucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo0MSxcIm9mZnNldFlcIjoxOTIsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo3LjczNzUsXCJzaG93RGVsYXlcIjozLjM2MjUsXCJzaG93RWFzZVwiOlwibGluZWFyXCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sMTAwLG4sbixuLG4sMjUsbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjMuNTEyNSxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwibGluZWFyXCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLG4sLTEwMCxuLG4sbixuLC0yNSxuLG4sbixuLG4sbixuKVwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjU5LFwic2hvd0VmZmVjdFwiOjExNyxcImhpZGVFZmZlY3RcIjoxMTh9IiwiNjAiOiJ7XCJpZFwiOjYwLFwibmFtZVwiOlwid2ViIDJcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigtMzVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTAwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0yLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MixcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTIucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo4ODgsXCJvZmZzZXRZXCI6LTEzMCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjcuNjUsXCJzaG93RGVsYXlcIjozLjQyNSxcInNob3dFYXNlXCI6XCJsaW5lYXJcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTAwLG4sbixuLG4sLTM1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjozLjUxMjUsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImxpbmVhclwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xMDAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjo2MCxcInNob3dFZmZlY3RcIjoxMTksXCJoaWRlRWZmZWN0XCI6MTIwfSIsIjYxIjoie1wiaWRcIjo2MSxcIm5hbWVcIjpcIndlYiAzXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTAwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTE1MHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMS0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjIsXCJ0eXBlXCI6XCJpbWFnZVwiLFwibm9Td2lwZVwiOmZhbHNlLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0xLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJ1c2VBY3Rpb25cIjpmYWxzZSxcInNjcm9sbER1cmF0aW9uXCI6MixcIm9mZnNldFhcIjo3MDksXCJvZmZzZXRZXCI6MTcxLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6Ni43NzUsXCJzaG93RGVsYXlcIjozLjI3NSxcInNob3dFYXNlXCI6XCJsaW5lYXJcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTAwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjgsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xNTAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NjEsXCJzaG93RWZmZWN0XCI6MTIxLFwiaGlkZUVmZmVjdFwiOjEyMn0iLCI2MiI6IntcImlkXCI6NjIsXCJuYW1lXCI6XCJ3ZWIgMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTAwcHgpIHJvdGF0ZVooMjVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTUwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0zLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MCxcInR5cGVcIjpcImltYWdlXCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTMucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjMxOSxcIm9mZnNldFlcIjoxNjAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo2Ljc4NzUsXCJzaG93RGVsYXlcIjozLjI3NSxcInNob3dFYXNlXCI6XCJsaW5lYXJcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxMDAsbixuLG4sbiwyNSxuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6Mi44LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTUwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjYyLFwic2hvd0VmZmVjdFwiOjEyMyxcImhpZGVFZmZlY3RcIjoxMjR9IiwiNjMiOiJ7XCJpZFwiOjYzLFwibmFtZVwiOlwid2ViIDJcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTUwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0yLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MSxcInR5cGVcIjpcImltYWdlXCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTIucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjU3NixcIm9mZnNldFlcIjotNjgsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo2Ljc3NSxcInNob3dEZWxheVwiOjMuMjc1LFwic2hvd0Vhc2VcIjpcImxpbmVhclwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xMDAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjIuOCxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLG4sLTE1MCxuLG4sbixuLC0yNSxuLG4sbixuLG4sbixuKVwiLFwiYnRuQ2xhc3NcIjpcIm1zLWJ0biBtcy1kZWZhdWx0LWJ0blwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjo2MyxcInNob3dFZmZlY3RcIjoxMjUsXCJoaWRlRWZmZWN0XCI6MTI2fSIsIjY0Ijoie1wiaWRcIjo2NCxcIm5hbWVcIjpcIlNlY29uZGFyeSBUZXh0XCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgxNTBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjozLFwidHlwZVwiOlwidGV4dFwiLFwibm9Td2lwZVwiOmZhbHNlLFwiY29udGVudFwiOlwiRG9uZWMgc2VkIG9kaW8gZHVpLiBGdXNjZSBkYXBpYnVzLCB0ZWxsdXMgYWMgY3Vyc3VzIGNvbW1vZG8sIHRvcnRvciBtYXVyaXMgY29uZGltZW50dW0gbmliaCwgdXQgZmVybWVudHVtIG1hc3NhIGp1c3RvLlwiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJ1c2VBY3Rpb25cIjpmYWxzZSxcInNjcm9sbER1cmF0aW9uXCI6MixcIm9mZnNldFhcIjowLFwib2Zmc2V0WVwiOjUwLFwid2lkdGhcIjo1MDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwibWNcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTJcIixcInNob3dEdXJhdGlvblwiOjEuMjg3NSxcInNob3dEZWxheVwiOjAuMixcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImJ0bkNsYXNzXCI6XCJtcy1idG4gbXMtZGVmYXVsdC1idG5cIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo2NCxcInNob3dFZmZlY3RcIjoxMjcsXCJoaWRlRWZmZWN0XCI6MTI4fSIsIjY1Ijoie1wiaWRcIjo2NSxcIm5hbWVcIjpcIkhlYWRpbmdcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjQsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJTb2NpYWwgTWVkaWEgT3B0aW1pemF0aW9uXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6MTIzLFwid2lkdGhcIjo2MDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGNcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi0xLTY1XCIsXCJzaG93RHVyYXRpb25cIjoxLjI4NzUsXCJzaG93RGVsYXlcIjowLFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDE1MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiYnRuQ2xhc3NcIjpcIm1zLWJ0biBtcy1kZWZhdWx0LWJ0blwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjY1LFwic2hvd0VmZmVjdFwiOjEyOSxcImhpZGVFZmZlY3RcIjoxMzB9IiwiNzkiOiJ7XCJpZFwiOjc5LFwibmFtZVwiOlwiTGFiZWwgM1wiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo3LFwidHlwZVwiOlwidGV4dFwiLFwibm9Td2lwZVwiOmZhbHNlLFwiY29udGVudFwiOlwiQ1JPXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOjYwLFwib2Zmc2V0WVwiOjYwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRjXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJzaG93RHVyYXRpb25cIjoxLjAyNSxcInNob3dEZWxheVwiOjMuMjM3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiYnRuQ2xhc3NcIjpcIm1zLWJ0biBtcy1kZWZhdWx0LWJ0blwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjc5LFwic2hvd0VmZmVjdFwiOjE1NyxcImhpZGVFZmZlY3RcIjoxNTh9IiwiODAiOiJ7XCJpZFwiOjgwLFwibmFtZVwiOlwiTGFiZWwgMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo1LFwidHlwZVwiOlwidGV4dFwiLFwibm9Td2lwZVwiOmZhbHNlLFwiY29udGVudFwiOlwiU0VPXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcInVzZUFjdGlvblwiOmZhbHNlLFwic2Nyb2xsRHVyYXRpb25cIjoyLFwib2Zmc2V0WFwiOi02MC41LFwib2Zmc2V0WVwiOjYwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRjXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEzXCIsXCJzaG93RHVyYXRpb25cIjoxLFwic2hvd0RlbGF5XCI6MS45ODc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDQwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOm51bGwsXCJzdHlsZU1vZGVsXCI6ODAsXCJzaG93RWZmZWN0XCI6MTU5LFwiaGlkZUVmZmVjdFwiOjE2MH0iLCI4MSI6IntcImlkXCI6ODEsXCJuYW1lXCI6XCJMYWJlbCAyXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSg0MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjYsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJub1N3aXBlXCI6ZmFsc2UsXCJjb250ZW50XCI6XCJTTU1cIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwidXNlQWN0aW9uXCI6ZmFsc2UsXCJzY3JvbGxEdXJhdGlvblwiOjIsXCJvZmZzZXRYXCI6MCxcIm9mZnNldFlcIjo2MCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0Y1wiLFwic3RheUhvdmVyXCI6dHJ1ZSxcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC0xM1wiLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjIuNTc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXR" .
						"RdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDQwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJidG5DbGFzc1wiOlwibXMtYnRuIG1zLWRlZmF1bHQtYnRuXCIsXCJzbGlkZVwiOm51bGwsXCJzdHlsZU1vZGVsXCI6ODEsXCJzaG93RWZmZWN0XCI6MTYxLFwiaGlkZUVmZmVjdFwiOjE2Mn0iLCI4MiI6IntcImlkXCI6ODIsXCJuYW1lXCI6XCJ3ZWIgM1wiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTAwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTE1MHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMS0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjIsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0xLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MCxcIm9mZnNldFlcIjoyMTcsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo4LjkzNzUsXCJzaG93RGVsYXlcIjozLjI3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxMDAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjIuOCxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLG4sLTE1MCxuLG4sbixuLC0yNSxuLG4sbixuLG4sbixuKVwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjgyLFwic2hvd0VmZmVjdFwiOjE2MyxcImhpZGVFZmZlY3RcIjoxNjR9IiwiODMiOiJ7XCJpZFwiOjgzLFwibmFtZVwiOlwid2ViIDFcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDEwMHB4KSByb3RhdGVaKDI1ZGVnKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTE1MHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMy0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjAsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0zLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6Nzg2LFwib2Zmc2V0WVwiOjE4NSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjguOTM3NSxcInNob3dEZWxheVwiOjMuMjc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDEwMCxuLG4sbixuLDI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjgsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xNTAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo4MyxcInNob3dFZmZlY3RcIjoxNjUsXCJoaWRlRWZmZWN0XCI6MTY2fSIsIjg0Ijoie1wiaWRcIjo4NCxcIm5hbWVcIjpcIndlYiAyXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTAwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTE1MHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMi0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjEsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0yLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NDQzLjUsXCJvZmZzZXRZXCI6LTE1NSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjguOTM3NSxcInNob3dEZWxheVwiOjMuMjc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xMDAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjIuOCxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLG4sLTE1MCxuLG4sbixuLC0yNSxuLG4sbixuLG4sbixuKVwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjg0LFwic2hvd0VmZmVjdFwiOjE2NyxcImhpZGVFZmZlY3RcIjoxNjh9IiwiODUiOiJ7XCJpZFwiOjg1LFwibmFtZVwiOlwic29jaWFsIGljb24g4oCTIGZhY2Vib2tcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKC0xMzBweCkgc2NhbGVYKDAuNSkgc2NhbGVZKDAuNSkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc29jaWFsLWljb24tZmFjZWJvb2sucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTIsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NvY2lhbC1pY29uLWZhY2Vib29rLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6OTg2LFwib2Zmc2V0WVwiOjEyNyxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNDUsXCJzaG93RGVsYXlcIjoyLjMzNzUsXCJzaG93RWFzZVwiOlwiZWFzZUluUXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsLTEzMCxuLG4sbixuLG4sbiwwLjUsMC41LG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjo1LFwic3R5bGVNb2RlbFwiOjg1LFwic2hvd0VmZmVjdFwiOjE2OSxcImhpZGVFZmZlY3RcIjoxNzB9IiwiODYiOiJ7XCJpZFwiOjg2LFwibmFtZVwiOlwic29jaWFsIGljb24g4oCTIHBpbnRlcmVzdFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEzMHB4KSB0cmFuc2xhdGVZKC04MHB4KSBzY2FsZVgoMC41KSBzY2FsZVkoMC41KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEzMHB4KSB0cmFuc2xhdGVZKC04MHB4KSBzY2FsZVgoMS41KSBzY2FsZVkoMS41KSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NvY2lhbC1pY29ucy1nb29nbGUucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTMsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NvY2lhbC1pY29ucy1nb29nbGUucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo4ODYsXCJvZmZzZXRZXCI6MjA4LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MS40NSxcInNob3dEZWxheVwiOjIuNjYyNSxcInNob3dFYXNlXCI6XCJlYXNlSW5RdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwtMTMwLC04MCxuLG4sbixuLG4sMC41LDAuNSxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjA2MjUsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSwtMTMwLC04MCxuLG4sbixuLG4sMS41LDEuNSxuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjUsXCJzdHlsZU1vZGVsXCI6ODYsXCJzaG93RWZmZWN0XCI6MTcxLFwiaGlkZUVmZmVjdFwiOjE3Mn0iLCI4NyI6IntcImlkXCI6ODcsXCJuYW1lXCI6XCJzb2NpYWwgaWNvbiDigJMgdHdpdHRlclwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoMTMwcHgpIHNjYWxlWCgwLjUpIHNjYWxlWSgwLjUpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgtMTcwcHgpIHNjYWxlWCgxLjUpIHNjYWxlWSgxLjUpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc29jaWFsLWljb24tdHdpdHRlci5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo5LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zb2NpYWwtaWNvbi10d2l0dGVyLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MTE4LFwib2Zmc2V0WVwiOjExOSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNDUsXCJzaG93RGVsYXlcIjoxLjM1LFwic2hvd0Vhc2VcIjpcImVhc2VJblF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLDEzMCxuLG4sbixuLG4sbiwwLjUsMC41LG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjIuMDc1LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsLTE3MCxuLG4sbixuLG4sbiwxLjUsMS41LG4sbixuLG4sbilcIixcInNsaWRlXCI6NSxcInN0eWxlTW9kZWxcIjo4NyxcInNob3dFZmZlY3RcIjoxNzMsXCJoaWRlRWZmZWN0XCI6MTc0fSIsIjg4Ijoie1wiaWRcIjo4OCxcIm5hbWVcIjpcInNvY2lhbCBpY29uIOKAkyBsaW5rZWRpblwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoMTMwcHgpIHRyYW5zbGF0ZVkoLTgwcHgpIHNjYWxlWCgwLjUpIHNjYWxlWSgwLjUpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgtMTMwcHgpIHRyYW5zbGF0ZVkoLTgwcHgpIHNjYWxlWCgxLjUpIHNjYWxlWSgxLjUpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc29jaWFsLWljb24tbGlua2VkaW4ucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTAsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NvY2lhbC1pY29uLWxpbmtlZGluLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MjE3LFwib2Zmc2V0WVwiOjIxNyxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNDUsXCJzaG93RGVsYXlcIjoxLjY3NSxcInNob3dFYXNlXCI6XCJlYXNlSW5RdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwxMzAsLTgwLG4sbixuLG4sbiwwLjUsMC41LG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjIuMDYyNSxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLC0xMzAsLTgwLG4sbixuLG4sbiwxLjUsMS41LG4sbixuLG4sbilcIixcInNsaWRlXCI6NSxcInN0eWxlTW9kZWxcIjo4OCxcInNob3dFZmZlY3RcIjoxNzUsXCJoaWRlRWZmZWN0XCI6MTc2fSIsIjg5Ijoie1wiaWRcIjo4OSxcIm5hbWVcIjpcInNvY2lhbCBpY29uIOKAkyBwaW50ZXJlc3RcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKDEzMHB4KSB0cmFuc2xhdGVZKDgwcHgpIHNjYWxlWCgwLjUpIHNjYWxlWSgwLjUpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgtMTMwcHgpIHRyYW5zbGF0ZVkoLTgwcHgpIHNjYWxlWCgxLjUpIHNjYWxlWSgxLjUpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc29jaWFsLWljb24tcGludGVyZXN0LnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjgsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NvY2lhbC1pY29uLXBpbnRlcmVzdC5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjIxNSxcIm9mZnNldFlcIjozMixcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNDUsXCJzaG93RGVsYXlcIjoxLjA3NSxcInNob3dFYXNlXCI6XCJlYXNlSW5RdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwxMzAsODAsbixuLG4sbixuLDAuNSwwLjUsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6Mi4wNjI1LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsLTEzMCwtODAsbixuLG4sbixuLDEuNSwxLjUsbixuLG4sbixuKVwiLFwic2xpZGVcIjo1LFwic3R5bGVNb2RlbFwiOjg5LFwic2hvd0VmZmVjdFwiOjE3NyxcImhpZGVFZmZlY3RcIjoxNzh9IiwiOTAiOiJ7XCJpZFwiOjkwLFwibmFtZVwiOlwic29jaWFsIGljb24g4oCTIHBpbnRlcmVzdFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEzMHB4KSB0cmFuc2xhdGVZKDgwcHgpIHNjYWxlWCgwLjUpIHNjYWxlWSgwLjUpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgtMTMwcHgpIHRyYW5zbGF0ZVkoLTgwcHgpIHNjYWxlWCgxLjUpIHNjYWxlWSgxLjUpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc29jaWFsLWljb24teW91dHViZS5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxMSxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc29jaWFsLWljb24teW91dHViZS5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjkxMyxcIm9mZnNldFlcIjozNixcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNDUsXCJzaG93RGVsYXlcIjoxLjk4NzUsXCJzaG93RWFzZVwiOlwiZWFzZUluUXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsLTEzMCw4MCxuLG4sbixuLG4sMC41LDAuNSxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjA2MjUsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSwtMTMwLC04MCxuLG4sbixuLG4sMS41LDEuNSxuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjUsXCJzdHlsZU1vZGVsXCI6OTAsXCJzaG93RWZmZWN0XCI6MTc5LFwiaGlkZUVmZmVjdFwiOjE4MH0ifSwiTVNQYW5lbC5Db250cm9sIjp7IjEiOiJ7XCJpZFwiOlwiMVwiLFwibGFiZWxcIjpcIkxpbmUgVGltZXJcIixcIm5hbWVcIjpcInRpbWViYXJcIixcImF1dG9IaWRlXCI6ZmFsc2UsXCJvdmVyVmlkZW9cIjp0cnVlLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC41KVwiLFwid2lkdGhcIjozLFwiYWxpZ25cIjpcImJvdHRvbVwiLFwiaW5zZXRcIjp0cnVlfSIsIjMiOiJ7XCJpZFwiOjMsXCJsYWJlbFwiOlwiQnVsbGV0c1wiLFwibmFtZVwiOlwiYnVsbGV0c1wiLFwiYXV0b0hpZGVcIjpmYWxzZSxcIm92ZXJWaWRlb1wiOnRydWUsXCJtYXJnaW5cIjoyMCxcImRpclwiOlwiaFwiLFwiYWxpZ25cIjpcImJvdHRvbVwiLFwiaW5zZXRcIjp0cnVlfSJ9fQ==", // get import file content

						'custom_styles' => ".msp-cn-1-21 { font-weight:300;font-size:20px;line-height:30px;color:#ffffff; } ". 
						".msp-cn-1-22 { font-weight:300;font-size:48px;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; } ".
						".msp-cn-1-65 { font-weight:300;font-size:48px;text-align:center;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; }",
						'custom_fonts' => '',
						'status' => 'published',
					)
				);

				$wpdb->insert(
					$wpdb->prefix . 'masterslider_options',
					array(
						'ID' => 4,
						'option_name' => 'preset_style',
						'option_value' => 
						"eyJtZXRhIjp7IlByZXNldFN0eWxlIWlkcyI6IjksMTAsMTEsMTIsMTMiLCJQcmVzZXRTdHlsZSFuZXh0SWQiOjE0fSwiTVNQYW5lbC5QcmVzZXRTdHlsZSI6eyI5Ijoie1wiaWRcIjo5LFwibmFtZVwiOlwiU2xpZGVyIOKAkyBIZWFkaW5nXCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC05XCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6NDgsXCJsZXR0ZXJTcGFjaW5nXCI6LTEsXCJsaW5lSGVpZ2h0XCI6XCI0OHB4XCIsXCJjb2xvclwiOlwiI2ZmZmZmZlwiLFwiY3VzdG9tXCI6XCJmb250LXdlaWdodDoyMDA7XCJ9IiwiMTAiOiJ7XCJpZFwiOjEwLFwibmFtZVwiOlwiU2xpZGVyIOKAkyBIZWFkaW5nIChjZW50ZXJlZClcIixcInR5cGVcIjpcInByZXNldFwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTEwXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6NDgsXCJ0ZXh0QWxpZ25cIjpcImNlbnRlclwiLFwibGV0dGVyU3BhY2luZ1wiOi0xLFwibGluZUhlaWdodFwiOlwiNDhweFwiLFwiY29sb3JcIjpcIiNmZmZmZmZcIixcImN1c3RvbVwiOlwiZm9udC13ZWlnaHQ6MjAwO1wifSIsIjExIjoie1wiaWRcIjoxMSxcIm5hbWVcIjpcIlNsaWRlciDigJMgRGVzY3JpcHRpb25cIixcInR5cGVcIjpcInByZXNldFwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTExXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6MjAsXCJsZXR0ZXJTcGFjaW5nXCI6MCxcImxpbmVIZWlnaHRcIjpcIjMwcHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcIlwifSIsIjEyIjoie1wiaWRcIjoxMixcIm5hbWVcIjpcIlNsaWRlciDigJMgRGVzY3JpcHRpb24gKGNlbnRlcmVkKVwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTJcIixcImZvbnRXZWlnaHRcIjpcIjMwMFwiLFwiZm9udFNpemVcIjoyMCxcInRleHRBbGlnblwiOlwiY2VudGVyXCIsXCJsZXR0ZXJTcGFjaW5nXCI6MCxcImxpbmVIZWlnaHRcIjpcIjMwcHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcIlwifSIsIjEzIjoie1wiaWRcIjoxMyxcIm5hbWVcIjpcIlNsaWRlciDigJMgTGFiZWwgKHNtYWxsIHdpdGggYm9yZGVyKVwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtMTNcIixcImJhY2tncm91bmRDb2xvclwiOm51bGwsXCJwYWRkaW5nVG9wXCI6NCxcInBhZGRpbmdSaWdodFwiOjYsXCJwYWRkaW5nQm90dG9tXCI6NCxcInBhZGRpbmdMZWZ0XCI6NixcImJvcmRlclRvcFwiOjIsXCJib3JkZXJSaWdodFwiOjIsXCJib3JkZXJCb3R0b21cIjoyLFwiYm9yZGVyTGVmdFwiOjIsXCJib3JkZXJDb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjQ2KVwiLFwiYm9yZGVyUmFkaXVzXCI6NCxcImJvcmRlclN0eWxlXCI6XCJzb2xpZFwiLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJmb250U2l6ZVwiOjEyLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCIsXCJjb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjYpXCIsXCJjdXN0b21cIjpcIlwifSJ9fQ==", // get import file content
					)
				);

				$wpdb->insert(
					$wpdb->prefix . 'masterslider_options',
					array(
						'ID' => 5,
						'option_name' => 'preset_css',
						'option_value'  => ".msp-preset-9 { font-weight:300;font-size:48px;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; } ".
						".msp-preset-10 { font-weight:300;font-size:48px;text-align:center;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; } ".
						".msp-preset-11 { font-weight:300;font-size:20px;line-height:30px;color:#ffffff; } ".
						".msp-preset-12 { font-weight:300;font-size:20px;text-align:center;line-height:30px;color:#ffffff; } ".
						".msp-preset-13 { padding-top:4px;padding-right:6px;padding-bottom:4px;padding-left:6px;border-top:2px;border-right:2px;border-bottom:2px;border-left:2px;border-color:rgba(255, 255, 255, 0.46);border-radius:4px;border-style:solid;font-weight:normal;font-size:12px;line-height:normal;color:rgba(255, 255, 255, 0.6); }",
					)
				);

				// Force Custom CSS regeneration
				if ( defined('MSWP_AVERTA_VERSION') ) {
					include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/msp-admin-functions.php');
					msp_update_slider_custom_css_and_fonts( 1 );  //$new_slider_id
				}
			}

			// Use a static front page
			$home_page = get_page_by_title( LBMN_HOME_TITLE );
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			// Set the blog page (not needed)
			// $blog = get_page_by_title( LBMN_BLOG_TITLE );
			// update_option( 'page_for_posts', $blog->ID );

			lbmn_debug_console( 'lbmn_customized_css_cache_reset' );
			// Regenerate Custom CSS
			lbmn_customized_css_cache_reset(false); // refresh custom css without printig css (false)

			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				// call the function that normally starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}

			lbmn_debug_console( 'Search & Replace image URLS' );
			// Search & Replace image URLS
			lbmn_lcsearchreplace();

		} // if $_GET['importcontent']

	} // is isset($_GET['importcontent'])
}

/**
* ----------------------------------------------------------------------
* Start a theme tour
*/

if ( is_admin() && isset($_GET['theme_tour'] ) && $pagenow == "themes.php" ) {
	// Register the pointer styles and scripts
	add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

	// Add pointer javascript
	add_action( 'admin_print_footer_scripts', 'add_pointer_scripts' );

	// enqueue javascripts and styles
	function enqueue_scripts()
	{
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	// Add the pointer javascript
	function add_pointer_scripts()
	{
		$pointer_content = '<h3>We use a theme customizer</h3>';
		$pointer_content .= '<p>All theme options available for customization in theme customizer.</p>';
	?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('#menu-appearance a[href="customize.php"]').pointer({
				// pointer_id: 'customizer_menu_link',
				content: '<?php echo $pointer_content; ?>',
				position: {
					 edge: 'left', //top, bottom, left, right
					 align: 'middle' //top, bottom, left, right, middle
				 },
				buttons: function( event, t ) {

					var $buttonClose = jQuery('<a class="button-secondary" style="margin-right:10px;" href="#">End Tour</a>');
					$buttonClose.bind( 'click.pointer', function() {

						t.element.pointer('close');
					});

					var buttons = $('<div class="tiptour-buttons">');
					buttons.append($buttonClose);
					buttons.append('<a class="button-primary" style="margin-right:10px;" href="<?php echo admin_url('customize.php#first-time-visit'); ?>">Go to Theme Customizer</a>');
					return buttons;
				},

				close: function() {
					// Once the close button is hit
					$.post( ajaxurl, {
						pointer: 'customizer_menu_link',
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');

			$(".lumberman-message.quick-setup .step-tour").addClass("step-completed");
		});
		//]]>
		</script>
	<?php
	}
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}

/* Hide quick tour message block */
if ( is_admin() && isset($_GET['hide_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}


/**
 * ----------------------------------------------------------------------
 * Page redirects for LiveComposer Tutorials
 */
add_action( 'template_redirect', 'lbmn_lc_tutorial_redirect' );
function lbmn_lc_tutorial_redirect() {
	if(
		is_user_logged_in() && !isset($_GET['dslc']) &&
		( is_page( 'chapter-1' ) || is_page( 'chapter-2' ) || is_page( 'chapter-3' )  || is_page( 'chapter-4' ) )
	) {
		$arr_params = array( 'dslc' => 'active', 'dslc_tut' => 'start' );
		wp_redirect( add_query_arg($arr_params, get_permalink()) );
		exit();
	}
}

/**
 * ----------------------------------------------------------------------
 * In some situations on theme switch WordPress forget menus
 * that assigned to menu locations
 *
 * The next code block remember [menu id > location] pairs before theme
 * switch and redeclare it when users activate our theme again
 */

add_action( 'current_screen', 'lbmn_save_menu_locations' );
function lbmn_save_menu_locations($current_screen)
{
	// If Apperance > Menu screen visited
	if ( $current_screen->id == 'nav-menus' ) {
		// Remember menus assigned to our locations
		$locations = get_nav_menu_locations();
		update_option( LBMN_THEME_NAME . '_menuid_topbar', $locations['topbar'] );
		update_option( LBMN_THEME_NAME . '_menuid_header', $locations['header-menu'] );
	}
}

add_action('after_switch_theme', 'lbmn_redeclare_menu_locations' );
function lbmn_redeclare_menu_locations () {

	// check if 'header' locaiton has no menu assigned
	$menuid_header = get_option( LBMN_THEME_NAME . '_menuid_header' );
	if( !has_nav_menu('header-menu') && isset($menuid_header) ) {
		// Attach saved before menu id to 'topbar' location
		$locations = get_nav_menu_locations();
		$locations['header-menu'] = $menuid_header;
		set_theme_mod('nav_menu_locations', $locations);
	}

	// check if 'topbar' locaiton has no menu assigned
	$menuid_topbar = get_option( LBMN_THEME_NAME . '_menuid_topbar' );
	if( !has_nav_menu('topbar') && isset($menuid_topbar) ) {
		// Attach saved before menu id to 'topbar' location
		$locations = get_nav_menu_locations();
		$locations['topbar'] = $menuid_topbar;
		set_theme_mod('nav_menu_locations', $locations);
	}
}

// Replace dynamic values of widgets import (called from widgets-importer.php)
function lbmn_strreplace_on_widgetsimport($data) {
	if ($data) {
		global $widget_strings_replace;
		foreach ($widget_strings_replace as $search => $replace) {
			$data = str_replace($search, $replace, $data);
		}
	}
	return $data;
}

/**
 * ----------------------------------------------------------------------
 * LiveComposer image src attribute search and replace
 * Problem: LiveComposer image module has image urls hardcoded, that makes
 * impossilbe to demo import content with images pointing to the new server
 * instead of the demo one.
 * Solution: after images imported go through pages and scan their LiveComposer
 * code for image urls to replace
 */

function lbmn_lcsearchreplace() {
	ini_set('max_execution_time', 360);

	global $wpdb;

	$wpdb_prefix = $wpdb->prefix;
	$replace_before = '//export.seowptheme.com';
	$replace_after = str_replace( array('http://', 'https://'), '//', get_bloginfo( 'url' )); // current website url

	if ( !$replace_before || !$replace_after ) {
		die();
	}

	$results = $wpdb->get_results( "SELECT * FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = 'dslc_code' ORDER BY `meta_key`", OBJECT );
	foreach ( $results as $post ) {
		// echo $post->post_id;
		// echo "<hr />";
		// echo "Page: <strong>";
		// echo get_the_title( $post->post_id  );
		// echo "</strong><br />";

		$raw_lc_code = $post->meta_value;

		// First search replace unserialized urls
		$count = 0;
		$raw_lc_code = str_replace($replace_before, $replace_after, $raw_lc_code, $count);
		// if ( $count ) {
		// 	echo 'Performed '. $count .' replaces of unserialized urls.<br />';
		// }

		$scount = 0;

		if ( $raw_lc_code ) {

			$raw_lc_code_processed = '';
			$module_open_pos = strpos($raw_lc_code, '[dslc_module]');

			while ( $module_open_pos != false ) {
				// var_dump($module_open_pos);
				// echo 'while ( $module_open_pos !== false )<br />';
				$module_open_pos = intval($module_open_pos) + strlen('[dslc_module]');
				$scount = $scount + 1;

				$raw_lc_code_head = substr($raw_lc_code, 0, $module_open_pos );
				$module_close_pos = strpos($raw_lc_code, '[/dslc_module]', $module_open_pos);
				$raw_lc_code_encodedbody = substr($raw_lc_code, $module_open_pos, $module_close_pos - $module_open_pos );
				$raw_lc_code_tail = substr($raw_lc_code, $module_close_pos);

				// Perform search/replace of all image URLs found in encoded body code
				$raw_lc_code_encodedbody = lbmn_lcsearchreplace_image( $raw_lc_code_encodedbody, $replace_before, $replace_after);

				$raw_lc_code_processed .= $raw_lc_code_head . $raw_lc_code_encodedbody;
				$raw_lc_code = $raw_lc_code_tail;
				$module_open_pos = strpos($raw_lc_code, '[dslc_module]');
				if (!$module_close_pos) {
					$module_open_pos = false;
				}
			}

			if ( $raw_lc_code_processed && $raw_lc_code_tail ) {
				$raw_lc_code_processed .= $raw_lc_code_tail;
			}

			if ( $raw_lc_code_processed ) {

				// put data back to database
				$update_result = $wpdb->update(
					$wpdb->postmeta, // table
					array(
							'meta_value' => $raw_lc_code_processed, // data to update
					),
					array( 'meta_id' => $post->meta_id ) //where
				);

				// if ( !$update_result ) {
				// 	echo "Error updating 'postmeta' database";
				// }
			}
		}
	}

	// die();
}

function lbmn_lcsearchreplace_image($module_code_serialized, $replace_before, $replace_after) {

	// Decode and unpack
	$decoded_temp = maybe_unserialize( base64_decode($module_code_serialized) );


	if ( isset($decoded_temp['image']) ) {
		// echo "<br />";
		// echo "Old image src URL replaced: <br />" . $decoded_temp['image'] . "<br />";
		$decoded_temp['image'] = str_replace($replace_before, $replace_after, $decoded_temp['image']);
		// echo $decoded_temp['image'] . "<br />";

		// Encode and pack it back
		$decoded_temp = base64_encode( serialize($decoded_temp) );


		return $decoded_temp;
	} else {
		return $module_code_serialized;
	}
}