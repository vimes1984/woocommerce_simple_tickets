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
class AddProducttab{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";
  /**
   *
   *
   *plugin text domain
   *
   */
 	const TEXT_DOMAIN = 'woo-simple-tickets';
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

    // backend stuff
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_write_panel_tab' ) );
		add_action( 'woocommerce_product_write_panels',     array( $this, 'product_write_panel' ) );
		add_action( 'woocommerce_process_product_meta',     array( $this, 'product_save_data' ), 10, 2 );
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
   * Adds the panel to the Product Data postbox in the product interface
   */
  public function product_write_panel() {
    global $post;
    // the product

    // pull the custom tab data out of the database
    $tab_data = maybe_unserialize( get_post_meta( $post->ID, 'frs_woo_product_tabs', true ) );

    if ( empty( $tab_data ) ) {
      $tab_data[] = array( 'ticket_title' => '', 'ticket_content' => '' );
    }

    foreach ( $tab_data as $tab ) {
      // display the custom tab panel
      echo '<div id="woocommerce_product_tabs_lite" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';

      woocommerce_wp_text_input( array(
              'id' 					=> '_wc_custom_product_tabs_lite_tab_title_ticket',
              'label' 			=> __( 'Ticket Title', self::TEXT_DOMAIN ),
               'description' => __( '', self::TEXT_DOMAIN ),
              'value' 			=> $tab['ticket_title']
              )
      );
      echo "<h3 class='text-center'>Pdf ticket content</h3>";
      wp_editor( $tab['ticket_content'], "htmleditor", array('tinymce' => false) );
      echo '
          <p>Supported tags are</p>
            <pre  data-lang="HTML">
            <code>
                &lt;br/&gt; and &lt;p&gt;
                  &lt;b&gt;, &lt;i&gt; and &lt;u&gt;
                  &lt;img&gt; (src and width (or height) are mandatory)
                  &lt;a&gt; (href is mandatory)
                  &lt;font&gt;: possible attributes are
                color: hex color code
                face: available fonts are: arial, times, courier, helvetica, symbol
              </code>
            </pre>
              <br>

            ';

      echo '</div>';
    }
  }


  /**
  * Saves the data inputed into the product boxes, as post meta data
  * identified by the name 'frs_woo_product_tabs'
  *
  * @param int $post_id the post (product) identifier
  * @param stdClass $post the post (product)
  */
  public function product_save_data( $post_id, $post ) {

    $tab_title = stripslashes( $_POST['_wc_custom_product_tabs_lite_tab_title_ticket'] );
    $tab_content = $_POST['htmleditor'];

    if ( empty( $tab_title ) && empty( $tab_content ) && get_post_meta( $post_id, 'frs_woo_product_tabs', true ) ) {
      // clean up if the custom tabs are removed
      delete_post_meta( $post_id, 'frs_woo_product_tabs' );
    } elseif ( ! empty( $tab_title ) || ! empty( $tab_content ) ) {
      $tab_data = array();

      $tab_id = '';
      if ( $tab_title ) {
        if ( strlen( $tab_title ) != strlen( utf8_encode( $tab_title ) ) ) {
          // can't have titles with utf8 characters as it breaks the tab-switching javascript
          $tab_id = "tab-custom";
        } else {
          // convert the tab title into an id string
          $tab_id = strtolower( $tab_title );
          $tab_id = preg_replace( "/[^\w\s]/", '', $tab_id );
          // remove non-alphas, numbers, underscores or whitespace
          $tab_id = preg_replace( "/_+/", ' ', $tab_id );
          // replace all underscores with single spaces
          $tab_id = preg_replace( "/\s+/", '-', $tab_id );
          // replace all multiple spaces with single dashes
          $tab_id = 'tab-' . $tab_id;
          // prepend with 'tab-' string
        }
      }

      // save the data to the database
      $tab_data[] = array( 'ticket_title' => $tab_title, 'id' => $tab_id, 'ticket_content' => $tab_content );
      update_post_meta( $post_id, 'frs_woo_product_tabs', $tab_data );
    }
  }

  /**
  * Adds a new tab to the Product Data postbox in the admin product interface
  */
  public function product_write_panel_tab() {
    echo "<li class=\"product_tabs_lite_tab show_if_ticket\"><a href=\"#woocommerce_product_tabs_lite\">" . __( 'Ticket Details', self::TEXT_DOMAIN ) . "</a></li>";
  }

}
