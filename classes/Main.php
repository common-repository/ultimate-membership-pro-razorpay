<?php
/*
 * Run on public section
 */
namespace UmpRzr;

class Main
{
		/**
		 * @var array
		 */
		private $addOnSettings				= [];
		/**
		 * @var string
		 */
		private $view 								= null;

		/**
		 * @param array
		 * @param string
		 * @return none
		 */
    public function __construct( $addOnSettings=[], $viewObject=null )
    {
				$this->addOnSettings 	= $addOnSettings;
				$this->view 					= $viewObject;

	    	// check if magic feat is active filter ...
       	add_filter( 'ihc_is_magic_feat_active_filter', [ $this, 'isMagicFeatActive' ], 1, 2 );

				if ( !get_option( $this->addOnSettings['slug'] . '-enabled' ) ){
						return;
				}


				add_filter( 'ihc_payment_gateway_box_status', [ $this, 'paymentGatewayBoxStatus' ], 1, 2 );

				add_filter( 'ihc_payment_gateways_list', [ $this, 'paymentGatewaysList'], 1, 1 );

				add_filter( 'ihc_payment_gateway_create_payment_object', [ $this, 'createPaymentObject'], 1, 2 );

				add_filter( 'ihc_payment_gateway_status', [ $this, 'isPaymentAvailable' ], 1, 2 );

				add_filter( 'ihc_filter_payment_logo', [ $this, 'logo' ], 1, 2 );

    }

		/**
		 * @param none
		 * @return none
		 */
		public function styleAndScripts()
		{
				wp_enqueue_style( $this->addOnSettings['slug'] . '-public-style', $this->addOnSettings['dir_url'] . 'assets/css/public.css' );
				wp_enqueue_script( $this->addOnSettings['slug'] . '-public-js', $this->addOnSettings['dir_url'] . 'assets/js/public.js', [], null );
		}


		/**
		 * @param bool
 		 * @param string
		 * @return bool
		 */
    public function isMagicFeatActive( $isActive=false, $type='' )
    {
        if ( $this->addOnSettings['slug'] != $type ){
            return $isActive;
        }
        // check if is active ...
        $settings = ihc_return_meta_arr( $this->addOnSettings['slug'] );
        if ( !empty( $settings[ $this->addOnSettings['slug'] . '-enabled'] ) ){
            return true;
        }
        return false;
    }


		/**
		 * Return payment status for the box in payments services. admin.php?page=ihc_manage&tab=payment_settings
		 * The array must contain:
		 * 'active' => '{your-payment-slug}-active' ( this is a css class )
		 * 'status' => 0 or 1
		 * 'settings' => 'Completed' or 'Uncompleted'
		 * @param array
		 * @param string
		 * @return array
		 */
		public function paymentGatewayBoxStatus( $status=[], $paymentType='' )
		{
				if ( $paymentType != $this->addOnSettings['slug'] ){
						return $status;
				}
				$status = [
										'active'			=> '',
										'status'			=> 0,
										'settings'		=> 'Uncompleted',
				];

				$settings = ihc_return_meta_arr( 'ump_rzr' );
				if ( !empty( $settings['ump_rzr-secret'] ) && !empty( $settings['ump_rzr-key'] )) {
						$status['settings'] = 'Completed';
				}
				if ( !empty($settings['ump_rzr-enabled']) ){
						$status['active'] = $this->addOnSettings['slug'].'-active';
						$status['status'] = 1;
				}
				return $status;
		}

		/**
		 * Add this payment gateway into all ump payment gateways lists.
		 * @param array
		 * @return array
		 */
		public function paymentGatewaysList( $list=[] )
		{
				$list[ $this->addOnSettings['slug'] ] = esc_html__( 'RazorPay', 'ump_rzr' );
				return $list;
		}

		/**
		 * Create a instance of payment gateway object. It's used in DoPayment class and indeed-membership-pro.php webhook section.
		 * This method must return an object that extends \Indeed\Ihc\Gateways\PaymentAbstract
		 * @param object
		 * @param string
		 * @return object
		 */
		public function createPaymentObject( $object=null, $paymentType='' )
		{
				if ( $paymentType != $this->addOnSettings['slug'] ){
						return $object;
				}
				if ( class_exists( '\UmpRzrPro\RazorPay' ) ){
						// pro version
						return new \UmpRzrPro\RazorPay();
				}
				return new \UmpRzr\RazorPay();
		}

		/**
		 * Return a URL to your payment gateway logo. It will be used on public section.
		 * @param string
		 * @param string
		 * @return string
		 */
		public function logo( $imageUrl='', $paymentType='' )
		{
				if ( $paymentType != $this->addOnSettings['slug'] ){
						return $imageUrl;
				}
				return UMP_RZR_URL . 'assets/images/razorpay.png';
		}

		/**
		 * Check if the payment if enabled and all the credentials are completed.
 		 * @param bool
		 * @return bool
		 */
		public function isPaymentAvailable( $status=false, $type='' )
		{
				if ( $type != $this->addOnSettings['slug'] ){
						return $status;
				}
				$settings = ihc_return_meta_arr( $this->addOnSettings['slug'] );
				if ( !empty($settings['ump_rzr-enabled']) && $settings['ump_rzr-secret'] && !empty( $settings['ump_rzr-key'] )) {
						return true;
				}
				return false;
		}

}
