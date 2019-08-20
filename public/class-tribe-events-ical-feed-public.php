<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       derozier.me
 * @since      1.0.0
 *
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tribe_Events_Ical_Feed
 * @subpackage Tribe_Events_Ical_Feed/public
 * @author     Dan Derozier <dan@derozier.me>
 */
class Tribe_Events_Ical_Feed_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tribe_Events_Ical_Feed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tribe_Events_Ical_Feed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tribe-events-ical-feed-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tribe_Events_Ical_Feed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tribe_Events_Ical_Feed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tribe-events-ical-feed-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register custom rewrite rules for iCal feed
	 *
	 * @since    1.0.0
	 */
	public function add_ical_feed() {

		add_feed('ical', array($this, 'ical_feed'));

	}

	/**
	 * /* Set the correct HTTP header for Content-type.
	 * @param  [type] $content_type [description]
	 * @param  [type] $type         [description]
	 * @return [type]               [description]
	 */
	public function ical_feed_content_type( $content_type, $type ) {

		if ( 'ical' === $type ) {
			return 'text/calendar';
		}
		return $content_type;

	}


	public function add_query_vars_filter( $vars ) {

		$vars[] = "category";
		return $vars;

	}


	static function ical_feed() {

		$feed_name = get_bloginfo('name');
		$domain = parse_url($_SERVER['HTTP_HOST']);
		$timezone = new \DateTimeZone( get_option('timezone_string') );
		$category = get_query_var('category', false);

		$query = [
			'start_date' => '2019-01-01',
			'end_date' => '2021-08-01',
			'posts_per_page' => -1,
		];

		if ( $category ) {
			$feed_name = $feed_name . ' - ' . ucwords(str_replace('-', ' ', $category));

			$query['tax_query'] = array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' => $category
				)
			);
		}

		$events = tribe_get_events($query);

		$tec = Tribe__Events__Main::instance();

		$vCalendar = new \Eluceo\iCal\Component\Calendar($domain);
		$vCalendar
			->setName($feed_name)
			->setMethod('PUBLISH')
			->setCalendarScale('GREGORIAN')
			->setCalendarColor('#FF0000')
			->setTimeZone(get_option('timezone_string'));

		foreach($events as $event) {
			$event_cats = (array) wp_get_object_terms( $event->ID, Tribe__Events__Main::TAXONOMY, array( 'fields' => 'names' ) );
			$event_location = $tec->fullAddressString( $event->ID );

			$vEvent = new \Eluceo\iCal\Component\Event();

			$vEvent
				->setSummary( $event->post_title )
				->setDescription( $event->post_content )
				->setDtStart( new \DateTime($event->EventStartDate, $timezone) )
				->setDtEnd( new \DateTime($event->EventEndDate, $timezone) )
				->setUrl( $event->guid )
				->setCreated( new \DateTime($event->post_date, $timezone) )
				->setModified( new \DateTime($event->post_modified, $timezone) );

			if ( ! empty( $event_cats ) ) {
				$vEvent->setCategories( $event_cats );
			}

			if ( ! empty( $event_location ) ) {
				$vEvent->setLocation( $event_location );
			}

			$vEvent->setUseTimezone(true);

			$vCalendar->addComponent($vEvent);
		}

		header('Content-Type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $feed_name . '.ics"');

		echo $vCalendar->render();

	}
}
