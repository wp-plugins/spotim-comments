<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.spot.im/
 * @since      1.0.0
 *
 * @package    SPOT_IM
 * @subpackage SPOT_IM/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class SPOT_IM_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            do_action('spot_im_activated');
	}

}
