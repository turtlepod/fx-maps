<?php
namespace fx_maps;
if ( ! defined( 'WPINC' ) ) { die; }
Settings::get_instance();

/**
 * Settings
 * @since 1.0.0
 */
class Settings{

	/**
	 * Returns the instance.
	 */
	public static function get_instance(){
		static $instance = null;
		if ( is_null( $instance ) ) $instance = new self;
		return $instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		/* Vars */
		$this->uri  = trailingslashit( plugin_dir_url( __FILE__ ) );
		$this->path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->settings_slug = 'fx-maps';
		$this->hook_suffix   = 'settings_page_fx-maps';
		$this->options_group = 'fx-maps';
		$this->option_name   = 'fx-maps';

		/* Create Settings Page */
		add_action( 'admin_menu', array( $this, 'create_settings_page' ) );

		/* Register Settings and Fields */
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		/* Screen Layout Columns */
		add_filter( 'screen_layout_columns',  array( $this, 'layout_columns' ), 10, 2 );

		/* Settings Scripts */
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		/* Footer Scripts */
		add_action( "admin_footer-{$this->hook_suffix}", array( $this, 'footer_scripts' ) );

		/* PRO Notice */
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}


	/**
	 * Create Settings Page
	 * @since 1.0.0
	 */
	public function create_settings_page(){

		/* Create Settings Sub-Menu */
		add_options_page(
			$page_title  = __( 'Maps Settings', 'fx-maps' ),
			$menu_title  = __( 'Maps', 'fx-maps' ),
			$capability  = 'manage_options',
			$menu_slug   = $this->settings_slug,
			$function    = array( $this, 'settings_page' )
		);
	}

	/**
	 * Settings Page Output
	 * @since 1.0.0
	 */
	public function settings_page(){
		global $hook_suffix;
		do_action( 'add_meta_boxes', $hook_suffix ); // enable meta boxes
		?>
		<div class="wrap">
			<h1><?php _e( 'Maps Settings', 'fx-maps' ); ?></h1>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
					<form method="post" action="options.php">
						<div id="postbox-container-2" class="postbox-container">
							<?php do_settings_sections( $this->settings_slug ); ?>
							<?php settings_fields( $this->options_group ); ?>
							<?php submit_button(); ?>
						</div>
						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( $hook_suffix, 'side', null ); ?>
							<!-- #side-sortables -->
						</div><!-- #postbox-container-1 -->
					</form>
				</div><!-- #post-body -->
				<br class="clear">
			</div><!-- #poststuff -->
		</div><!-- wrap -->
		<?php
	}


	/**
	 * Register Settings
	 * @since 0.1.0
	 */
	public function register_settings(){

		/* Register settings */
		register_setting(
			$option_group      = $this->options_group,
			$option_name       = $this->option_name,
			$sanitize_callback = array( $this, 'sanitize' )
		);

		/* Create settings section */
		add_settings_section(
			$section_id        = 'fx_maps_section',
			$section_title     = '',
			$callback_function = '__return_false',
			$settings_slug     = $this->settings_slug
		);

		/* Create setting field: Address */
		add_settings_field(
			$field_id          = 'fx_maps_address',
			$field_title       = '<label for="fx-maps-address">' . __( 'Location Address', 'fx-maps' ) . '</label>',
			$callback_function = function(){
				$address = Functions::get_option( 'address' );
				?>
					<textarea autocomplete="off" id="fx-maps-address" class="widefat" name="fx-maps[address]"><?php echo esc_textarea( $address ); ?></textarea>
					<p class="description"><?php _e( 'Your location address.', 'fx-maps' ); ?></p>
				<?php
			},
			$settings_slug     = $this->settings_slug,
			$section_id        = 'fx_maps_section'
		);

		/* Create setting field: Google Maps API */
		add_settings_field(
			$field_id          = 'fx_maps_api',
			$field_title       = '<label for="fx-maps-gmaps-api-key">' . __( 'Google Maps API Key', 'fx-maps' ) . '</label>',
			$callback_function = function(){
				$api_key = Functions::get_option( 'gmaps_api_key' );
				?>
					<input autocomplete="off" id="fx-maps-gmaps-api-key" name="fx-maps[gmaps_api_key]" type="text" class="widefat" value="<?php echo sanitize_text_field( $api_key ); ?>">
					<p class="description"><?php printf( __( 'Get your API key <a target="_blank" href="%s">here</a>.', 'fx-maps' ), '#' ); ?></p>
				<?php
			},
			$settings_slug     = $this->settings_slug,
			$section_id        = 'fx_maps_section'
		);

		/* Create setting field: Google Maps */
		add_settings_field(
			$field_id          = 'fx_maps_google_maps',
			$field_title       = '<label for="fx-maps-gmaps_address">' . __( 'Google Maps Location', 'fx-maps' ) . '</label>',
			$callback_function = function(){
				$gmaps_address = Functions::get_option( 'gmaps_address' ); // search
				$gmaps_lat     = Functions::get_option( 'gmaps_lat' );
				$gmaps_lng     = Functions::get_option( 'gmaps_address' );
				?>
					<p id="gmaps-search">
						<input autocomplete="off" placeholder="<?php echo sanitize_text_field( __( 'Search Google Maps...', 'fx-maps' ) ); ?>" id="fx-maps-gmaps_address" name="fx-maps[gmaps_address]" type="search" class="widefat" value="<?php echo sanitize_text_field( $gmaps_address ); ?>">
						<a id="get-loc" title="<?php esc_attr_e( 'Get your current location', 'fx-maps' ); ?>" href="#"><span><?php _e( 'Get your current location', 'fx-maps' ); ?></span></a>
					</p>
					<p>{Maps Goes Here.}</p>
					<input autocomplete="off" name="fx-maps[gmaps_lat]" type="text" value="<?php echo sanitize_text_field( $gmaps_lat ); ?>">
					<input autocomplete="off" name="fx-maps[gmaps_lng]" type="text" value="<?php echo sanitize_text_field( $gmaps_lng ); ?>">
					<p class="description"><?php _e( 'Search your location in Google Maps.', 'fx-maps' ); ?></p>
				<?php
			},
			$settings_slug     = $this->settings_slug,
			$section_id        = 'fx_maps_section'
		);

	}


