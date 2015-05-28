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
 class WC_Product_Ticket extends WC_Product{

      //static $product = $this;

     /**
      * __construct function.
      *
      * @access public
      * @param mixed $product
      */
     public function __construct( $product = array() ) {

         $this->virtual         = 'yes';
         $this->downloadable    = 'yes';
         $this->manage_stock    = 'yes';

         $this->product_type  = 'ticket';
         add_filter('woocommerce_product_data_tabs', array($this, 'remove_tabs'), 10, 1);
         add_filter( 'product_type_options', array($this, 'add_virtual'),10 , 1);
         $getdir = '';


         parent::__construct($product);
     }
     /**
      *
      *Add virtual/download picker
      *
      */
     public function add_virtual($product){

       $product['virtual']['wrapper_class']      = $product['virtual']['wrapper_class']. " show_if_ticket";

       $product['downloadable']['wrapper_class'] = $product['downloadable']['wrapper_class']. " show_if_ticket";


       return $product;
     }
     /***
        *Show or hide product tabs
        */
     public function remove_tabs($tabs) {

         /**
          * The available tab array keys are:
          *
          * general
          * inventory
          * shipping
          * linked_product
          * attribute
          * variations
          * advanced
          */


          array_push($tabs['inventory']['class'], "show_if_ticket" );
          array_push($tabs['attribute']['class'], "hide_if_ticket" );
          array_push($tabs['shipping']['class'], "hide_if_ticket" );

/*
          echo "<pre>";
            var_dump($tabs);
          echo "</pre>";
*/
          //unset($tabs['shipping']);
         return $tabs;
     }
  /**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}
	/**
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'woocommerce' ) : __( 'Read More', 'woocommerce' );
		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}
	/**
	 * Get the title of the post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_title() {
		$title = $this->post->post_title;
		if ( $this->get_parent() > 0 ) {
			$title = get_the_title( $this->get_parent() ) . ' &rarr; ' . $title;
		}
		return apply_filters( 'woocommerce_product_title', $title, $this );
	}
	/**
	 * Sync grouped products with the children lowest price (so they can be sorted by price accurately).
	 *
	 * @access public
	 * @return void
	 */
	public function grouped_product_sync() {
		if ( ! $this->get_parent() ) return;
		$children_by_price = get_posts( array(
			'post_parent'    => $this->get_parent(),
			'orderby'        => 'meta_value_num',
			'order'          => 'asc',
			'meta_key'       => '_price',
			'posts_per_page' => 1,
			'post_type'      => 'product',
			'fields'         => 'ids'
		));
		if ( $children_by_price ) {
			foreach ( $children_by_price as $child ) {
				$child_price = get_post_meta( $child, '_price', true );
				update_post_meta( $this->get_parent(), '_price', $child_price );
			}
		}
		delete_transient( 'wc_products_onsale' );
		do_action( 'woocommerce_grouped_product_sync', $this->id, $children_by_price );
	}

 }
 new WC_Product_Ticket();


  add_filter( 'product_type_selector', 'add_ticket_type_product' );

  function add_ticket_type_product( $types ){
      $types[ 'ticket' ] = __( 'Ticket' );
      return $types;
  }

  define( 'YOUR_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

  add_action('woocommerce_ticket_add_to_cart', 'add_to_cart',30);

  function add_to_cart() {
      //wc_get_template( 'single-product/add-to-cart/ticket.php', array(), $this->plugin_path() . '/templates/', $default_path = '' );

      wc_get_template( 'single-product/add-to-cart/ticket.php', array(),  '', YOUR_TEMPLATE_PATH );

  }
