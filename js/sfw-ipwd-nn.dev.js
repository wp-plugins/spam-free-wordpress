/*jslint browser: true*/
/*global $, jQuery, alert */
			/*
			 * TODO later
			 * Display modal dialog error message rather than alert
			 * http://jqueryui.com/dialog/#default
			 * http://dinbror.dk/bpopup/
			 */
jQuery(function() {
	// get the comment post id
	var sfw_pid = jQuery( '#comment_post_ID' ).val();
	
	// get password and remote IP address
	jQuery( '#comment' ).one( 'keydown', function() {
		// Error notification
		jQuery( this ).ajaxError( function() {
			alert( "Spam Free Wordpress disabled the comment form because it could not retrieve the password from the server. It may be necessary to do one, or all, of the following. Turn on the Old Password Fields option, turn off Nonce Security, or to turn on Generate Comment Form." );
			jQuery("input[type='submit']" ).attr("disabled", true);
		});
		// Fetch AJAX data, and send nonce for authentication
		var sfw_ajax_data = {
			action : 'sfw_i_pwd',
			post_id : sfw_pid
		};
		jQuery.post( sfw_ipwd_script.sfw_ajaxurl, sfw_ajax_data, function( response ) {
			jQuery( '.pwddefault' ).val( response.pwd );
			jQuery( '#comment_ip' ).val( response.ip );
		}, 'json');
	});
});