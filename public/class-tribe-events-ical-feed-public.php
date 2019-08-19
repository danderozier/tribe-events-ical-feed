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

		$domain = parse_url($_SERVER['HTTP_HOST']);
		$category = get_query_var('category', false);

		$query = [
			'start_date' => '2019-01-01',
			'end_date' => '2021-08-01',
			'posts_per_page' => -1,
		];

		if ( $category ) {
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
		$icalobj = new ZCiCal();

		foreach($events as $event) {
			$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

			$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $event->post_title));

			$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event->EventStartDate)));
			$eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event->EventEndDate)));
			$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));

			$location = $tec->fullAddressString( $event->ID );
			if ( ! empty( $location ) ) {
        $str_location = str_replace( array( ',', "\n" ), array( '\,', '\n' ), html_entity_decode( $location, ENT_QUOTES ) );
        $eventobj->addNode(new ZCiCalDataNode("LOCATION:" . $str_location));
      }

      $eventobj->addNode(new ZCiCalDataNode("URL:" . $event->guid));
    	$event_cats = (array) wp_get_object_terms( $event->ID, Tribe__Events__Main::TAXONOMY, array( 'fields' => 'names' ) );
      if ( ! empty( $event_cats ) ) {
          $eventobj->addNode(new ZCiCalDataNode('CATEGORIES:' . html_entity_decode( join( ',', $event_cats ), ENT_QUOTES )));
      }

      $uid = $event->post_name . '-' . date('Y-m-d-H-i-s') . '@' . $domain['path'];
      $eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));
      $eventobj->addNode(new ZCiCalDataNode("CREATED:" . ZCiCal::fromSqlDateTime($event->post_date)));
      $eventobj->addNode(new ZCiCalDataNode("LAST-MODIFIED:" . ZCiCal::fromSqlDateTime($event->post_modified)));

      $eventobj->addNode(new ZCiCalDataNode("DESCRIPTION:" . ZCiCal::formatContent(
      	$event->post_content
      )));
		}

		echo $icalobj->export();

	}

}
