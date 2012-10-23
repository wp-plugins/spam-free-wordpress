/*jslint browser: true*/
/*global $, jQuery, alert */
jQuery(function() {
	// get jQuery version, and the comment post id
	var jver = jQuery.fn.jquery, sfw_pid = jQuery('#comment_post_ID').val();
	
	if( typeof sfw_pid !== 'string' ) {
		alert('Spam Free Wordpress could not find the Post ID.');
	} else if( jver >= '1.7' ) {
		// get password and remote IP address
		jQuery( '#comment' ).on( 'keydown', function(e) {

			alert('.on is in jQuery version ' + jver);
			
			// Fetch AJAX data
			jQuery.post(sfw_ipwd.sfw_ajaxurl, { action: 'sfw_i_pwd', post_id : sfw_pid }, function( response ) {
				jQuery( '.pwddefault' ).val( response.pwd );
				jQuery( '#comment_ip' ).val( response.ip );
			}, 'json');
			// Time to turn off AJAX for the keydown event
			jQuery( this ).off( e );
		});
	} else {	
		jQuery( '#comment' ).bind( 'keydown', function(e) {

			alert('.on is NOT in jQuery version ' + jver);
		
			// Fetch AJAX data
			jQuery.post(sfw_ipwd.sfw_ajaxurl, { action: 'sfw_i_pwd', post_id : sfw_pid }, function( response ) {
				jQuery( '.pwddefault' ).val( response.pwd );
				jQuery( '#comment_ip' ).val( response.ip );
			}, 'json');
			// Time to turn off AJAX for the keydown event
			jQuery( this ).unbind( e );
		});
	}
});