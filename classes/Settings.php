<?php
namespace UmpRzr;

class Settings
{
    /**
     * Modify this array with your custom values
     * @var array
     */
    private $data = [

    										'lang_domain'				=> 'ultimate-membership-pro-razorpay',
    										'slug'							=> 'ump_rzr',
    										'name'						  => 'RazorPay',
    										'description'				=> 'The fastest method of making payments',
                        'ump_min_version'		=> '10.9',
    ];
    /**
     * Initialized automaticly. don't edit this array
     * @var array
     */
    private $paths = [
                      'dir_path'					=> '',
                      'dir_url'						=> '',
                      'plugin_base_name'	=> '',
    ];

    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        $this->setPaths();
        add_filter( 'ihc_default_options_group_filter', [ $this, 'options' ], 1, 2 );
    }



  /**
    * @param array
    * @param string
    * @return array
    */
    public function options( $options=[], $type='' )
    {
        if ( 'ump_rzr' == $type ){
                return [
                    'ump_rzr-enabled'         		    => 0,
                    'ump_rzr-key'         		        => 0,
                    'ump_rzr-secret'         		      => 0,
                    'ihc_ump_rzr_label'               => 'RazorPay',
                    'ihc_ump_rzr_select_order'        => '',
                    'ihc_ump_rzr_short_description'   => '',
                ];
        }
        return $options;
    }

    /**
     * @param none
     * @return none
     */
    public function setPaths()
    {
        $this->paths['dir_path'] = plugin_dir_path( __FILE__ );
        $this->paths['dir_path'] = str_replace( 'classes/', '', $this->paths['dir_path'] );

        $this->paths['dir_url'] = plugin_dir_url( __FILE__ );
        $this->paths['dir_url'] = str_replace( 'classes/', '', $this->paths['dir_url'] );

        $this->paths['plugin_base_name'] = dirname(plugin_basename( __FILE__ ));
        $this->paths['plugin_base_name'] = str_replace( 'classes', '', $this->paths['plugin_base_name'] );
    }

    /**
     * @param string
     * @return object
     */
    public function get()
    {
        return $this->data + $this->paths;
    }

    /**
     * @param none
     * @return string
     */
    public function getPluginSlug()
    {
        return $this->data['slug'];
    }
}
