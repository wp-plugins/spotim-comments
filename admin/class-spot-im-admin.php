<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://www.spot.im/
 * @since      1.0.0
 *
 * @package    SPOT_IM
 * @subpackage SPOT_IM/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 */
class SPOT_IM_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

        
        /**
	 * The options management class of the the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SPOT_IM_Options $options Manipulates with plugin options
	 */
	private $options;
        
      
	private $settings_key;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name
	 * @param string $version
	 * @param SPOT_IM_Options $options
	 *
	 * @private param string $plugin_name The name of this plugin.
	 * @private param string $version The version of this plugin.
         * @private param string $options The object of the SPOT_IM_Options.
	 */
	public function __construct( $plugin_name, $version, $options ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
                $this->options = $options;
                $this->settings_key = $this->options->get_spot_id_key();
		$this->slug_admin_sync         = $this->plugin_name . '-sync';
		$this->slug_admin_settings = $this->plugin_name . '-settings';
	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 * This function called from SPOT_IM main class and registered with 'admin_menu' hook.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
                
                add_menu_page(
			__('Spot IM','spot-im'),
			__('Spot IM','spot-im'),
			'administrator',
			$this->slug_admin_sync,
			array( $this, 'render_sync' ),
			'dashicons-welcome-write-blog'
		);
		add_submenu_page(
			$this->slug_admin_sync,
			__('Synchronize','spot-im'), 
			__('Synchronize','spot-im'), 
			'administrator',
			$this->slug_admin_sync,
                        array( $this, 'render_sync')
		);
		add_submenu_page(
			$this->slug_admin_sync,
			__('SpotIM Settings','spot-im'), 
			__('SpotIM Settings','spot-im'), 
			'administrator',
			$this->slug_admin_settings,
                        array( $this, 'settings_render' )
		);
		
              
	}
        
        /**
	 * Render Settings page
	 *
	 * @since    1.0.0
	 */
        public function settings_render(){
          $spot_id =  $this->options->get_spot_id();
        ?>
            <div class="wrap">
                <h2><?php _e('Set your Spot Id','spot-im')?></h2>
                <form method="post" action="options.php" id="<?php echo $this->settings_key;?>-form">
                    <?php settings_fields($this->settings_key); ?>
                    <?php do_settings_sections($this->settings_key); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Spot Id','spot-im')?>:</th>
                            <td>
                                <input type="hidden" value="<?php echo $spot_id?esc_attr( $spot_id):''; ?>" />
                                <input required="required" type="text" name="<?php echo $this->settings_key;?>"  value="<?php echo $spot_id?esc_attr( $spot_id):''; ?>" />
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
        <?php
        }
        
       
        /**
	 * Render Synchronize page
	 *
	 * @since    1.0.0
	 */
        public function render_sync(){
            
            $get_step = $this->options->get_step();
            $owner = $this->options->get_owner();
            if(!$owner){
                $get_step = false;
                $super_admins = get_users(array('fields'=>array('ID','display_name'),
                                                'role'=>'administrator',
                                                'orderby'=>'display_name',
                                                'order'=>'ASC'));
            }
            else{
                $get_step = 2;
            }
            $comment_prepared = $this->options->get_export_comment_count();
            $comments_count = SPOT_IM_Comments::get_comment_total();
            ?>
                <div class="spot_im_sync_wrapper">
                    <h3 id="spot_im_steps"><?php _e('Step','spot-im')?> <b>1</b>/4</h3>
                    <ul class="spot_im_tabs">
                        <li  <?php if(!$get_step):?>class="spot_im_active"<?php endif;?>>
                            <a href="<?php echo admin_url( 'admin-ajax.php?action=set_spot_onwer' ) ?>"><?php _e('Choose Onwer','spot-im')?></a>
                        </li>
                        <li <?php if($get_step==2):?>class="spot_im_active"<?php endif;?>>
                            <a href="<?php echo admin_url( 'admin-ajax.php?action=prepare' ) ?>"><?php _e('Prepare Data','spot-im')?></a>
                        </li>
                        <li <?php if($get_step==3):?>class="spot_im_active"<?php endif;?>>
                            <a href="<?php echo admin_url( 'admin-ajax.php?action=export' ) ?>"><?php _e('Export','spot-im')?></a>
                        </li>
                        <li <?php if($get_step==4):?>class="spot_im_active"<?php endif;?>>
                            <a href="<?php echo admin_url( 'admin-ajax.php?action=finish' ) ?>"><?php _e('Finish','spot-im')?></a>
                        </li>
                    </ul>
                    <form method="post" id="spot_im_form">
                        <ul class="spot_im_description">
                            <li <?php if(!$get_step):?>class="spot_im_active"<?php endif;?>>
                                <label for="spot_im_owner"><?php _e('Set Owner')?></label>
                                <select id="spot_im_owner" name="owner">
                                    <?php foreach($super_admins as $admin):?>
                                        <option value="<?php echo intval($admin->ID) ?>"><?php echo esc_attr($admin->display_name)?></option>
                                    <?php endforeach;?>
                                </select>
                                <p>
                                    <?php _e('The email,display_name of select admin user will be exported in the export file.','spot-im')?><br/>
                                    <strong><?php _e('Important','spot-im')?>:</strong> <?php _e("You can choose the owner only one time, after choosing You can't change it.",'spot-im')?>
                                </p>
                            </li>
                            <li <?php if($get_step==2):?>class="spot_im_active"<?php endif;?>>
                                <div class="spot_im_progressbar_wrapper">
                                    <span class="spot_im_progressbar_label"><?php _e('Comments','spot-im')?></span>
                                    <div id="spot_im_comment_progressbar" class="spot_im_progressbar"><div class="progress-label">0%</div></div>
                                    <span class="spot_im_comment_current spot_im_current_label"><b><?php echo $comment_prepared>0?$comment_prepared:0?></b>/<span class="spot_im_total"><?php echo $comments_count?></span></span>
                                </div>
                            </li>
                            <li <?php if($get_step==3):?>class="spot_im_active"<?php endif;?>></li>
                            <li <?php if($get_step==4):?>class="spot_im_active"<?php endif;?>>
                                <center><h3><?php _e("Export finished",'spot-im')?></h3></center>
                                <label for="spot_im_export_data"><?php _e("If export .zip wasn't generated in few seconds copy this text into .txt file and save",'spot-im')?></label>
                                <textarea id="spot_im_export_data"></textarea>
                            </li>
                        </ul>
                        <input type="button" value="<?php _e('Start','spot-im')?>" id="spot_im_start" class="button button-primary button-large" />
                    </form>
                </div>
            <?php
        }


        /**
	 * Register the plugin settings and settings section.
	 * This function called from SPOT_IM main class and registered with 'admin_init' hook.
	 *
	 * @since    1.0.0
	 */
	public function register_plugin_settings() {
            register_setting($this->settings_key,$this->settings_key);
	}

	/**
	 * Register the stylesheets/javaScript for the admin dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in SPOT_IM_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The SPOT_IM_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */
           
            wp_enqueue_media();
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-progressbar' );
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spot-im-admin.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spot-im-admin.css', array(), $this->version, 'all' );
            $translation_array = array(
                'change_spot_im' => __("You need to have correct  Spot-Id in order to show your  existing comments. Are you sure that you want to change it?", 'spot-im' ),
                'spot_im_req' => __( 'To show comments You need to enter Spot Id', 'spot-im' ),
            );
            wp_localize_script($this->plugin_name, 'spot_im_trans', $translation_array );
	}

}   
