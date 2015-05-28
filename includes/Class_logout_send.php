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
		add_action( "woocommerce_checkout_order_processed", array($this, 'update_stock_on_checkout_nopayment') );
		//add_action( "woocommerce_payment_complete", array($this, 'update_stock_on_checkout') );
		//Stop woocommerce from reducing stock amounts
		//add_filter( 'woocommerce_payment_complete_reduce_order_stock', '__return_false' );
	}

	/**
	 *
	 */
	public function update_stock_on_checkout($order_id){

		die();
	}
	/**
	*
	*/
	public function update_stock_on_checkout_nopayment($order_id){
		global $woocommerce, $wpdb;

		// order object (optional but handy)

		$order 			= new WC_Order( $order_id );
		$items 			= $order->get_items();
		$varation_ids 	= array();



			var_dump($items);

		//return false;
		die();
	}
}
