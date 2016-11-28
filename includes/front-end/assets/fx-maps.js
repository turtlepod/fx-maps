/**
 * JS SCRIPT
**/
jQuery( document ).ready( function( $ ){

	/* For Each Map */
	$( ".fx-maps-google-maps" ).each(function( index ) {

		/* This */
		var that = $( this );

		/* Get Coordinate */
		var map_lat = that.data( 'lat' );
		var map_lng = that.data( 'lng' );

		/* Render Maps */
		var pos = new google.maps.LatLng( map_lat, map_lng );
		var map = new google.maps.Map( that[0], { 
			center           : pos, 
			zoom             : 13,
			streetViewControl: false,
		});

		/* Render Marker */
		var marker = new google.maps.Marker({
			map         : map,
			draggable   : true,
			animation   : google.maps.Animation.DROP,
			anchorPoint : new google.maps.Point( 0, -29 ),
			position    : pos,
		});

		/* Make responsive */
		google.maps.event.addDomListener( window, 'resize', function() {
			var center = map.getCenter();
			google.maps.event.trigger( map, 'resize' );
			map.setCenter( center ); 
		});

		/* Custom event to force resize if needed */
		that.on( 'fx_maps_resize', function(){
			var center = map.getCenter();
			google.maps.event.trigger( map, 'resize' );
			map.setCenter( center ); 
		});
	});

});