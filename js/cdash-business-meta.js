var map;
var marker;

jQuery(document).ready(function ($) {
// Copy location information to billing metabox 
	$(document).on( 'click', '.billing-copy', function (evt) {
	// $('.billing-copy').click(function (evt) {  // this is what triggers the function   
		var address = $( evt.target ).closest('.location').children('.address-data').children('.address-wrapper').children('textarea').val();
		$("#billing-address").val(address);
		var city = $( evt.target ).closest('.location').children('.address-data').children('.city-wrapper').children('p').children('.city').val();
		$("#billing-city").val(city);
		var state = $( evt.target ).closest('.location').children('.address-data').children('.state-wrapper').children('p').children('.state').val();
		$("#billing-state").val(state);
		var zip = $( evt.target ).closest('.location').children('.address-data').children('.zip-wrapper').children('p').children('.zip').val();
		$("#billing-zip").val(zip);
		var email = $( evt.target ).closest('.location').children('.email-fieldset').children('.wpa_loop-email').children('.first').children('p').children('.email').val();
		$("#billing-email").val(email);
		var phone = $( evt.target ).closest('.location').children('.phone-fieldset').children('.wpa_loop-phone').children('.first').children('p').children('.phone').val();
		$("#billing-phone").val(phone);

		$( evt.target ).closest('.location').children('.copy-confirm').show();
		return false;
	});

	// update geolocation data    
	$(document).on( 'change', '.trigger-geolocation', function( evt ) {
		console.log('trigger-geolocation');
		var street = $( evt.target ).closest('.address-data').children('.address-wrapper').children('textarea').val();
		var city = $( evt.target ).closest('.address-data').children('.city-wrapper').children('p').children('.city').val();
		var state = $( evt.target ).closest('.address-data').children('.state-wrapper').children('p').children('.state').val();
		var zip = $( evt.target ).closest('.address-data').children('.zip-wrapper').children('p').children('.zip').val(); 
		var country = $( evt.target ).closest('.address-data').children('.country-wrapper').children('p').children('.country').val(); 
		var address = street + ' ' + city + ', ' + state + ' ' + zip + country;
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({ 'address': address }, function(results, status) {
		  if (status == google.maps.GeocoderStatus.OK) {
		    var latitude = results[0].geometry.location.A;
		    var longitude = results[0].geometry.location.F;
		    $( evt.target ).closest('.address-data').children('.geolocation-data').children('.latitude').val(latitude);
			$( evt.target ).closest('.address-data').children('.geolocation-data').children('.longitude').val(longitude);   
		  } else {
		    var latitude = 0;
		    var longitude = 0;
		    $( evt.target ).closest('.address-data').children('.geolocation-data').children('.latitude').val(latitude);
			$( evt.target ).closest('.address-data').children('.geolocation-data').children('.longitude').val(longitude);   
		  }

		});
	});

// Preview Map



	$(document).on( 'click', '.preview-map', function( evt ) {
		evt.preventDefault();

		// show the nearest .map-canvas
		$( evt.target ).closest('.geolocation-data').children('.map-canvas').show();
		// show the nearest "change coordinates" button
		$( evt.target ).closest('.geolocation-data').children('.custom-coords').show();
		
		// make it show custom coords, if they are entered, and if not, then show these coords
		var latitude = parseFloat($( evt.target ).closest('.geolocation-data').children('.enter-custom-coords').children('.custom-coords-fields').children('.custom-latitude').val());
		if( latitude !== latitude ) { // http://adripofjavascript.com/blog/drips/the-problem-with-testing-for-nan-in-javascript.html
			var latitude = parseFloat($( evt.target ).closest('.geolocation-data').children('.latitude').val());
		}
		var longitude = parseFloat($( evt.target ).closest('.geolocation-data').children('.enter-custom-coords').children('.custom-coords-fields').children('.custom-longitude').val());
		if( longitude !== longitude ) { 
			var longitude = parseFloat($( evt.target ).closest('.geolocation-data').children('.longitude').val());
		}
		var canvas = $( evt.target ).closest('.geolocation-data').children('.map-canvas');


		initialize(latitude, longitude, canvas);

		function initialize(latitude, longitude, canvas) {
		  var myLatLng = {lat: latitude, lng: longitude};

		  map = new google.maps.Map(canvas[0], {
		    zoom: 13,
		    center: myLatLng
		  });

		  marker = new google.maps.Marker({
		    position: myLatLng,
		    map: map,
		  });

		}

	});



	// change latitude and longitude
	$(document).on( 'click', '.custom-coords', function( evt ) {
		evt.preventDefault();

		// show the nearest .enter-custom-coords
		$( evt.target ).closest('.geolocation-data').children('.enter-custom-coords').show();

	});

	$(document).on( 'click', '.update-map', function( evt ) {
		evt.preventDefault();
		$( evt.target ).closest('.update-preview').children('.update-reminder').show();

	    var newlatitude = parseFloat($( evt.target ).closest('.custom-coords-fields').children('.custom-latitude').val());
		var newlongitude = parseFloat($( evt.target ).closest('.custom-coords-fields').children('.custom-longitude').val());

	    var newLatLng = new google.maps.LatLng(newlatitude, newlongitude);
	    marker.setPosition(newLatLng)
	    map.panTo( newLatLng );
	});

});
