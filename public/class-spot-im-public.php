<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Spot_IM
 * @subpackage Spot_IM/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Spot_IM_Public {

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
        
	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name
	 * @param string $version
	 * @param SPOT_IM_Options $options
	 *
	 * @private param string $plugin_name The name of this plugin.
	 * @private param string $version The version of this plugin.
         * @private param string $options The object of the SPOT_IM_Options.
	 */
	public function __construct($plugin_name, $version, $options) {
                
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->options = $options;
	}
        
        public function set_spot_id(){
            $spot_id = $this->options->get_spot_id();
            ?>
                <script type="text/javascript">
                     $spot_im_id = '<?php echo esc_js($spot_id) ?>';
                </script>
            <?php
        }
        
	/**
	 * Register the stylesheets/javaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
            if(is_singular()){
                wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spot-im-public.css', array(), $this->version, 'all' );
                wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spot-im-public.js', array('jquery'), $this->version, true  );
            }
            
        }
        
        public function conversation(){
            if (is_singular()) {
                return plugin_dir_path( __FILE__ ) . 'comment-template.php';
            }
        }

}
