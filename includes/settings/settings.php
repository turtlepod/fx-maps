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
							<?php settings_errors(); ?>
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
			$section_id        = 'fx_base_section1',
			$section_title     = '',
			$callback_function = '__return_false',
			$settings_slug     = $this->settings_slug
		);

		/* Create Setting Field: Boxes, Buttons, Columns */
		add_settings_field(
			$field_id          = 'fx_base_settings_field1',
			$field_title       = __( 'Field #1', 'fx-maps' ),
			$callback_function = array( $this, 'settings_field1' ),
			$settings_slug     = $this->settings_slug,
			$section_id        = 'fx_base_section1'
		);

	}

	/**
	 * Settings Field Callback
	 * @since 1.0.0
	 */
	public function settings_field1(){
		?>
		<p>
			<input type="text" name="fx-base" value="<?php echo sanitize_text_field( get_option( $this->option_name ) ); ?>">
		</p>
		<p class="description">
			<?php _e( 'Hi there!', 'fx-maps' ); ?>
		</p>
		<?php
	}


	/**
	 * Sanitize Options
	 * @since 1.0.0
	 */
	public function sanitize( $data ){
		return sanitize_text_field( $data );
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
	}
}