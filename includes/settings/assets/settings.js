/**
 * JS SCRIPT
**/
jQuery( document ).ready( function( $ ){

	/* Get Coordinate */
	var map_lat = $( '#fx-maps-gmaps_address' ).data( 'lat' );
	var map_lng = $( '#fx-maps-gmaps_address' ).data( 'lng' );

	/* Render Map in Search Input */
	$( '#fx-maps-gmaps_address' ).fxGmaps( {
		lat       : map_lat,
		lng       : map_lng,
		lat_input : 'fx-maps[gmaps_lat]',
		lng_input : 'fx-maps[gmaps_lng]',
	} );

	/* Resize Map on Column Change */
	$( 'input[name="screen_columns"]' ).change(function() {
		$( '.fx-gmaps' ).trigger( 'fxgmaps_resize' );
	} );

});