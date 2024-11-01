<?php
namespace UmpRzr;

class RazorPay extends \Indeed\Ihc\Gateways\PaymentAbstract
{

    protected $paymentType                    = 'ump_rzr'; // slug. cannot be empty.

    protected $paymentRules                   = [
                'canDoRecurring'						                  => true, // does current payment gateway supports recurring payments.
                'canDoTrial'							                    => false, // does current payment gateway supports trial subscription
                'canDoTrialFree'						                  => false, // does current payment gateway supports free trial subscription
                'canApplyCouponOnRecurringForFirstPayment'		=> false, // if current payment gateway support coupons on recurring payments only for the first transaction
                'canApplyCouponOnRecurringForFirstFreePayment'=> false, // if current payment gateway support coupons with 100% discount on recurring payments only for the first transaction.
                'canApplyCouponOnRecurringForEveryPayment'	  => false, // if current payment gateway support coupons on recurring payments for every transaction
                'paymentMetaSlug'                             => 'ump_rzr', // payment gateway slug. exenple: paypal, stripe, etc.
                'returnUrlAfterPaymentOptionName'             => 'ump_rzr-return_page', // option name ( in wp_option table ) where it's stored the return URL after a payment is done.
                'returnUrlOnCancelPaymentOptionName'          => '', // option name ( in wp_option table ) where it's stored the return URL after a payment is canceled.
                'paymentGatewayLanguageCodeOptionName'        => '', // option name ( in wp_option table ) where it's stored the language code.
    ]; // some payment does not support all our features
    protected $intervalSubscriptionRules      = [
                'daysSymbol'               => 'daily',
                'weeksSymbol'              => 'weekly',
                'monthsSymbol'             => 'monthly',
                'yearsSymbol'              => 'yearly',
                'daysSupport'              => true,
                'daysMinLimit'             => 1,
                'daysMaxLimit'             => 90,
                'weeksSupport'             => true,
                'weeksMinLimit'            => 1,
                'weeksMaxLimit'            => 52,
                'monthsSupport'            => true,
                'monthsMinLimit'           => 1,
                'monthsMaxLimit'           => 24,
                'yearsSupport'             => true,
                'yearsMinLimit'            => 1,
                'yearsMaxLimit'            => 5,
                'maximumRecurrenceLimit'   => 52, // leave this empty for unlimited
                'minimumRecurrenceLimit'   => 2,
                'forceMaximumRecurrenceLimit'   => false,
    ];
    protected $intervalTrialRules             = [
                              'daysSymbol'               => '',
                              'weeksSymbol'              => '',
                              'monthsSymbol'             => '',
                              'yearsSymbol'              => '',
                              'supportCertainPeriod'     => false,
                              'supportCycles'            => false,
                              'cyclesMinLimit'           => 1,
                              'cyclesMaxLimit'           => '',
                              'daysSupport'              => true,
                              'daysMinLimit'             => 1,
                              'daysMaxLimit'             => 90,
                              'weeksSupport'             => true,
                              'weeksMinLimit'            => 1,
                              'weeksMaxLimit'            => 52,
                              'monthsSupport'            => true,
                              'monthsMinLimit'           => 1,
                              'monthsMaxLimit'           => 24,
                              'yearsSupport'             => true,
                              'yearsMinLimit'            => 1,
                              'yearsMaxLimit'            => 5,
    ];

    protected $stopProcess                    = false;
    protected $inputData                      = []; // input data from user
    protected $paymentOutputData              = [];
    protected $paymentSettings                = []; // api key, some credentials used in different payment types

    protected $paymentTypeLabel               = 'RazorPay'; // label of payment
    protected $redirectUrl                    = ''; // redirect to payment gateway or next page
    protected $defaultRedirect                = ''; // redirect home
    protected $errors                         = [];

