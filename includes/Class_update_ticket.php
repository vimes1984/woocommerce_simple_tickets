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
class UpdateTicketProduct{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";

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
		//run on product save

		add_action( 'save_post', array($this, 'save_ticket_data'), 10, 3 );



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
	 * Save post metadata when a post is saved.
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function save_ticket_data( $post_id, $post, $update ) {
			global $product;
	    /*
	     * In production code, $slug should be set only once in the plugin,
	     * preferably as a class property, rather than in each function that needs it.
	     */
	    $slug = 'product';

	    // If this isn't a 'product' post, don't update it.
	    if ( $slug != $post->post_type ) {
	        return;
	    }
			$getprod 					= get_product($post_id);
			//If it's not a ticket return aswell
			if($getprod->product_type != 'ticket'){
				return;

			}
			$getmeta = get_post_meta($post_id, '_downloadable_files', true);
			//Traverse the return array since we don't know the first key
			foreach($getmeta as $key => $value){
					$url = $value['file'];
			}

			$path = $_SERVER['DOCUMENT_ROOT'] . parse_url($url, PHP_URL_PATH);
			//To get the dir, use: dirname($path)
			require_once('fpdf/fpdf.php');
			require_once('fpdi/fpdi.php');
			//Get stuff to add to pdf :D
			$getmetaall = get_post_meta($post_id);
			$getcontent = get_post_meta($post_id, 'frs_woo_product_tabs', true);


			$i = 1;
			$pdf =  new FPDI();
		//	$pdf->AddPage();


			//Set the source PDF file
			//$pagecount = $pdf->setSourceFile($path);

			//Import the first page of the file
			//$tpl = $pdf->importPage($i);
			//Use this page as template
			//$pdf->useTemplate($tpl);

			#Print Hello World at the bottom of the page
			//Clear all
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetY(1);
			$pdf->SetFont('Arial','I',19);
			$pdf->Cell(0, $pdf->h-2, ' ', 0, 0, 'C', true);
			//Go to 1.5 cm from bottom
			$pdf->SetY(1);
			//Select Arial italic 8
			$pdf->SetFont('Arial','I',19);
			//Print centered cell with a text in it
			$pdf->Cell(0, 10, $post->post_title, 0, 0, 'C');
/*


			$pdf->SetY(10);
			$pdf->SetFont('Arial','I',16);
			$pdf->Cell(0, 10, gmdate("Y-m-d", $getmetaall["wpcf-event-start-date"][0]), 0, 0, 'C');

			$pdf->SetY(20);
			$pdf->SetFont('Arial','I',16);
			$pdf->Cell(0, 10, 'Start time: ' . $getmetaall["wpcf-event-start-time"][0], 0, 0, 'C');

			$pdf->SetY(27);
			$pdf->SetFont('Arial','I',16);
			$pdf->Cell(0, 10, 'End time: ' . $getmetaall["wpcf-event-end-time"][0], 0, 0, 'C');

			$pdf->SetY(1);
			$pdf->Image('http://dancenergy.zenutech.com/production/wp-content/uploads/2014/06/Logo.png', 5, 0, 33.78);
*/
			//Select Arial italic 8
			$pdf->SetY(20);
			$pdf->SetFont('Arial','I',15);
			$pdf->WriteHTML($getcontent[0]['ticket_content']);


			$pdf->Output($path, "F");
		/*
			echo "<pre>";
				var_dump( $getmetaall );
			echo "</pre>";
*/
			return;


	}


}