	/**
	 * Sanitize Options
	 * @since 1.0.0
	 */
	public function sanitize( $data ){
		$new_data = array();
		if( isset( $data['address'] ) ){
			$new_data['address'] = wp_kses_post( $data['address'] );
		}
		if( isset( $data['gmaps_address'] ) ){
			$new_data['gmaps_address'] = strip_tags( $data['gmaps_address'] );
		}
		if( isset( $data['gmaps_api_key'] ) ){
			$new_data['gmaps_api_key'] = strip_tags( $data['gmaps_api_key'] );
		}
		if( isset( $data['gmaps_lat'] ) ){
			$new_data['gmaps_lat'] = strip_tags( $data['gmaps_lat'] );
		}
		if( isset( $data['gmaps_lat'] ) ){
			$new_data['gmaps_lat'] = strip_tags( $data['gmaps_lat'] );
		}
		return $new_data;
	}


	/**
	 * Number of Column available in Settings Page.
	 * we can only set to 1 or 2 column.
	 * @since 1.0.0
	 */
	public function layout_columns( $columns, $screen ){
		if ( $screen === $this->hook_suffix ){
			$columns[$this->hook_suffix] = 2;
		}
		return $columns;
	}


	/**
	 * Settings Scripts
	 * @since 1.0.0
	 */
	public function scripts( $hook_suffix ){

		/* Only load in settings page. */
		if ( $this->hook_suffix == $hook_suffix ){

			/* Meta Box Scripts */
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

			/* CSS */
			wp_enqueue_style( "{$this->settings_slug}_settings", $this->uri . 'assets/settings.css', array(), VERSION );

			/* JS */
			wp_enqueue_script( "{$this->settings_slug}_settings", $this->uri . 'assets/settings.js', array( 'jquery' ), VERSION, true );
		}
	}


	/**
	 * Print Footer Scripts
	 * @since 1.0.0
	 */
	public function footer_scripts(){
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				postboxes.add_postbox_toggles( '<?php echo esc_attr( $this->hook_suffix ); ?>' );
			});
			//]]>
		</script>
		<?php
	}


	/**
	 * Add Meta Boxes
	 * @since 1.0.0
	 */
	public function add_meta_boxes(){

		/* PRO Upsell */
		add_meta_box(
			$id         = 'fx_maps_upsell',
			$title      = __( 'f(x) Maps PRO', 'fx-maps' ),
			$callback   = function(){
				?>
				<p>Lorem Ipsum</p>
				<?php
			},
			$screen     = $this->hook_suffix,
			$context    = 'side',
			$priority   = 'high'
		);

		/* Shortcode */
		add_meta_box(
			$id         = 'fx_maps_shortcode',
			$title      = __( 'Shortcode', 'fx-maps' ),
			$callback   = function(){
				?>
				<p>Use this shortcode to display map and address.</p>
				<p><input id="fx-maps-shortcode" class="widefat" type="text" readonly="readonly" value="[fx-maps]"></p>
				<?php
			},
			$screen     = $this->hook_suffix,
			$context    = 'side',
			$priority   = 'default'
		);
	}
}