/**
 * Theme Customizer enhancements for a better user experience.
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * This JavaScript is loading as part of Theme Customizer preview iframe
 * not , not customizer.php
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

( function( $ ) {
	"use strict";

	/**
	* ----------------------------------------------------------------------
	* Return font name by provided preset number
	*/
	// console.log(customizerDataSent.fontPresetsNames);
	function getFontNameByPreset (presetNumber) {
		var fontName = '';
		fontName = customizerDataSent.fontPresetsNames[presetNumber];

		return fontName;
	}

	/**
	* ----------------------------------------------------------------------
	* Update element font css
	*/
	function updateElementFont (controlName, htmlAddress) {
		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				// console.log('controlName:'+controlName);

				// Here we create/replacet <style></style> with CSS for each controlName
				// One <style></style> per controlName
				// Creating <style> instead of using inline css provide us
				// with possiblity to change :hover states live

				// Find if we already have <style data-stylesheet-id="controlName">
				// to make sure we do not create multiply <style> for each controlName
				var styleid = controlName.replace(/[!\"#$%&'\(\)\*\+,\.\/:;<=>\?\@\[\\\]\^`\{\|\}~]/g, '') + 'fontfamily';
				// console.log('styleid:' + styleid);
				var style = document.querySelector("[data-stylesheet-id='" + styleid + "']");
				var createNewStyle = 0;

				// Create <style> if it was't created for controlName before
				if ( !style ) {
					var style = document.createElement('style');
					style.setAttribute('data-stylesheet-id', styleid );
					createNewStyle = 1;
				}


				var activeFontFamilyName = getFontNameByPreset(to);
				activeFontFamilyName = activeFontFamilyName.replace("+"," ");

				var script = document.getElementsByTagName('script')[0],
				styles = 'html ' + htmlAddress + ' {font-family:"' + activeFontFamilyName +'";}';

				if ( createNewStyle ) {
					script.parentNode.insertBefore(style, script);
				}

				try{style.innerHTML = styles;}
				catch(error){style.styleSheet.cssText = styles;} //IE fix

			} );
		} );
	}

	/**
	* ----------------------------------------------------------------------
	* Update element css
	*/
	function updateElementCSS (controlName, htmlAddress, cssProperty, cssPropertyToPrefix, cssPropertyToSuffix) {
		if(typeof(cssPropertyToPrefix)==='undefined') cssPropertyToPrefix = '';
		if(typeof(cssPropertyToSuffix)==='undefined') cssPropertyToSuffix = '';

		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				// console.info(to);

				// Here we create/replacet <style></style> with CSS for each controlName
				// One <style></style> per controlName
				// Creating <style> instead of using inline css provide us
				// with possiblity to change :hover states live

				// Find if we already have <style data-stylesheet-id="controlName">
				// to make sure we do not create multiply <style> for each controlName
				var styleid = controlName.replace(/[!\"#$%&'\(\)\*\+,\.\/:;<=>\?\@\[\\\]\^`\{\|\}~]/g, '') + cssProperty.replace(/[!\"#$%&'\(\)\*\+,\.\/:;<=>\?\@\[\\\]\^`\{\|\}~]/g, '');
				// console.log('styleid:' + styleid);
				var style = document.querySelector("[data-stylesheet-id='" + styleid + "']");
				var createNewStyle = 0;

				// Create <style> if it was't created for controlName before
				if ( !style ) {
					var style = document.createElement('style');
					style.setAttribute('data-stylesheet-id', styleid );
					createNewStyle = 1;
				}

				var script = document.getElementsByTagName('script')[0],
				styles = 'html ' + htmlAddress + ' {' + cssProperty + ':' + cssPropertyToPrefix +  to + cssPropertyToSuffix + ';}';

				// console.log('styles');
				// console.log(styles);

				if ( createNewStyle ) {
					script.parentNode.insertBefore(style, script);
				}

				try{style.innerHTML = styles;}
				catch(error){style.styleSheet.cssText = styles;} //IE fix
			} );
		} );
	}

	/**
	* ----------------------------------------------------------------------
	* Update element class attribute
	*/
	function updateElementClass (controlName, htmlAddress, classesToRemove, classPrefix, classSuffix) {

		if(typeof(classPrefix)==='undefined') classPrefix = '';
		if(typeof(classSuffix)==='undefined') classSuffix = '';

		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				// console.log(controlName);

				var length = classesToRemove.length,
				classToRemove =null;

				for(var i =0; i < length; i++){
					classToRemove = classesToRemove[i];// Do something with element i.
					$( htmlAddress ).removeClass(classToRemove);
				}

				$( htmlAddress ).addClass(classPrefix + to + classSuffix);
			} );
		} );
	}

	/**
	* ----------------------------------------------------------------------
	* Switch element class
	*/
	function switchElementClass (controlName, htmlAddress, classToSwitch) {

		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				if ( to === true ) {
					$( htmlAddress ).addClass(classToSwitch);
				} else {
					$( htmlAddress ).removeClass(classToSwitch);
				}
			});
		});
	}

	/**
	* ----------------------------------------------------------------------
	* Update element attribute
	*/
	function updateElementAttr (controlName, htmlAddress, htmlAttribute) {
		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				$( htmlAddress ).attr(htmlAttribute,to);
			} );
		} );
	}

	/**
	* ----------------------------------------------------------------------
	* Update element text
	*/
	function updateElementText (controlName, htmlAddress) {
		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				$( htmlAddress ).html( to );
			} );
		} );
	}

	/**
	* ----------------------------------------------------------------------
	* Update element display propery
	*/
	function updateDisplayProperty (controlName, htmlAddress) {
		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				if (to) {
					$( htmlAddress ).attr('style', 'display:block!important');
				} else {
					$( htmlAddress ).attr('style', 'display:none!important');
				}
			} );
		} );
	}

	/**
	* ----------------------------------------------------------------------
	* Special update for header/menu panel height
	*/

	var topbarHeightGlobal = 0;
	var headerTopHeightGlobal = 0;
	var megaMenuHeightGlobal = 0;

	function previewHeaderHeightChange (to, elementUpdating ) {
		var location_name = '';

		var topbarHeight = 0;
		var headerTopHeight = 0;
		var megaMenuHeight = 0;

		if ( elementUpdating === 'header' ) {
			location_name = 'header-menu';

			headerTopHeight = to;
			headerTopHeight = parseInt( headerTopHeight.replace("px","") );
			headerTopHeightGlobal = headerTopHeight;
			megaMenuHeight = megaMenuHeightGlobal;
		} else if ( elementUpdating === 'menu' ) {
			location_name = 'header-menu';

			megaMenuHeight = to;
			megaMenuHeight = parseInt( megaMenuHeight.replace("px","") );
			megaMenuHeightGlobal = megaMenuHeight;
			headerTopHeight = headerTopHeightGlobal;
		} else if ( elementUpdating === 'topbar' ) {
			location_name = 'topbar';

			topbarHeight = to;
			topbarHeight = parseInt( topbarHeight.replace("px","") );
			topbarHeightGlobal = headerTopHeightGlobal = topbarHeight;

			headerTopHeight = megaMenuHeight = topbarHeight;
		}


		if ( headerTopHeightGlobal === 0 ) {
			headerTopHeightGlobal = headerTopHeight = customizerDataSent.headerTopHeight;
		}

		if ( megaMenuHeightGlobal === 0 ) {
			megaMenuHeightGlobal =  megaMenuHeight = customizerDataSent.megaMenuHeight;
		}

		if ( topbarHeightGlobal === 0 ) {
			topbarHeightGlobal = topbarHeight = customizerDataSent.topbarHeight;
		}

		// Here we create/replacet <style></style> with CSS for each update
		// Find if we already have <style data-stylesheet-id="lbmn_headertop_height">

		var style = document.querySelector("[data-stylesheet-id='lbmn_header_" + location_name + "']");
		var createNewStyle = 0;

		// Create <style> if it was't created for controlName before
		if ( !style ) {
			var style = document.createElement('style');
			style.setAttribute('data-stylesheet-id', 'lbmn_header_' + location_name, '');
			createNewStyle = 1;
		}

		var script = document.getElementsByTagName('script')[0];
		var styles = '';
		var globalPrefix = 'html ';

		/**
		* ------------------------------------------------------------------------
		* Header mega main menu
		*/

		location_name = "." + location_name;

		// My custom style for mega main menu panel
		//	.topbar .menu_holder {
		styles += globalPrefix + location_name + " .menu_holder {";
			styles += "min-height:" + headerTopHeight + "px;";

			if ( megaMenuHeight ) {
				if ( ( headerTopHeight  - megaMenuHeight ) > 0 ) {
					styles += "padding-top:" + ( headerTopHeight  - megaMenuHeight ) /2 + "px;";
				} else {
					styles += "padding-top:0px;";
				}
			}
		styles += "}";



		styles += globalPrefix + location_name + " .nav_logo {";
			styles += "min-height:" + headerTopHeight + "px;";
			if ( megaMenuHeight ) {
				if ( ( headerTopHeight  - megaMenuHeight ) > 0 ) {
					styles += "margin-top: -" + ( headerTopHeight  - megaMenuHeight ) /2 + "px;";
				} else {
					styles += "margin-top:0px;";
				}
			}
		styles += "}";

		styles += globalPrefix + location_name + " .nav_logo .logo_link {";
			styles += "min-height:" + headerTopHeight + "px;";
			styles += "line-height:" + headerTopHeight + "px;";
		styles += "}";


		// skin.php line ~35
		// initial_height

		styles += globalPrefix + "#mega_main_menu" + location_name + "{";
			styles += "min-height:" + headerTopHeight + "px;";
		styles += "}";

		// Initial menu container height
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > .nav_logo > .logo_link,";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle,";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button,";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > ul > li > .item_link, ";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > ul > li > .item_link > .link_content, ";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > ul > li.nav_search_box, ";

		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-left > .menu_holder > .menu_inner > ul > li > .item_link > i, ";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-right > .menu_holder > .menu_inner > ul > li > .item_link > i, ";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link.disable_icon > .link_content, ";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link.menu_item_without_text > i, ";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > ul > li.nav_buddypress > .item_link > i.ci-icon-buddypress-user ";
		styles += "{";
			styles += "height:" + megaMenuHeight + "px;";
			styles += "line-height:" + megaMenuHeight + "px;";
		styles += "}";


		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > ul > li > .item_link > .link_content > .link_text";
		styles += "{";
			styles += "height:" + megaMenuHeight + "px;";
		styles += "}";

		// Icons Top

		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link > i,";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link > .link_content";
		styles += "{";
			styles += "height:" + ( megaMenuHeight / 2 ) + "px;";
			styles += "line-height:" + ( megaMenuHeight / 3 ) + "px;";
		styles += "}";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link.with_icon > .link_content > .link_text";
		styles += "{";
			styles += "height:" + ( megaMenuHeight / 3 ) + "px;";
		styles += "}";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link > i";
		styles += "{";
			styles += "padding-top:" + ( megaMenuHeight / 3 / 2 ) + "px;";
		styles += "}";
		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder > .menu_inner > ul > li > .item_link > .link_content";
		styles += "{";
			styles += "padding-bottom:" + ( megaMenuHeight / 3 / 2 ) + "px;";
		styles += "}";
		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder > .menu_inner > ul > li.nav_buddypress > .item_link > i:before";
		styles += "{";
			styles += "width:" + ( megaMenuHeight * 0.6 ) + "px;";
		styles += "}";





		// IF LOGO IS ABOVE MENU
		styles += globalPrefix + location_name + ".logoplacement-top-left .nav_logo,";
		styles += globalPrefix + location_name + ".logoplacement-top-center .nav_logo,";
		styles += globalPrefix + location_name + ".logoplacement-top-right .nav_logo {";
			styles += "min-height:" +  ( headerTopHeight  - megaMenuHeight ) + "px;";
		styles += "}";

		styles += globalPrefix + location_name + ".logoplacement-top-left .nav_logo .logo_link,";
		styles += globalPrefix + location_name + ".logoplacement-top-center .nav_logo .logo_link,";
		styles += globalPrefix + location_name + ".logoplacement-top-right .nav_logo .logo_link {";
			styles += "min-height:" +  ( headerTopHeight  - megaMenuHeight ) + "px;";
			styles += "line-height:" +  ( headerTopHeight  - megaMenuHeight ) + "px;";
		styles += "}";

		if ( createNewStyle ) {
			script.parentNode.insertBefore(style, script);
		}

		try{style.innerHTML = styles;}
		catch(error){style.styleSheet.cssText = styles;} //IE fix
	}


	/**
	 * ----------------------------------------------------------------------
	 * Preview sticky header height change
	 */

 	function previewStickyHeaderHeightChange (to, location_name ) {
 		// Here we create/replacet <style></style> with CSS for each update
 		// Find if we already have <style data-stylesheet-id="lbmn_headertop_height">

 		var style = document.querySelector("[data-stylesheet-id='lbmn_header_" + location_name + "_stickycontainer']");
 		var createNewStyle = 0;

 		// Create <style> if it was't created for controlName before
 		if ( !style ) {
 			var style = document.createElement('style');
 			style.setAttribute('data-stylesheet-id', 'lbmn_header_' + location_name + '_stickycontainer', '');
 			createNewStyle = 1;
 		}

 		var script = document.getElementsByTagName('script')[0];
 		var styles = '';
 		var globalPrefix = 'html ';

 		location_name = "." + location_name;

 		// My custom style for mega main menu panel
 		styles += globalPrefix + "#mega_main_menu" + location_name + " .sticky_container {"; //.menu_holder.sticky_container
 			styles += "min-height:" + to + "px;";
 		styles += "}";

 		styles += globalPrefix + "#mega_main_menu" + location_name + " .sticky_container .nav_logo {";
 			styles += "min-height:" + to + "px;";
 		styles += "}";

 		styles += globalPrefix + "#mega_main_menu" + location_name + " .sticky_container .nav_logo .logo_link {";
 			styles += "min-height:" + to + "px;";
 			styles += "line-height:" + to + "px;";
 		styles += "}";

 		// skin.php line ~81
 		// initial_height_sticky

 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > .nav_logo > .logo_link,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > .nav_logo > .mobile_toggle,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > .link_content,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > ul > li.nav_search_box,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-left > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > i,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-right > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > i,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link.disable_icon > .link_content,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link.menu_item_without_text > i,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > ul > li.nav_buddypress > .item_link > i.ci-icon-buddypress-user";
 		styles += "{";
 			styles += "height:" + to + "px;";
 			styles += "line-height:" + to + "px;";
 		styles += "}";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > .link_content > .link_text";
 		styles += "{";
 			styles += "height:" + to + "px;";
 		styles += "}";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > i,";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > .link_content";
 		styles += "{";
 			styles += "height:" + ( to / 2 ) + "px;";
 			styles += "line-height:" + ( to / 3 ) + "px;";
 		styles += "}";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link.with_icon > .link_content > .link_text";
 		styles += "{";
 			styles += "height:" + ( to / 3 ) + "px;";
 		styles += "}";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > i";
 		styles += "{";
 			styles += "padding-top:" + ( to / 3 / 2 ) + "px;";
 		styles += "}";
 		styles += globalPrefix + "#mega_main_menu" + location_name + ".icons-top > .menu_holder.sticky_container > .menu_inner > ul > li > .item_link > .link_content";
 		styles += "{";
 			styles += "padding-bottom:" + ( to / 3 / 2 ) + "px;";
 		styles += "}";
 		styles += globalPrefix + "#mega_main_menu" + location_name + " > .menu_holder.sticky_container > .menu_inner > ul > li.nav_buddypress > .item_link > i:before";
 		styles += "{";
 			styles += "width:" + ( to * 0.6 ) + "px;";
 		styles += "}";

 		if ( createNewStyle ) {
 			script.parentNode.insertBefore(style, script);
 		}

 		try{style.innerHTML = styles;}
 		catch(error){style.styleSheet.cssText = styles;} //IE fix
 	}

	/**
	* ----------------------------------------------------------------------
	* HEX TO RGB converter
	* alert( hexToRgb("#0033ff").g ); // "51";
	* alert( hexToRgb("#03f").g ); // "51";
	*/

	function hexToRgb(hex) {
	    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
	    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
	        return r + r + g + g + b + b;
	    });

	    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	    return result ? {
	        r: parseInt(result[1], 16),
	        g: parseInt(result[2], 16),
	        b: parseInt(result[3], 16)
	    } : null;
	}

	/**
	* ----------------------------------------------------------------------
	* Special update that fire some additional JS for logo position (top / bottom)
	*/
	/*
	function updateElementClass (controlName, htmlAddress, classesToRemove, classPrefix, classSuffix) {

		if(typeof(classPrefix)==='undefined') classPrefix = '';
		if(typeof(classSuffix)==='undefined') classSuffix = '';

		wp.customize( controlName, function( value ) {
			value.bind( function( to ) {
				// console.log(controlName);

				var length = classesToRemove.length,
				classToRemove =null;

				for(var i =0; i < length; i++){
					classToRemove = classesToRemove[i];// Do something with element i.
					$( htmlAddress ).removeClass(classToRemove);
				}

				$( htmlAddress ).addClass(classPrefix + to + classSuffix);
			} );
		} );
	}
	*/

	/**
	* ----------------------------------------------------------------------
	* Notification panel 'Enable' checkbox
	*/
	// check state on load and show/hide .notification-panel based on state
	jQuery(document).ready(function ($) {
		if ( $('.notification-panel').data('stateonload') == ''  ) {
			$( '.notification-panel' ).css( 'display', 'none' );
		}
	});

	// Enable/Disable switch
	updateDisplayProperty ('lbmn_notificationpanel_switch', '.notification-panel');
	updateElementCSS ('lbmn_notificationpanel_height', '.notification-panel:before', 'min-height', '', 'px');
	// updateElementCSS ('lbmn_notificationpanel_height', '.notification-panel__close', 'line-height', '', 'px');

	// update icon
	wp.customize( 'lbmn_notificationpanel_icon', function( value ) {
		value.bind( function( to ) {
			$( '.notification-panel__icon' ).attr('class','notification-panel__icon');
			$( '.notification-panel__icon' ).addClass(to);
		} );
	} );
	updateElementText ('lbmn_notificationpanel_message', '.notification-panel__message-text'); // update message text

	// update inline-css for colors
	updateElementCSS ('lbmn_notificationpanel_backgroundcolor', '.notification-panel', 'background-color');
	updateElementCSS ('lbmn_notificationpanel_textcolor', '.notification-panel, html .notification-panel *', 'color');
	updateElementCSS ('lbmn_notificationpanel_backgroundcolor_hover', '.notification-panel:hover', 'background-color');
	updateElementCSS ('lbmn_notificationpanel_textcolor_hover', '.notification-panel:hover, html .notification-panel:hover *', 'color');


	/**
	* ----------------------------------------------------------------------
	* Top bar
	*/

	// Enable/Disable switch
	updateDisplayProperty ('lbmn_topbar_switch', '.topbar');

	wp.customize( 'lbmn_topbar_height', function( value ) {
		value.bind( function( to ) { previewHeaderHeightChange (to, 'topbar' ) } );
	} );

	// updateElementCSS ('lbmn_topbar_height', '.topbar .menu_holder', 'min-height');

	updateElementCSS ('lbmn_topbar_backgroundcolor', '.topbar .menu_holder:before', 'background-color');

	// TOP BAR > COLORS
	updateElementCSS ('lbmn_topbar_linkcolor',                '#mega_main_menu.topbar > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link *', 'color');
	updateElementCSS ('lbmn_topbar_linkhovercolor',           '#mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li:hover > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link:hover, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li:hover > .item_link *, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.current-menu-ancestor > .item_link *, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.current-menu-item > .item_link *', 'color');
	updateElementCSS ('lbmn_topbar_linkhoverbackgroundcolor', '#mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li:hover > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link:hover, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.current-menu-ancestor > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.current-menu-item > .item_link', 'background-color');
	updateElementCSS ('lbmn_topbar_textlinescolor',           'body #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > span.item_link, html body #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > span.item_link *', 'color' );

	// TOP BAR > FONTS
	updateElementFont ('lbmn_topbar_firstlevelitems_font',       '#mega_main_menu.topbar > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link .link_text, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.nav_search_box *, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li .post_details > .post_title, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li .post_details > .post_title > .item_link');
	updateElementCSS  ('lbmn_topbar_firstlevelitems_fontsize',   '#mega_main_menu.topbar > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link .link_text, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.nav_search_box *, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li .post_details > .post_title, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li .post_details > .post_title > .item_link', 'font-size');
	updateElementCSS  ('lbmn_topbar_firstlevelitems_fontweight', '#mega_main_menu.topbar > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link .link_text, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li.nav_search_box *, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li .post_details > .post_title, html #mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li .post_details > .post_title > .item_link', 'font-weight');

	// TOP BAR > SETTINGS
	updateElementClass ('lbmn_topbar_firstlevelitems_align',   '.topbar', new Array('first-lvl-align-center', 'first-lvl-align-left', 'first-lvl-align-right'), 'first-lvl-align-');
	updateElementCSS   ('lbmn_topbar_linkhoverborderradius',   'body #mega_main_menu.topbar > .menu_holder > .menu_inner:first-child > ul > li > .item_link, html body #mega_main_menu.topbar > .menu_holder > .menu_inner:first-child > ul > li:hover > .item_link', 'border-radius', '', 'px!important');
	updateElementCSS   ('lbmn_topbar_firstlevelitems_spacing', 'body #mega_main_menu.topbar > .menu_holder > .menu_inner:first-child > ul > li > .item_link, html body #mega_main_menu.topbar > .menu_holder > .menu_inner:first-child > ul > li:hover > .item_link', 'margin-right', '', 'px');
	// TOP BAR > ICONS
	updateElementClass ('lbmn_topbar_firstlevelitems_iconposition', '.topbar', new Array('icons-top', 'icons-left', 'icons-right', 'icons-disable_first_lvl', 'icons-disable_globally'), 'icons-');
	updateElementCSS   ('lbmn_topbar_firstlevelitems_iconsize',     '#mega_main_menu.topbar > .menu_holder > .menu_inner > ul > li > .item_link > i', 'font-size');

	// TOP BAR > SEPARATORS
	updateElementClass ('lbmn_topbar_firstlevelitems_separator', '#mega_main_menu.topbar', new Array('first-lvl-separator-sharp', 'first-lvl-separator-smooth', 'first-lvl-separator-none'), 'first-lvl-separator-');
	updateElementCSS ('lbmn_topbar_firstlevelitems_separator_opacity', '#mega_main_menu.topbar.direction-horizontal > .menu_holder > .menu_inner > ul > li > .item_link:before, body #mega_main_menu.topbar.direction-horizontal > .menu_holder > .menu_inner > ul > li.nav_search_box:before', 'opacity');

	/**
	* ---------------------------------------------------------------------------
	* HEADER
	*/

	// HEADER DESIGN SECTION
	updateElementCSS ('lbmn_headertop_backgroundcolor', '.header-menu .menu_holder:before', 'background-color');
	updateElementCSS ('lbmn_headertop_stick_backgroundcolor', '.header-menu .menu_holder.sticky_container:before', 'background-color');

	wp.customize( 'lbmn_headertop_height', function( value ) {
		value.bind( function( to ) { previewHeaderHeightChange (to, 'header' ) } );
	} );

	wp.customize( 'lbmn_headertop_menu_height', function( value ) {
		value.bind( function( to ) { previewHeaderHeightChange (to, 'menu' ) } );
	} );

	wp.customize( 'lbmn_headertop_stick', function( value ) {
		value.bind( function( to ) {
			if (to) {
				$( '.header-menu .menu_holder' ).attr('data-sticky','1');
				updateStickyOffset();
			} else {
				$( '.header-menu .menu_holder' ).attr('data-sticky','0');
			}
		} );
	} );

	wp.customize( 'lbmn_headertop_stick_height', function( value ) {
		value.bind( function( to ) { previewStickyHeaderHeightChange (to, 'header-menu' ) } );
	} );

	// STICKY MENU PADDING
	updateElementCSS ('lbmn_headertop_sticky_padding',   '#mega_main_menu.header-menu .sticky_container', 'padding-top', '','px');
	updateElementCSS ('lbmn_headertop_sticky_padding',   '#mega_main_menu.header-menu .sticky_container', 'padding-bottom', '','px');

	// MENU SECTION BACKGROUND
	updateElementCSS ('lbmn_megamenu_sectionbackgroundcolor',   '.header-menu .menu_holder:after', 'background-color');
	updateElementCSS ('lbmn_megamenu_sectionbackgroundopacity', '.header-menu .menu_holder:after', 'opacity');

	// MEGA MENU > COLORS
	updateElementCSS ('lbmn_megamenu_linkcolor',                '#mega_main_menu.header-menu > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link *', 'color');
	updateElementCSS ('lbmn_megamenu_linkhovercolor',           '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li:hover > .item_link, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link:hover, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li:hover > .item_link *, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.current-menu-ancestor > .item_link *, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.current-menu-item > .item_link *', 'color');
	updateElementCSS ('lbmn_megamenu_linkhoverbackgroundcolor', '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li:hover > .item_link,.header-menu > .menu_holder > .menu_inner > ul > li > .item_link:hover, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.current-menu-ancestor > .item_link, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.current-menu-item > .item_link', 'background-color');
	updateElementCSS ('lbmn_megamenu_textlinescolor',           'body #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > span.item_link, html body #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > span.item_link *', 'color' );

	// MEGA MENU > FONTS
	updateElementFont ('lbmn_megamenu_firstlevelitems_font',       '#mega_main_menu.header-menu > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link .link_text, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.nav_search_box *, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .post_details > .post_title, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .post_details > .post_title > .item_link');
	updateElementCSS  ('lbmn_megamenu_firstlevelitems_fontsize',   '#mega_main_menu.header-menu > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link .link_text, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.nav_search_box *, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .post_details > .post_title, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .post_details > .post_title > .item_link', 'font-size');
	updateElementCSS  ('lbmn_megamenu_firstlevelitems_fontweight', '#mega_main_menu.header-menu > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link .link_text, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.nav_search_box *, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .post_details > .post_title, html #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .post_details > .post_title > .item_link', 'font-weight');

	// MEGA MENU > SETTINGS
	updateElementClass ('lbmn_megamenu_firstlevelitems_align',   '.header-menu', new Array('first-lvl-align-center', 'first-lvl-align-left', 'first-lvl-align-right'), 'first-lvl-align-');
	updateElementCSS   ('lbmn_megamenu_linkhoverborderradius',   'body #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link, html body #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li:hover > .item_link', 'border-radius', '', 'px');
	updateElementCSS   ('lbmn_megamenu_firstlevelitems_spacing', 'body #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link, html body #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li:hover > .item_link', 'margin-right', '', 'px');
	updateElementCSS   ('lbmn_megamenu_firstlevelitems_innerspacing', '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link', 'padding-left', '', 'px');
	updateElementCSS   ('lbmn_megamenu_firstlevelitems_innerspacing', '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link', 'padding-right', '', 'px');

	// MEGA MENU > ICONS
	updateElementClass ('lbmn_megamenu_firstlevelitems_iconposition', '.header-menu', new Array('icons-top', 'icons-left', 'icons-right', 'icons-disable_first_lvl', 'icons-disable_globally'), 'icons-');
	updateElementCSS   ('lbmn_megamenu_firstlevelitems_iconsize',     '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link > i', 'font-size');

	// MEGA MENU > SEPARATORS
	updateElementClass ('lbmn_megamenu_firstlevelitems_separator', '#mega_main_menu.header-menu', new Array('first-lvl-separator-sharp', 'first-lvl-separator-smooth', 'first-lvl-separator-none'), 'first-lvl-separator-');
	updateElementCSS ('lbmn_megamenu_firstlevelitems_separator_opacity', '#mega_main_menu.header-menu.direction-horizontal > .menu_holder > .menu_inner > ul > li > .item_link:before, body #mega_main_menu.header-menu.direction-horizontal > .menu_holder > .menu_inner > ul > li.nav_search_box:before', 'opacity');

	// MEGA MENU DROPDOWN > COLORS
	updateElementCSS ('lbmn_megamenu_dropdown_textcolor',      '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .mega_dropdown *', 'color');
	updateElementCSS ('lbmn_megamenu_dropdown_linkcolor',      '#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .post_details > .post_icon > i, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown .item_link *, html#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown a, html#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown a *, html#mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li > .item_link *, html#mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li > .item_link *#mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li > .item_link *, html#mega_main_menu.mega_main_menu ul li li .post_details a', 'color');
	updateElementCSS ('lbmn_megamenu_dropdown_linkhovercolor', '#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown .item_link:hover, html #mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li:hover > .item_link, html #mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li > .item_link:hover, html #mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li:hover > .item_link, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li > .item_link:hover, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li:hover > .processed_image, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li:hover > .item_link, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li > .item_link:hover, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li > .processed_image:hover, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown .item_link:hover *, html #mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li:hover > .item_link *, html #mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li.current-menu-item > .item_link *, html #mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li > .item_link:hover *, html #mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li.current-menu-item > .item_link *, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li:hover > .item_link *, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li.current-menu-item > .item_link *, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li:hover > .item_link *, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li a:hover *, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li.current-menu-item > .item_link *, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li > .processed_image:hover > .cover > a > i', 'color');
	updateElementCSS ('lbmn_megamenu_dropdown_menuitemsdividercolor', '#mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li > .item_link', 'border-color');

	// MEGA MENU DROPDOWN > FONTS
	updateElementCSS ('lbmn_megamenu_dropdown_linkhoverbackgroundcolor', '#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown .item_link:hover, html #mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li:hover > .item_link, html #mega_main_menu.mega_main_menu ul li.default_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li > .item_link:hover, html #mega_main_menu.mega_main_menu ul li.multicolumn_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li:hover > .item_link, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li > .item_link:hover, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li:hover > .processed_image, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li:hover > .item_link, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li > .item_link:hover, html #mega_main_menu.mega_main_menu ul li.grid_dropdown .mega_dropdown > li.current-menu-item > .item_link, html #mega_main_menu.mega_main_menu ul li.post_type_dropdown .mega_dropdown > li > .processed_image:hover', 'background-color');
	updateElementCSS ('lbmn_megamenu_dropdown_background', '#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li.default_dropdown .mega_dropdown, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li > .mega_dropdown, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .mega_dropdown > li .post_details', 'background-color');
	updateElementClass ('lbmn_megamenu_dropdown_animation', '#mega_main_menu.mega_main_menu', new Array('dropdowns_animation-none', 'dropdowns_animation-anim_1', 'dropdowns_animation-anim_2', 'dropdowns_animation-anim_3', 'dropdowns_animation-anim_4', 'dropdowns_animation-anim_5'), 'dropdowns_animation-');
	// updateElementCSS ('lbmn_headertop_dropdown_menuitemsdividercolor', '#mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle > .mobile_button,.mega_main_menu > .menu_holder > .menu_inner > ul > li > .item_link,.mega_main_menu > .menu_holder > .menu_inner > ul > li > .item_link *', 'color');

	// MEGA MENU DROPDOWN > RADIUS
	updateElementCSS ('lbmn_megamenu_dropdownradius', '#mega_main_menu.mega_main_menu.primary_style-buttons > .menu_holder > .menu_inner > ul > li > .item_link, html #mega_main_menu.mega_main_menu.primary_style-buttons > .menu_holder > .menu_inner > .nav_logo > .mobile_toggle, html #mega_main_menu.mega_main_menu.primary_style-buttons.direction-vertical > .menu_holder > .menu_inner > ul > li:first-child > .item_link, html #mega_main_menu.mega_main_menu > .menu_holder, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .post_details, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul .mega_dropdown', 'border-radius', '', 'px');
	updateElementCSS ('lbmn_megamenu_dropdown_opacity', '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li > .item_link:after', 'opacity');


	// DROPDOWN and SEARCH BAR > FONTS
	updateElementFont ('lbmn_megamenu_dropdown_font', '#mega_main_menu.mega_main_menu li .mega_dropdown > li > .item_link, html #mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link .link_text, html #mega_main_menu.mega_main_menu ul li .mega_dropdown, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .post_details > .post_description, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li.nav_search_box #mega_main_menu_searchform .field:focus');
	updateElementCSS ('lbmn_megamenu_dropdown_fontsize', '#mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link, html #mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link .link_text, html #mega_main_menu.mega_main_menu ul li .mega_dropdown, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .post_details > .post_description, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li.nav_search_box #mega_main_menu_searchform .field:focus', 'font-size');
	updateElementCSS ('lbmn_megamenu_dropdown_fontweight', '#mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link, html #mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link .link_text, html #mega_main_menu.mega_main_menu ul li .mega_dropdown, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li .post_details > .post_description, html #mega_main_menu.mega_main_menu > .menu_holder > .menu_inner > ul > li.nav_search_box #mega_main_menu_searchform .field:focus', 'font-weight');
	// updateElementCSS ('lbmn_megamenu_dropdown_iconsize', '#mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link > i', 'font-size');

	// dropdown icon font-size update
	wp.customize( 'lbmn_megamenu_dropdown_iconsize', function( value ) {
		value.bind( function( to ) {
			if (to) {
				$( 'html #mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link > i' ).css({
					'font-size': to + 'px',
					'height': to + 'px',
					'line-height': to + 'px',
					'width': to + 'px',
					'margin-top': '-' + ( parseInt(to) / 2 ) + 'px',
				});

				$( 'html #mega_main_menu.mega_main_menu ul li .mega_dropdown > li > .item_link.with_icon > span' ).css({
					'margin-left': ( parseInt(to) + 8 ) + 'px',
				});
			}
		} );
	} );



	// SEARCH BAR
	updateDisplayProperty ('lbmn_searchblock_switch', '.header-menu .nav_search_box');
	updateElementCSS ('lbmn_searchblock_inputfieldwidth', '#mega_main_menu > .menu_holder > .menu_inner > ul > li.nav_search_box #mega_main_menu_searchform .field:focus', 'width','','px');
	updateElementCSS ('lbmn_searchblock_inputfieldwidth', '#mega_main_menu > .menu_holder > .menu_inner > ul > li.nav_search_box #mega_main_menu_searchform .field:focus', 'max-width','','px');
	updateElementCSS ('lbmn_searchblock_inputfieldradius', '#mega_main_menu.header-menu ul .nav_search_box #mega_main_menu_searchform:before', 'border-radius','','px');

	updateElementCSS ('lbmn_searchblock_inputbackgroundcolor', '.header-menu li.nav_search_box > #mega_main_menu_searchform:before', 'background-color');
	updateElementCSS ('lbmn_searchblock_textandiconcolor', '#mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.nav_search_box .field, html  #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li.nav_search_box *, html  #mega_main_menu.header-menu > .menu_holder > .menu_inner > ul > li .icosearch', 'color');
	updateElementClass ('lbmn_searchblock_shadow', '.header-menu', new Array('search-shadow-inside', 'search-shadow-outside', 'search-shadow-none'), 'search-shadow-');



	/**
	* ----------------------------------------------------------------------
	* Logo
	*/

	updateElementClass ('lbmn_logo_placement', '.header-menu', new Array('logoplacement-top-left', 'logoplacement-top-right', 'logoplacement-top-center', 'logoplacement-bottom-left', 'logoplacement-bottom-right'), 'logoplacement-');
	updateElementCSS   ('lbmn_logo_height', '#mega_main_menu .nav_logo > .logo_link > img', 'max-height','','%');
	updateElementCSS   ('lbmn_logo_margin_top', '#mega_main_menu .nav_logo .logo_link', 'margin-top','','px');
	updateElementCSS   ('lbmn_logo_margin_left', '#mega_main_menu .nav_logo .logo_link', 'margin-left','','px');
	updateElementCSS   ('lbmn_logo_margin_right', '#mega_main_menu .nav_logo .logo_link', 'margin-right','','px');



	/**
	* ---------------------------------------------------------------------------
	* Page Layout
	*/

	updateElementCSS ('lbmn_content_background_color', 'body, html .global-wrapper', 'background-color');
	switchElementClass ('lbmn_pagelayoutboxed_switch', 'body', 'boxed-page-layout');
	updateElementCSS   ('lbmn_page_background_color', 'body.boxed-page-layout', 'background-color');
	updateElementCSS   ('lbmn_page_background_image', 'body.boxed-page-layout:before', 'background-image', 'url(', ')');
	updateElementCSS   ('lbmn_page_background_image_opacity', 'body.boxed-page-layout:before', 'opacity');
	updateElementCSS   ('lbmn_page_background_image_repeat', 'body.boxed-page-layout:before', 'background-repeat');
	updateElementCSS   ('lbmn_page_background_image_position', 'body.boxed-page-layout:before', 'background-position');
	updateElementCSS   ('lbmn_page_background_image_attachment', 'body.boxed-page-layout:before', 'background-attachment');
	updateElementCSS   ('lbmn_page_background_image_size', 'body.boxed-page-layout:before', 'background-size');


	/**
	* ----------------------------------------------------------------------
	* Typography
	*/

	// Links
	updateElementCSS ('lbmn_typography_link_color', 'a', 'color');
	updateElementCSS ('lbmn_typography_link_hover_color', 'a:hover', 'color');

	// Paragraphs
	updateElementFont('lbmn_typography_p_font', 'body');
	updateElementCSS ('lbmn_typography_p_fontsize', '.site', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_p_fontweight', 'body', 'font-weight');
	updateElementCSS ('lbmn_typography_p_lineheight', 'body', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_p_marginbottom', 'p', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_p_color', 'body', 'color');

	// Heading 1
	updateElementFont('lbmn_typography_h1_font', 'h1');
	updateElementCSS ('lbmn_typography_h1_fontsize', 'h1', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_h1_fontweight', 'h1', 'font-weight');
	updateElementCSS ('lbmn_typography_h1_lineheight', 'h1', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_h1_marginbottom', 'h1', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_h1_color', 'h1', 'color');

	// Heading 2
	updateElementFont('lbmn_typography_h2_font', 'h2');
	updateElementCSS ('lbmn_typography_h2_fontsize', 'h2', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_h2_fontweight', 'h2', 'font-weight');
	updateElementCSS ('lbmn_typography_h2_lineheight', 'h2', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_h2_marginbottom', 'h2', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_h2_color', 'h2', 'color');

	// Heading 3
	updateElementFont('lbmn_typography_h3_font', 'h3');
	updateElementCSS ('lbmn_typography_h3_fontsize', 'h3', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_h3_fontweight', 'h3', 'font-weight');
	updateElementCSS ('lbmn_typography_h3_lineheight', 'h3', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_h3_marginbottom', 'h3', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_h3_color', 'h3', 'color');

	// Heading 4
	updateElementFont('lbmn_typography_h4_font', 'h4');
	updateElementCSS ('lbmn_typography_h4_fontsize', 'h4', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_h4_fontweight', 'h4', 'font-weight');
	updateElementCSS ('lbmn_typography_h4_lineheight', 'h4', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_h4_marginbottom', 'h4', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_h4_color', 'h4', 'color');

	// Heading 5
	updateElementFont('lbmn_typography_h5_font', 'h5');
	updateElementCSS ('lbmn_typography_h5_fontsize', 'h5', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_h5_fontweight', 'h5', 'font-weight');
	updateElementCSS ('lbmn_typography_h5_lineheight', 'h5', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_h5_marginbottom', 'h5', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_h5_color', 'h5', 'color');

	// Heading 6
	updateElementFont('lbmn_typography_h6_font', 'h6');
	updateElementCSS ('lbmn_typography_h6_fontsize', 'h6', 'font-size', '', 'px');
	updateElementCSS ('lbmn_typography_h6_fontweight', 'h6', 'font-weight');
	updateElementCSS ('lbmn_typography_h6_lineheight', 'h6', 'line-height', '', 'px');
	updateElementCSS ('lbmn_typography_h6_marginbottom', 'h6', 'margin-bottom', '', 'px');
	updateElementCSS ('lbmn_typography_h6_color', 'h6', 'color');


	/**
	* ----------------------------------------------------------------------
	* Call to action area
	*/

	// check state on load and show/hide .notification-panel based on state
	jQuery(document).ready(function ($) {
		if ( $('.calltoaction-area').data('stateonload') == ''  ) {
			$( '.calltoaction-area' ).css( 'display', 'none' );
		}
	});

	// Enable/Disable switch
	updateDisplayProperty ('lbmn_calltoaction_switch', '.calltoaction-area');
	updateElementCSS ('lbmn_calltoaction_height', '.calltoaction-area', 'height', '', 'px');
	updateElementCSS ('lbmn_calltoaction_height', '.calltoaction-area', 'line-height', '', 'px');
	updateElementText ('lbmn_calltoaction_message', '.calltoaction-area__message'); // update message text
	updateElementFont ('lbmn_calltoaction_font', '.calltoaction-area__content');
	updateElementCSS ('lbmn_calltoaction_fontsize', '.calltoaction-area__content', 'font-size');
	updateElementCSS ('lbmn_calltoaction_fontweight', '.calltoaction-area__content', 'font-weight');
	updateElementCSS ('lbmn_calltoaction_backgroundcolor', '.calltoaction-area', 'background-color');
	updateElementCSS ('lbmn_calltoaction_textcolor', '.calltoaction-area, html .calltoaction-area *', 'color');
	updateElementCSS ('lbmn_calltoaction_backgroundcolor_hover', '.calltoaction-area:hover', 'background-color');
	updateElementCSS ('lbmn_calltoaction_textcolor_hover', '.calltoaction-area:hover, html .calltoaction-area:hover *', 'color');



} )( jQuery );