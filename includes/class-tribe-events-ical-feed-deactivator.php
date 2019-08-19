<?php

/**
 * Fired during plugin deactivation
 *
 * @link       derozier.me
 * @since      1.0.0
 *
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/includes
 * @author     Dan Derozier <dan@derozier.me>
 */
class Tribe_Events_Ical_Feed_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

    global $wp_rewrite;
    $wp_rewrite->flush_rules();

	}

}
