<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.spot.im/
 * @since             1.0.0
 * @package           Spot_IM_COMMENT
 *
 * @wordpress-plugin
 * Plugin Name:       Spot_IM Comment
 * Plugin URI:        https://www.spot.im/
 * Description:       The official temporary SpotIM plugin
 * Version:           1.0.0
 * Author:            Spot_IM
 * Author URI:        https://www.spot.im/
 * License:           N/A
 * License URI:       N/A
 * Text Domain:       spot-im
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('SPOT_IM_PLUGIN_NAME','spot-im');
define('SPOT_IM_VERSIONE','1.0.0');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spot-im-activator.php
 */
function activate_spot_im() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spot-im-activator.php';
	SPOT_IM_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spot-im-deactivator.php
 */
function deactivate_spot_im() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spot-im-deactivator.php';
	SPOT_IM_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_spot_im' );
register_deactivation_hook( __FILE__, 'deactivate_spot_im' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-spot-im.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function spot_im_run() {
	$plugin = new SPOT_IM(SPOT_IM_PLUGIN_NAME,SPOT_IM_VERSIONE);
	$plugin->run();
}

spot_im_run();
