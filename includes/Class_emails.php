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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A custom ticket Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Simple_ticket_Order_Email extends WC_Email {

      /**
      * Set email defaults
      *
      * @since 0.1
      */
     public function __construct() {
         // set ID, this simply needs to be a unique name
         $this->id = 'wc_ticket_order';

         // this is the title in WooCommerce Email settings
         $this->title = 'Tickets Order';

         //home/dancenergyjail/home/dancenergy/public_html/sandbox/wp-content/plugins/woocommerce/templates//home/dancenergyjail/home/dancenergy/public_html/sandbox/wp-content/plugins/ticket_plugin/includes/templates/emails/customer-completed-order.php
         // this is the description in WooCommerce email settings
         $this->description = 'Tickets Order Notification emails are sent when a customer places an order with a ticket.';

         // these are the default heading and subject lines that can be overridden using the settings
         $this->heading = 'Your Tickets';
         $this->subject = 'Your Tickets';

         // these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
         $this->template_base  = plugin_dir_path(__FILE__);
         $this->template_html  = 'templates/emails/customer-completed-order-ticket.php';
         $this->template_plain = 'templates/emails/plain/customer-completed-order-ticket.php';
         // Trigger on new paid orders
         //backend order page hook
         add_action('woocommerce_after_resend_order_email',  array( $this, 'trigger' ), 1);
        //Frontend hook after the order status changes to completed
         add_action('woocommerce_order_status_completed_notification',  array( $this, 'trigger' ), 1);


                 		// For WooCommerce 2.0


         // Call parent constructor to load any other defaults not explicity defined here
         parent::__construct();


         // this sets the recipient to the settings defined below in init_form_fields()
           $this->recipient = $this->get_option( 'recipient' );

           // if none was entered, just use the WP admin email as a fallback
           if ( ! $this->recipient )
               $this->recipient = get_option( 'admin_email' );
     }
     /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 0.1
     * @param int $order_id
     */
    public function trigger( $order_id ) {
        global $woocommerce;

        // bail if no order ID is present
        if ( ! $order_id )
            return;


        // setup order object
        $this->object = new WC_Order( $order_id );

        //check if is downloadbale
        if(!$this->object->has_downloadable_item()){
          return;
        }
        // replace variables in the subject/headings
        $this->find[] = '{order_date}';
        $this->replace[] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );

        $this->find[] = '{order_number}';
        $this->replace[] = $this->object->get_order_number();
        //To get the dir, use: dirname($path)
  			require_once('fpdf/fpdf.php');
  			require_once('fpdi/fpdi.php');


        if ( ! $this->is_enabled() || ! $this->get_recipient() ){
            return;
        }

            $upload_dir = wp_upload_dir(); // Array of key => value pairs
            //cosntruct ticket temp dir
            $tickettemp = $upload_dir['basedir'].'/tickets_temp';

            if (!file_exists($tickettemp)) {

                mkdir($tickettemp, 0755, true);

            }
        		$items 			    = $this->object->get_items();
        		$varation_ids 	= array();
            $metaorder      = get_post_meta( $this->object->id );
            $emaailid       = $this->object->billing_email;
            $downloadlinks  = array();

            $i = 0;


            foreach($items as $key => $value){
              $x = 1;
              $getprod        = $this->object->get_product_from_item($value);

              $getdownload    = $this->object->get_item_downloads($value);


              if($getprod->product_type != 'ticket'){
                break;
              }
              if (empty($getdownload)) {
                break;
              }
              $qty            = intval($value['qty']);


              while($x <= $qty) {

                    $x++;
                    foreach ($getdownload as $keysub => $valuesub) {

                      $downlink           = $valuesub['file'];

                    }

                      $path               = $_SERVER['DOCUMENT_ROOT'] . parse_url($downlink, PHP_URL_PATH);
                      $pdfout             = $tickettemp . '/ticket_'. $this->object->id .'' .$value['product_id'] .''.$x .'.pdf';
                      $downloadlinks[$i]  = $pdfout;

                      $pagenum = 1;
                      $pdf =  new FPDI();
                    	$pdf->AddPage();


                      //Set the source PDF file
                      $pagecount = $pdf->setSourceFile($path);

                      //Import the first page of the file
                      $tpl = $pdf->importPage($pagenum);
                      //Use this page as template
                      $pdf->useTemplate($tpl);

                      $getfooterY = $pdf->h -35;
                      //Select Arial italic 8
                      $pdf->SetY($getfooterY);
                      $pdf->SetFont('Arial','I',10);
                      $pdf->Cell(0, 10, 'Ticket id: ' .  $this->object->id .'' .$value['product_id'] .''.$x , 0, 0, 'C');


                      $pdf->Output($pdfout, "F");




                    $i++;
                }

            }
/*
            $email_class = new WC_Email();
            remove_action( 'woocommerce_after_resend_order_email', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
            echo "<pre>";
              var_dump( $this->emails );
            echo "</pre>";
              var_dump( $downloadlinks );
              var_dump( $items );
              var_dump( $emaailid);
              var_dump( $this->object );
*/

        // woohoo, send the email!

        $this->send( $this->get_recipient().', '. $this->object->billing_email, $this->get_subject() , $this->get_content(), $this->get_headers(), $downloadlinks );
        //Delete the temp tickets
        foreach( $downloadlinks as $key => $value){

          chmod($value, 0777);

          if (file_exists($value)) {
              unlink($value);

          }

        }
        //die();
    }
    /**
     * get_content_html function.
     *
     * @since 0.1
     * @return string
     */
    public function get_content_html() {
        ob_start();
        woocommerce_get_template( $this->template_html, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading()
        ) );
        return ob_get_clean();
    }


    /**
     * get_content_plain function.
     *
     * @since 0.1
     * @return string
     */
    public function get_content_plain() {
        ob_start();
        woocommerce_get_template( $this->template_plain, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading()
        ) );
        return ob_get_clean();
    }
    /**
     * Initialize Settings Form Fields
     *
     * @since 0.1
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'yes'
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'recipient'  => array(
                'title'       => 'Recipient(s)',
                'type'        => 'text',
                'description' => sprintf( 'Enter recipients to recive a copy of the tickets.. maybe a catchall email.<br/>(comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => 'Email Heading',
                'type'        => 'text',
                'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
          				'title'       => 'Email type',
          				'type'        => 'select',
          				'description' => 'Choose which format of email to send.',
          				'default'     => 'html',
          				'class'       => 'email_type',
          				'options'     => array(
          					'plain'	    => __( 'Plain text', 'woocommerce' ),
          					'html' 	    => __( 'HTML', 'woocommerce' ),
          					'multipart' => __( 'Multipart', 'woocommerce' ),
          				)
          			)
        );
    }
} // end \WC_Expedited_Order_Email class
