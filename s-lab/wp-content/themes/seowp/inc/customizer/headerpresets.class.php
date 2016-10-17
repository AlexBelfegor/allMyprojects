<?php
/**
 * Theme Customizer Header Presets Class
 * NOT USED YET
 * TODO: Finish it
 */

class HeaderDesignPreset {
	// Array of presets
	public $header_design_presets;


	public function __construct() {
		// Get header presets from the database (theme_options)
		$header_presets_db = unserialize( get_theme_mod( 'lbmn_header_preset' ) );

		// Define header_presets theme mod on theme activation
		// if it's not yet set in the theme mods table
		if ( !$header_presets_db ) {
			/** @var array  Predesigned header designs that goes with a theme */
			$predefined_header_design_presets = array(
				'header_design_1' => array(
					// Header design section
					'lbmn_headertop_backgroundcolor' => '#2192dd',
				),
				'header_design_2' => array(
					// Header design section
					'lbmn_headertop_backgroundcolor' => '#222222',
				),
				'header_design_3' => array(
					// Header design section
					'lbmn_headertop_backgroundcolor' => '#333333',
				),
			);

			set_theme_mod( 'lbmn_header_preset', serialize( $predefined_header_design_presets ) );
			$header_presets_db = $predefined_header_design_presets;
		}

		// Set values to array of pressets
		$this -> header_design_presets = $header_presets_db;
	}
	/*
	public function __destruct() {
		// echo 'The class "', __CLASS__, '" was destroyed.<br />';
	}
	*/

	public function set_header_preset( $newpreset_title, $newpreset_settings ) {
		// Update attribute array with a new value
		$this -> header_design_presets[$newpreset_title] = $newpreset_settings;

		// Update header presets in the database
		set_theme_mod( 'lbmn_header_preset', serialize( $this -> header_design_presets ) );
	}


	public function get_header_preset( $preset_key ) {
		return $this -> header_design_presets[$preset_key];
	}

	public function get_header_preset_list() {
		$header_presets = array();
		$header_presets_keys = array_keys( $this -> header_design_presets );
		$header_presets_keys = array_flip($header_presets_keys);
		// lbmn_debug($header_presets_keys);

		foreach ( $header_presets_keys as $key => $value ) {
			// lbmn_debug($key);
			$header_presets[$key] = ucwords( str_replace( '_', ' ', $key ) );
		}

		// lbmn_debug($header_presets);
		return $header_presets;
		// return array_keys( $this -> header_design_presets );
	}

	public function get_header_presets() {
		$header_presets = array();
		$header_presets_keys = array_keys( $this -> header_design_presets );
		$header_presets_keys = array_flip($header_presets_keys);
		// lbmn_debug($header_presets_keys);

		foreach ( $header_presets_keys as $key => $value ) {
			// lbmn_debug($key);
			$header_presets[$key] = ucwords( str_replace( '_', ' ', $key ) );
		}

		// lbmn_debug($header_presets);
		return $header_presets;
		// return array_keys( $this -> header_design_presets );
	}
}

$header_design_presets = new HeaderDesignPreset;
$newpreset_title = 'header_design_4';
$newpreset_settings = array(
	// Header design section
	'lbmn_headertop_backgroundcolor' => '#333333',
);

// $header_design_presets->set_header_preset($newpreset_title, $newpreset_settings);

// lbmn_debug( $header_design_presets->get_header_preset('Header Design 4') );
// lbmn_debug( $header_design_presets->get_header_presets() );

/*
$header_design_presets = new HeaderDesignPreset;

$newpreset_title = 'Header Design 4';
$newpreset_settings = array(
		// Header design section
		'lbmn_headertop_backgroundcolor' => '#333333',
	);

// $obj->set_header_preset($newpreset_title, $newpreset_settings);
lbmn_debug( $header_design_presets->get_header_preset('Header Design 4') );
echo '-------------------';

lbmn_debug( $header_design_presets->get_header_presets() );
echo '-------------------';
// lbmn_debug(unserialize(get_theme_mod( 'lbmn_header_preset')));


// unset ($obj);
*/
// lbmn_debug($obj);