    /**
     * @param none
     * @return object
     */
    public function charge()
    {
        include_once UMP_RZR_PATH . 'classes/libs/razorpay/Razorpay.php';
        if ( $this->paymentOutputData['is_recurring'] ){
            // recurring payment is not available in this module
            return $this;
        }
        //
        if ( empty( $this->paymentSettings['ump_rzr-enabled'] ) || empty( $this->paymentSettings['ump_rzr-key'] )
        || empty( $this->paymentSettings['ump_rzr-secret'] ) ){
            return $this;
        }
        try {
            $api = new \Razorpay\Api\Api( $this->paymentSettings['ump_rzr-key'], $this->paymentSettings['ump_rzr-secret'] );
        } catch ( \Exception $e ){
            return $this;
        }

        /*
        if ( $this->paymentOutputData['currency'] !== 'INR' ){
            $this->paymentOutputData['currency'] = 'INR';
        }
        */

        try {
            $razorPay = $api->paymentLink->create([
                'amount'                    => $this->paymentOutputData['amount'] * 100,
                'currency'                  => $this->paymentOutputData['currency'],
                'accept_partial'            => false,
                'description'               => $this->paymentOutputData['level_description'],
                'customer'                  => [
                                                  'name'      => $this->paymentOutputData['customer_name'],
                                                  'contact'   => '',// phone number
                                                  'email'     => $this->paymentOutputData['customer_email'],
                ],
                'notify'                    => [ 'sms' => false, 'email' => false ],
                'reminder_enable'           => false,
                'notes'                     => [
                                                  'order_identificator' => $this->paymentOutputData['order_identificator'],
                                                  'uid'                 => $this->paymentOutputData['uid'],
                                                  'lid'                 => $this->paymentOutputData['lid'],
                ],
                'callback_url'              => $this->returnUrlAfterPayment,
                'callback_method'           => 'get'
            ]);
            $this->redirectUrl = isset( $razorPay->short_url ) ? $razorPay->short_url : '';
        } catch ( \Exception $e ){
            return $this;
        }
        return $this;

    }

    /**
     * @param none
     * @return none
     */
    public function webhook()
    {
        $post = file_get_contents( 'php://input' );
        $postData = json_decode( $post, true );
        if ( empty( $postData ) ){
            echo '============= Ultimate Membership Pro - RazorPay Webhook ============= ';
            echo '<br/><br/>No Payments details sent. Come later';
            exit;
        }

        if ( !isset( $postData['event'] ) ){
            return;
        }

        $orderIdentificator = isset( $postData['payload']['order']['entity']['notes']['order_identificator'] ) ? $postData['payload']['order']['entity']['notes']['order_identificator'] : '';

        $this->webhookData = [
                                'transaction_id'              => '',
                                'order_identificator'         => '',
                                'amount'                      => '',
                                'currency'                    => '',
                                'payment_details'             => '',
                                'payment_status'              => '',
        ];

        switch ( $postData['event'] ){
            case 'payment_link.paid':
              $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
              $orderId = $orderMeta->getIdFromMetaNameMetaValue( 'order_identificator', $orderIdentificator );

              $orderObject = new \Indeed\Ihc\Db\Orders();
              $orderData = $orderObject->setId( $orderId )
                                       ->fetch()
                                       ->get();

              $this->webhookData = [
                                      'transaction_id'              => isset( $postData['payload']['payment']['entity']['id'] ) ? $postData['payload']['payment']['entity']['id'] : '',
                                      'order_identificator'         => $orderIdentificator,
                                      'amount'                      => isset( $postData['payload']['payment']['entity']['amount'] ) ? ($postData['payload']['payment']['entity']['amount'] / 100) : '',
                                      'currency'                    => isset( $postData['payload']['payment']['entity']['currency'] ) ? $postData['payload']['payment']['entity']['currency'] : '',
                                      'payment_details'             => $postData,
                                      'payment_status'              => 'completed',
                                      'uid'                         => isset( $orderData->uid ) ? $orderData->uid : 0,
                                      'lid'                         => isset( $orderData->lid ) ? $orderData->lid : 0,
              ];
              break;
            case 'payment_link.cancelled':
            case 'payment.failed':
              $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
              $orderId = $orderMeta->getIdFromMetaNameMetaValue( 'order_identificator', $orderIdentificator );

              $orderObject = new \Indeed\Ihc\Db\Orders();
              $orderData = $orderObject->setId( $orderId )
                                       ->fetch()
                                       ->get();

              $this->webhookData = [
                                      'transaction_id'              => isset( $postData['payload']['payment']['entity']['id'] ) ? $postData['payload']['payment']['entity']['id'] : '',
                                      'order_identificator'         => $orderIdentificator,
                                      'amount'                      => isset( $postData['payload']['payment']['entity']['amount'] ) ? ($postData['payload']['payment']['entity']['amount'] / 100) : '',
                                      'currency'                    => isset( $postData['payload']['payment']['entity']['currency'] ) ? $postData['payload']['payment']['entity']['currency'] : '',
                                      'payment_details'             => $postData,
                                      'payment_status'              => 'cancel',
                                      'uid'                         => isset( $orderData->uid ) ? $orderData->uid : 0,
                                      'lid'                         => isset( $orderData->lid ) ? $orderData->lid : 0,
              ];
              break;
        }

    }

    /**
     * @param int
     * @param int
     * @param string
     * @return bool
     */
    public function canDoPause( $uid = 0, $lid = 0, $transactionId = '' )
    {
        return false;
    }


}
