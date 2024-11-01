<?php
/*
 * This class will run only on Admin section
 */
namespace UmpRzr\Admin;

class Main
{
    /**
		 * @var array
		 */
		private $addOnSettings			  = [];
		/**
		 * @var string
		 */
		private $view 								= null;

    /**
     * @param array
		 * @param object
     * @return none
     */
    public function __construct( $settings=[], $viewObject=null )
    {
        $this->addOnSettings 		= $settings;
				$this->view 						= $viewObject;
        // admin settings page
        add_filter( 'ihc_magic_feature_list', [ $this, 'addMagicFeatureItem' ], 1, 1 );
        // settings page
        add_action( 'ump_print_admin_page', [ $this, 'printSettingsPage' ], 1, 1 );
        // style & js stuff
        add_action( 'admin_enqueue_scripts', [ $this, 'styleAndScripts' ] );

				// extra actions on Admin settings page
				//add_action( 'ump_addon_action_before_print_admin_settings', [ $this, 'beforePrintSettings' ] );

				// box
				add_action( 'ihc_payment_gateway_box', [ $this, 'box' ], 1, 0 );

				add_action( 'ihc_payment_gateway_page', [ $this, 'paymentSettings' ],1, 1 );
    }

    /**
		 * @param array
		 * @return array
		 */
    public function addMagicFeatureItem( $items=[] )
    {
        $items[ $this->addOnSettings['slug'] ] = array(
                'label'						=> esc_html__( $this->addOnSettings['name'], 'ultimate-membership-pro-razorpay' ),
                'link' 						=> (defined('IHCACTIVATEDMODE') && IHCACTIVATEDMODE) ? admin_url('admin.php?page=ihc_manage&tab=' . $this->addOnSettings['slug'] ) : '',
                'icon'						=> 'icon-ump-razorpay', //'fa-' . $this->addOnSettings['slug'] . '-ihc',
                'extra_class' 		=> 'ihc-extra-extension-box iump-' . $this->addOnSettings['slug'] . '-box',
                'description'			=> $this->addOnSettings['description'],
                'enabled'					=> ihc_is_magic_feat_active( $this->addOnSettings['slug'] ),
        );

				if ( defined( 'UMP_RAZORPAY_PRO' ) && UMP_RAZORPAY_PRO ){
						// Pro Label if its case
						$items[ $this->addOnSettings['slug'] ]['label'] = esc_html__('Razorpay Pro', 'ultimate-membership-pro-razorpay');
				}
        return $items;
    }

    /**
		 * @param string
		 * @return none
		 */
    public function printSettingsPage( $tab='' )
    {
        if ( $tab != $this->addOnSettings['slug'] ){
            return;
        }
				do_action( 'ump_addon_action_before_print_admin_settings' );

				if ( isset( $_POST['ihc_save'] ) ){
        		ihc_save_update_metas( $this->addOnSettings['slug'] );//save update metas
				}

        $data = ihc_return_meta_arr( $this->addOnSettings['slug'] );
				$data['plugin_slug'] = $this->addOnSettings['slug'];
				$data['lang'] = $this->addOnSettings['slug'];
				$data['name'] = $this->addOnSettings['name'];
				$data['description'] = $this->addOnSettings['description'];
        $string = $this->view->setTemplate( $this->addOnSettings['dir_path'] . 'views/admin.php' )
                  			->setContentData( $data )
                  			->getOutput();

				$allowedHtml = [
					'a' 			=> [
													'href'		=> [],
													'target'	=> [],
					],
					'label' 	=> [ 'class' => [] ],
					'div' 		=> [ 'class' => [] ],
					'h4'			=> [],
					'h3'			=> [ 'class' => [] ],
					'h2'			=> [],
					'ul'			=> [ 'class' => [] ],
					'li'			=> [],
					'p'				=> [],
					'b'				=> [],
					'strong'	=> [],
					'code'		=> [],
					'span'		=> [ 'class'	=> [] ],
					'table'		=> [ 'class'	=> [] ],
					'tr'			=> [],
					'td'			=> [],
					'th'			=> [],
					'textarea'	=>[	'name'	=> [],
													'class'	=> [],
													'rows'	=> [],
													'cols'	=> [],
													'placeholder'	=> []
						],
					'input'		=> [
													'class' 			=> [],
													'id' 					=> [],
													'type' 				=> [],
													'name' 				=> [],
													'value' 			=> [],
													'onclick' 		=> [],
													'checked' 		=> []
					],
					'select'	=> [
													'name'				=> [],
													'class'				=> []
													],
					'option'		=> [ 'value' => [], 'selected' => [] ],
					'form'		=> [
													'action' => [],
													'method' => []
					],
	];
						echo wp_kses( $string, $allowedHtml );
    }

