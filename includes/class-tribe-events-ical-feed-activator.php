<?php

/**
 * Fired during plugin activation
 *
 * @link       derozier.me
 * @since      1.0.0
 *
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/includes
 * @author     Dan Derozier <dan@derozier.me>
 */
class Tribe_Events_Ical_Feed_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( !class_exists('Tribe__Events__Main') ) {
			die('The plugin "Tribe Events iCal Feed" requires plugin "The Events Calendar" to be activated.');
		}

    global $wp_rewrite;
    $wp_rewrite->flush_rules();

	}

}
