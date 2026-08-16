<?php
/**
 * Plugin Name: Real Visitor Stats
 * Description: Statistici reale despre vizitatori: unici pe zi, pageviews, referreri, browsere, dispozitive.
 * Version: 1.0.0
 * Author: YourName
 * License: GPL v2 or later
 * Text Domain: real-visitor-stats
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RVS_VERSION', '1.0.0' );
define( 'RVS_PLUGIN_FILE', __FILE__ );
define( 'RVS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RVS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once RVS_PLUGIN_DIR . 'includes/class-rvs-tracker.php';
require_once RVS_PLUGIN_DIR . 'includes/class-rvs-admin.php';
require_once RVS_PLUGIN_DIR . 'includes/class-rvs-plugin.php';

register_activation_hook( __FILE__, array( 'RVS_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'RVS_Plugin', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'RVS_Plugin', 'uninstall' ) );

RVS_Plugin::get_instance();
