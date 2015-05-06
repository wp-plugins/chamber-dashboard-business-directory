jQuery(document).ready(function ($) {
// Copy location information to billing metabox 
	$('.billing-copy').click(function (evt) {  // this is what triggers the function   
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

	// When someone picks a membership level, add the price to the total    
	$('.trigger-geolocation').change(function (evt) {  // this is what triggers the function        
		var street = $( evt.target ).closest('.address-data').children('.address-wrapper').children('textarea').val();
		var city = $( evt.target ).closest('.address-data').children('.city-wrapper').children('p').children('.city').val();
		var state = $( evt.target ).closest('.address-data').children('.state-wrapper').children('p').children('.state').val();
		var zip = $( evt.target ).closest('.address-data').children('.zip-wrapper').children('p').children('.zip').val(); 
		var address = street + ' ' + city + ', ' + state + ' ' + zip;
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({ 'address': address }, function(results, status) {
		  if (status == google.maps.GeocoderStatus.OK) {
		    var latitude = results[0].geometry.location.A;
		    var longitude = results[0].geometry.location.F;
		    $( evt.target ).closest('.address-data').children('.geolocation-data').children('.latitude').val(latitude);
			$( evt.target ).closest('.address-data').children('.geolocation-data').children('.longitude').val(longitude);   
		  }

		});
	});


});