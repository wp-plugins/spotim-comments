<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://www.spot.im/
 * @since      1.0.0
 *
 * @package    SPOT_IM
 * @subpackage SPOT_IM/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class SPOT_IM {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SPOT_IM_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;
        
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct($pluginname, $version) {

		$this->plugin_name = $pluginname;
		$this->version     = $version;

		$this->load_dependencies();
		$this->set_locale();
                if(is_admin()){
                    $this->define_admin_hooks();
                }
                else{
                    $this->define_public_hooks();
                }

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SPOT_IM_Loader. Orchestrates the hooks of the plugin.
	 * - SPOT_IM_i18n. Defines internationalization functionality.
         * - SPOT_IM_Options.  The plugin options helper class
         * - SPOT_IM_Comments. Class to work with comments
	 * - SPOT_IM_Admin. Defines all hooks for the dashboard.
	 * - SPOT_IM_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
                $plugin_dir = plugin_dir_path( dirname( __FILE__ ) );
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $plugin_dir.'includes/class-spot-im-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $plugin_dir.'includes/class-spot-im-i18n.php';
		require_once $plugin_dir.'includes/class-spot-im-options.php';
		require_once $plugin_dir.'includes/class-spot-im-comments.php';


		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once $plugin_dir.'admin/class-spot-im-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $plugin_dir.'public/class-spot-im-public.php';

		$this->loader = new SPOT_IM_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SPOT_IM_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new SPOT_IM_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

            $plugin_options = new SPOT_IM_Options( $this->get_plugin_name(), $this->get_version() );
            $plugin_admin   = new SPOT_IM_Admin( $this->get_plugin_name(), $this->get_version(), $plugin_options );


            //Ajax actions registration
            $this->loader->add_action( 'wp_ajax_set_spot_onwer', $plugin_options, 'set_spot_onwer' );
            $this->loader->add_action( 'wp_ajax_set_spot', $plugin_options, 'set_spot' );
            $this->loader->add_action( 'wp_ajax_prepare', $plugin_options, 'prepare' );
            $this->loader->add_action( 'wp_ajax_export', $plugin_options, 'export' );
            $this->loader->add_action( 'wp_ajax_finish', $plugin_options, 'finish' );
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
            $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
            $this->loader->add_action( 'admin_init', $plugin_admin, 'register_plugin_settings',11 );
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
            $plugin_options = new SPOT_IM_Options( $this->get_plugin_name(), $this->get_version() );
            $spot_id = $plugin_options->get_spot_id();
            if($spot_id){
                $plugin_public = new SPOT_IM_Public( $this->get_plugin_name(), $this->get_version(),$plugin_options );
                $this->loader->add_action('wp_head', $plugin_public, 'set_spot_id');
                $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                $this->loader->add_action('comments_template', $plugin_public, 'conversation',11);
            }
        }


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
            $this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    SPOT_IM_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
            return $this->loader;
	}

}
