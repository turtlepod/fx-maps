<?php
namespace fx_maps;
if ( ! defined( 'WPINC' ) ) { die; }
Front_End::get_instance();

/**
 * Front End
 * @since 1.0.0
 */
class Front_End{

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

		/* Scripts */
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		/* Init */
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Scripts
	 * @since 1.0.0
	 */
	public function scripts(){

		/* Google Maps */
		$url = add_query_arg( array(
			'v'         => '3.exp',
			'libraries' => 'places',
		), '//maps.googleapis.com/maps/api/js' );
		$key = strip_tags( Functions::get_option( 'gmaps_api_key' ) );
		$url = $key ? add_query_arg( 'key', urlencode( $key ), $url ) : $url;
		wp_register_script( 'google-maps', $url, array(), '3.exp', false );

		/* CSS */
		//wp_enqueue_style( "fx-maps", $this->uri . 'assets/fx-maps.css', array(), VERSION );

		/* JS */
		wp_register_script( "fx-maps", $this->uri . 'assets/fx-maps.js', array( 'jquery', 'google-maps' ), VERSION, true );
	}

	/**
	 * Init
	 * @since 1.0.0
	 */
	public function init(){

		/* Register Shortcode [fx-maps] */
		add_shortcode( 'fx-maps', array( $this, 'fx_maps_shortcode' ) );
	}

	/**
	 * [fx-maps] Shorcode Callback
	 * @since 1.0.0
	 */
	public function fx_maps_shortcode(){

		/* Data */
		$lat       = Functions::get_option( 'gmaps_lat', '40.712784' ); // default to new york.
		$lng       = Functions::get_option( 'gmaps_lng', '-74.005941' ); // default to new york.
		$address   = Functions::get_option( 'display_address' );

		/* Load Scripts */
		wp_enqueue_script( "fx-maps" );

		/* Render Output */
		ob_start();
		?>
		<div class="fx-maps">
			<div class="fx-maps-google-maps" style="width:100%;height:200px;" data-lat="<?php echo esc_attr( strip_tags( $lat ) ); ?>" data-lng="<?php echo esc_attr( strip_tags( $lng ) ); ?>"></div>
			<?php if( $address ){ ?>
				<address>
					<?php echo wpautop( wp_kses_post( $address ), true ); ?>
				</address>
			<?php } ?>
		</div>
		<?php
		return ob_get_clean();
	}

}