    /**
		 * @param none
		 * @return none
		 */
    public function styleAndScripts()
    {
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'ihc_manage' ){
            	wp_enqueue_style( $this->addOnSettings['slug'] . '-admin-style', $this->addOnSettings['dir_url'] . 'assets/css/admin.css' );
							wp_enqueue_style( $this->addOnSettings['slug'] . '-box-font-style', $this->addOnSettings['dir_url'] . 'assets/css/fontello.css' );

						 wp_enqueue_script( $this->addOnSettings['slug'] . '-admin-js', $this->addOnSettings['dir_url'] . 'assets/js/admin.js', [], null );
        }
    }

		/**
		 * @param none
		 * @return none
		 */
		public function beforePrintSettings()
		{
				// Add your custom code and functionality here
		}

		/**
		 * Print a box into Payment Services section.
		 * @param none
		 * @return string
		 */
		public function box()
		{
			$data = ihc_return_meta_arr( $this->addOnSettings['slug'] );
			$data['slug'] = $this->addOnSettings['slug'];
			$data['pay_stat'] = ihc_check_payment_status( 'ump_rzr' );
			$string = $this->view->setTemplate( $this->addOnSettings['dir_path'] . 'views/box.php' )
											->setContentData( $data )
											->getOutput();

			$allowedHtml = [
				'a' 			=> [
												'href'		=> [],
												'target'	=> [],
				],
				'label' 	=> [ 'class' => [] ],
				'div' 		=> [ 'class' => [] ],
				'h4'			=> [],
				'h3'			=> [ 'class' => [] ],
				'h2'			=> [],
				'ul'			=> [ 'class' => [] ],
				'li'			=> [],
				'p'				=> [],
				'b'				=> [],
				'strong'	=> [],
				'code'		=> [],
				'span'		=> [ 'class'	=> [] ],
				'table'		=> [ 'class'	=> [] ],
				'tr'			=> [],
				'td'			=> [],
				'th'			=> [],
				'textarea'	=>[	'name'	=> [],
												'class'	=> [],
												'rows'	=> [],
												'cols'	=> [],
												'placeholder'	=> []
					],
				'input'		=> [
												'class' 			=> [],
												'id' 					=> [],
												'type' 				=> [],
												'name' 				=> [],
												'value' 			=> [],
												'onclick' 		=> [],
												'checked' 		=> []
				],
				'select'	=> [
												'name'				=> [],
												'class'				=> []
												],
				'option'		=> [ 'value' => [], 'selected' => [] ],
				'form'		=> [
												'action' => [],
												'method' => []
											],
							];
					echo wp_kses( $string, $allowedHtml );
		}

		/**
		 * Payment settings page in Payment Services section.
		 * @param string
		 * @return string
		 */
		public function paymentSettings( $tab='' )
		{
				if ( $tab !== 'ump_rzr' ){
						return;
				}
			do_action( 'ump_addon_action_before_print_admin_settings' );
			if ( isset( $_POST['ihc_save'] ) ){
					ihc_save_update_metas( $this->addOnSettings['slug'] );//save update metas
			}
			$data = ihc_return_meta_arr( $this->addOnSettings['slug'] );
			$data['plugin_slug'] = $this->addOnSettings['slug'];
			$data['lang'] = $this->addOnSettings['slug'];
			$data['name'] = $this->addOnSettings['name'];
			$data['description'] = $this->addOnSettings['description'];
			$string = $this->view->setTemplate( $this->addOnSettings['dir_path'] . 'views/admin.php' )
											->setContentData( $data )
											->getOutput();

			$allowedHtml = [
				'a' 			=> [
												'href'		=> [],
												'target'	=> [],
				],
				'label' 	=> [ 'class' => [] ],
				'div' 		=> [ 'class' => [] ],
				'h4'			=> [],
				'h3'			=> [ 'class' => [] ],
				'h2'			=> [],
				'ul'			=> [ 'class' => [] ],
				'li'			=> [],
				'p'				=> [],
				'b'				=> [],
				'strong'	=> [],
				'code'		=> [],
				'span'		=> [ 'class'	=> [] ],
				'table'		=> [ 'class'	=> [] ],
				'tr'			=> [],
				'td'			=> [],
				'th'			=> [],
				'textarea'	=>[	'name'	=> [],
												'class'	=> [],
												'rows'	=> [],
												'cols'	=> [],
												'placeholder'	=> []
					],
				'input'		=> [
												'class' 			=> [],
												'id' 					=> [],
												'type' 				=> [],
												'name' 				=> [],
												'value' 			=> [],
												'onclick' 		=> [],
												'checked' 		=> []
				],
				'select'	=> [
												'name'				=> [],
												'class'				=> []
												],
				'option'		=> [ 'value' => [], 'selected' => [] ],
				'form'		=> [
												'action' => [],
												'method' => []
											],
							];
					echo wp_kses( $string, $allowedHtml );
		}

}
