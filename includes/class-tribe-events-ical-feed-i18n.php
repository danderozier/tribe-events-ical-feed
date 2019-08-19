<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       derozier.me
 * @since      1.0.0
 *
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/includes
 * @author     Dan Derozier <dan@derozier.me>
 */
class Tribe_Events_Ical_Feed_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tribe-events-ical-feed',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
