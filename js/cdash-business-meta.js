jQuery(document).ready(function ($) {
// When someone picks a membership level, add the price to the total    
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
});