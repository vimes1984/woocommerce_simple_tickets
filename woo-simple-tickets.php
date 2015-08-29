<?php
/**
 * Woo Simple Tickets
 *
 * Sell simple tickts as virtual products in Woo
 *
 * @package   woo-simple-tickets
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-7-2015 BAWD
 *
 * @wordpress-plugin
 * Plugin Name: Woo Simple Tickets
 * Plugin URI:  http://buildawebdoctor.com
 * Description: Sell simple tickts as virtual products in Woo
 * Version:     1.0.10
 * Author:      vimes1984
 * Author URI:  http://buildawebdoctor.com
 * Text Domain: woo-simple-tickets-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if (!defined("WPINC")) {
	die;
}

require_once(plugin_dir_path(__FILE__) . "WooSimpleTickets.php");
require_once(plugin_dir_path(__FILE__) . "includes/Class_update_ticket.php");
require_once(plugin_dir_path(__FILE__) . "includes/Class_ticket_tab.php");
require_once(plugin_dir_path(__FILE__) . "includes/ClassCheckoutSend.php");

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array("WooSimpleTickets", "activate"));
register_deactivation_hook(__FILE__, array("WooSimpleTickets", "deactivate"));

WooSimpleTickets::get_instance();
UpdateTicketProduct::get_instance();
AddProducttab::get_instance();
ClassCheckoutSend::get_instance();

/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function add_ticket_order_woocommerce_email( $email_classes ) {

    // include our custom email class
		require_once( plugin_dir_path(__FILE__) .  'includes/Class_emails.php' );

    // add the email class to the list of email classes that WooCommerce loads
    $email_classes['WC_Simple_ticket_Order_Email'] = new WC_Simple_ticket_Order_Email();

    return $email_classes;

}
add_filter( 'woocommerce_email_classes', 'add_ticket_order_woocommerce_email' );


function myplugin_plugin_path() {

  // gets the absolute path to this plugin directory

  return untrailingslashit( plugin_dir_path( __FILE__ ) );

}



add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );



function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {

  global $woocommerce;



  $_template = $template;

  if ( ! $template_path ) $template_path = $woocommerce->template_url;

  $plugin_path  		= plugin_dir_path(__FILE__) . '/includes/templates/';
	$plugin_path_two	= plugin_dir_path(__FILE__) . '/includes/';


  // Look within passed path within the theme - this is priority

  $template = locate_template(

    array(

      $template_path . $template_name,

      $template_name

    )

  );



  // Modification: Get the template from this plugin, if it exists

  if ( ! $template && file_exists( $plugin_path . $template_name ) ){

		$template = $plugin_path . $template_name;

	}else if(!$template && file_exists( $plugin_path_two . $template_name )){

    $template = $plugin_path_two . $template_name;
	}





  // Use default template

  if ( ! $template )

    $template = $_template;



  // Return what we found

  return $template;

}
