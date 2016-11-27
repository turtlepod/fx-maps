<?php
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Text Domain
------------------------------------------ */
load_plugin_textdomain( dirname( $plugin ), false, dirname( $plugin ) . '/languages/' );

/* Add Support Link
------------------------------------------ */
require_once( $path . 'library/plugin-action-links.php' );
$args = array(
	'plugin'    => $plugin,
	'name'      => __( 'f(x) Maps', 'fx-maps' ),
	'version'   => $version,
	'text'      => __( 'Get Support', 'fx-maps' ),
);
new Fx_Maps_Plugin_Action_Links( $args );


/* Check PHP and WordPress Version
------------------------------------------ */
require_once( $path . 'library/system-requirement.php' );
$args = array(
	'wp_requires'   => array(
		'version'       => '4.4',
		'notice'        => wpautop( sprintf( __( 'f(x) Maps plugin requires at least WordPress 4.5+. You are running WordPress %s. Please upgrade and try again.', 'fx-maps' ), get_bloginfo( 'version' ) ) ),
	),
	'php_requires'  => array(
		'version'       => '5.3',
		'notice'        => wpautop( sprintf( __( 'f(x) Maps plugin requires at least PHP 5.3+. You are running PHP %s. Please upgrade and try again.', 'fx-maps' ), PHP_VERSION ) ),
	),
);
$sys_req = new Fx_Maps_System_Requirement( $args );
if( ! $sys_req->check() ) return;


/* Welcome Notice
------------------------------------------ */
require_once( $path . 'library/welcome-notice.php' );
$args = array( 
	'notice'       => wpautop( sprintf( __( 'Please visit <a href="%s">Maps Settings Page</a> to add your location.', 'fx-maps' ), add_query_arg( 'page', 'fx-maps', admin_url( 'options-general.php' ) ) ) ),
	'dismiss'      => __( 'Dismiss this notice.', 'fx-maps' ),
	'option'       => 'fx-maps_welcome',
	'hook_suffix'  => 'settings_page_fx-maps',
);
new Fx_Maps_Welcome_Notice( $args );
