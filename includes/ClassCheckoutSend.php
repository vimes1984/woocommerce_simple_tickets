<?php
/**
 * Woo Simple Tickets
 *
 * @package   woo-simple-tickets
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-7-2015 BAWD
 */

/**
 * Woo Simple Tickets class.
 *
 * @package WooSimpleTickets
 * @author  vimes1984 <churchill.c.j@gmail.com>
 */
class ClassCheckoutSend{
  /**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";
	/** plugin text domain */
	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "woo-simple-tickets";

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		//
		add_action( "woocommerce_thankyou", array($this, 'update_stock_on_checkout_nopayment'), 10, 1  );
		//add_action( "woocommerce_payment_complete", array($this, 'update_stock_on_checkout') );
	}

  		/**
  		 * Return an instance of this class.
  		 *
  		 * @since     1.0.0
  		 *
  		 * @return    object    A single instance of this class.
  		 */
  		public static function get_instance() {

  			// If the single instance hasn"t been set, set it now.
  			if (null == self::$instance) {
  				self::$instance = new self;
  			}

  			return self::$instance;
  		}


	/**
	 *
	 */
	public function update_stock_on_checkout($order_id){
		/**NOT IN USE **/
	}
	/**
	*
	*/
	public function update_stock_on_checkout_nopayment($order_id){
		global $woocommerce, $wpdb;
		/**
		 * NOT IN USE
		 */
	}
}
