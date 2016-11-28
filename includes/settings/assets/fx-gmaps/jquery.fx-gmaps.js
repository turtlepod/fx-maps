/**
 * f(x) GMaps
 * Simple JS to display Google Maps search below text input with geo tag feature to get current user location.
 * You also need to load Google Maps JS as dependency for this script to work.
 *
 * @version 1.0.0
 * @link http://genbumedia.com/plugins/fx-maps/
 * @license: GPLv2 or later
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/
;( function ( $ ) {
	$.fn.extend({

		/* Create Function */
		fxGmaps: function( options ) {

			/* Set default options */
			var settings = $.extend({
				height        : '200px',
				lat           : 0,
				lng           : 0,
				lat_input     : 'fx_gmaps_lat',
				lng_input     : 'fx_gmaps_lat',
			}, options );

			var that = $( this );

			/* Do this for each element */
			return this.each( function(){

				/* Prepare
				------------------------------------------ */

				/* Add Class */
				that.addClass( 'fx-gmaps-input' );

				/* Always add wrapper for consistency */
				that.wrap( '<div class="fx-gmaps"><div class="fx-gmaps-wrap"></div></div>' );

				/* Wrap El Vars */
				var fxGmapsEl = that.parents( '.fx-gmaps' );
				var wrapEl = that.parent( '.fx-gmaps-wrap' );

				/* Add Map */
				if( ! $( "#" + settings.id ).length ){
					fxGmapsEl.append( '<div class="map-canvas" style="width:100%;height:' + settings.height + ';"></div>' );
				}
				fxGmapsEl.append( '<input autocomplete="off" type="hidden" name="' + settings.lat_input + '" value="' + settings.lat + '">' );
				fxGmapsEl.append( '<input autocomplete="off" type="hidden" name="' + settings.lng_input + '" value="' + settings.lng + '">' );

				/* Vars */
				var addressInput = that;
				var mapCanvas    = fxGmapsEl.find( '.map-canvas' );
				var latInput     = fxGmapsEl.find( 'input[name="' + settings.lat_input + '"]' );
				var lngInput     = fxGmapsEl.find( 'input[name="' + settings.lng_input + '"]' );
				var map;
				var marker;
				var pos;
				var geocoder;
				var addressAutoComplete;

				/* Method Handler
				------------------------------------------ */
				var fxGmapsHandler = {

					/* Init
					------------------------------------------ */
					init: function(){

						/* Load Map */
						fxGmapsHandler.loadMap();

						/* Load Address Auto Complete */
						fxGmapsHandler.loadAddressAutoComplete();

						/* Add My Location */
						fxGmapsHandler.myLocation();
					},


					/* Load Map
					------------------------------------------ */
					loadMap: function(){
						/* Geo coder */
						geocoder = new google.maps.Geocoder();
						/* Get position */
						pos = new google.maps.LatLng( settings.lat, settings.lng );
						/* Render Map */
						map = new google.maps.Map( mapCanvas[0], { 
							center           : pos, 
							zoom             : 13,
							streetViewControl: false 
						});
						/* Render Marker */
						marker = new google.maps.Marker({
							map         : map,
							draggable   : true,
							animation   : google.maps.Animation.DROP,
							anchorPoint : new google.maps.Point( 0, -29 ),
							position    : pos,
						});
						/* Marker Drag-End Event */
						google.maps.event.addListener( marker, 'dragend', function( event ) {
							var pos = event.latLng;
							var lat = pos.lat();
							var lng = pos.lng();
							/* Update position */
							fxGmapsHandler.updatePosition( lat, lng, pos );
							/* Update address */
							fxGmapsHandler.updateAddress( pos );
						});
						/* Make responsive */
						google.maps.event.addDomListener( window, 'resize', function() {
							var center = map.getCenter();
							google.maps.event.trigger( map, 'resize' );
							map.setCenter( center ); 
						});
						/* Custom event to force resize if needed */
						fxGmapsEl.on( 'fxgmaps_resize', function(){
							var center = map.getCenter();
							google.maps.event.trigger( map, 'resize' );
							map.setCenter( center ); 
						});
					},


					/* My Location
					------------------------------------------ */
					myLocation: function(){
						if ( navigator.geolocation ) {
							/* Chrome need SSL */
							var is_chrome = /chrom(e|ium)/.test( navigator.userAgent.toLowerCase() );
							var is_ssl    = 'https:' == document.location.protocol;
							if( is_chrome && ! is_ssl ){
								return false;
							}
							wrapEl.prepend( '<span class="mylocation"></span>' );
							wrapEl.children( '.mylocation' ).click( function(e){
								e.preventDefault();
								var icon = $( this );
								icon.addClass( 'loading' );
								navigator.geolocation.getCurrentPosition(
									function( position ){ // success cb
										var lat = position.coords.latitude;
										var lng = position.coords.longitude;
										var pos = new google.maps.LatLng( lat, lng );
										/* Update position */
										fxGmapsHandler.updatePosition( lat, lng, pos );
										/* Set marker position */
										marker.setPosition( pos );
										/* Update address */
										fxGmapsHandler.updateAddress( pos );
										/* Done */
										icon.removeClass( 'loading' );
									},
									function(){ // fail cb
										icon.removeClass( 'loading' );
									}
								);
							});
						}
					},

					/* Load Address AutoComplete
					------------------------------------------ */
					loadAddressAutoComplete: function(){
						/* AutoComplete */
						addressAutoComplete = new google.maps.places.Autocomplete( addressInput[0] );
						addressAutoComplete.bindTo( 'bounds', map );
						/* Place Changed Event */
						google.maps.event.addListener( addressAutoComplete, 'place_changed', function() {
							var place = addressAutoComplete.getPlace();
							if( undefined !== place ){
								var pos = place.geometry.location;
								var lat = pos.lat();
								var lng = pos.lng();
								/* Update position */
								fxGmapsHandler.updatePosition( lat, lng, pos );
								/* Set marker position */
								marker.setPosition( pos );
							}
						});
						/* Disable "Enter" Key */
						google.maps.event.addDomListener( addressInput[0], 'keydown', function(e) { 
							if ( e.keyCode == 13 ) {
								e.preventDefault(); 
							}
						});
					},

					/* Utility
					------------------------------------------ */

					/* Update Coordinate Position */
					updatePosition : function( lat, lng, pos ){
						/* Update input */
						latInput.val( lat );
						lngInput.val( lng );
						/* Set map position */
						map.panTo( pos );
					},

					/* Update Address by Position */
					updateAddress : function( pos ){
						geocoder.geocode(
							{ 'latLng': pos },
							function( results, status ) {
								if ( status == google.maps.GeocoderStatus.OK && results[0] ) {
									addressInput.val( results[0].formatted_address );
								}
							}
						);
					},

				}; // var fxGmapsHandler

				/* Load Init */
				fxGmapsHandler.init();

			}); // end return;
		},

	});
}( jQuery ));
