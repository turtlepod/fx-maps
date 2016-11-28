<?php
/**
 * Plugin Name: f(x) Maps
 * Plugin URI: http://genbumedia.com/plugins/fx-maps/
 * Description: Simple way to display your business location using Google Maps.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: fx-maps
 * Domain Path: /languages/
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* Constants
------------------------------------------ */

define( 'FX_MAPS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'FX_MAPS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'FX_MAPS_FILE', __FILE__ );
define( 'FX_MAPS_PLUGIN', plugin_basename( __FILE__ ) );
define( 'FX_MAPS_VERSION', '1.0.0' );


/* Init
------------------------------------------ */

/* Load plugin in "plugins_loaded" hook */
add_action( 'plugins_loaded', 'fx_maps_init' );

/**
 * Plugin Init
 * @since 0.1.0
 */
function fx_maps_init(){

	/* Var */
	$uri      = FX_MAPS_URI;
	$path     = FX_MAPS_PATH;
	$file     = FX_MAPS_FILE;
	$plugin   = FX_MAPS_PLUGIN;
	$version  = FX_MAPS_VERSION;

	/* Prepare */
	require_once( $path . 'includes/prepare.php' );
	if( ! $sys_req->check() ) return;

	/* Setup */
	require_once( $path . 'includes/setup.php' );
}
