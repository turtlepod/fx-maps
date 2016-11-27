<?php
/**
 * Dismissable Admin Notice
 *
 * @version 1.1.0
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
 * @license GPLv2 or later
 *
 *-----------------------------
 * CHANGELOG:
 *-----------------------------
 *
 * 1.1.0 - 27.NOV.2016
 * - Add Hook Suffix Args.
 * 
 * 1.0.0 - 18.SEP.2016
 * - Initial Release.
 *
 *-----------------------------
**/
class Fx_Maps_Welcome_Notice{

	/* Args */
	var $args;

	/**
	 * Constructor.
	 */
	public function __construct( $args = array() ) {
		if( is_multisite() ){
			return;
		}

		$defaults = array(
			'notice'      => '',
			'option'      => 'fx-base_welcome',
			'dismiss'     => 'Dismiss this notice.',
			'capability'  => 'manage_options',
			'hook_suffix' => '', // target admin page/settings.
		);
		$this->args = wp_parse_args( $args, $defaults );

		/* Check if args is set */
		if( ! get_option( $this->args['option'] ) && $this->args['notice'] && $this->args['option'] && current_user_can( $this->args['capability'] ) ){

			/* Add Notice */
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			/* Ajax Callback */
			add_action( "wp_ajax_{$this->args['option']}_dismiss", array( $this, 'ajax_callback' ) );
		}
	}

	/**
	 * Admin Notice
	 */
	public function admin_notices(){

		/* Kill and bail if visiting the target admin page. */
		global $hook_suffix;
		if( $this->args['hook_suffix'] && $this->args['hook_suffix'] === $hook_suffix ){
			update_option( $this->args['option'], 1 );
			return;
		}

		$url = add_query_arg( 'action', "{$this->args['option']}_dismiss", admin_url( 'admin-ajax.php' )
		);
		$url = wp_nonce_url( $url, "{$this->args['option']}_nonce" );
		?>
		<div class="notice notice-info" style="padding-right:38px;position:relative;">
			<?php echo $this->args['notice']; ?>
			<a href="<?php echo esc_url( $url ); ?>" class="notice-dismiss" style="text-decoration:none;"><span class="screen-reader-text"><?php echo $this->args['dismiss']; ?></span></a>
		</div>
		<?php
	}

	/**
	 * Ajax Callback to Dismiss Notice
	 */
	public function ajax_callback(){
		if ( current_user_can( $this->args['capability'] ) && check_admin_referer( "{$this->args['option']}_nonce" ) ) {
			update_option( $this->args['option'], 1 );
		}
		/* Redirect them back to the page where they click dismiss icon */
		$redirect_url = wp_get_referer() ? wp_get_referer() : admin_url( 'index.php' );
		wp_safe_redirect( esc_url_raw( $redirect_url ) );
		die();
	}

}
