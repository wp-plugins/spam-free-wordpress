// For jQuery older than version 1.7, and WordPress prior to version 3.3
/*
(function($) {
	$(document).ready(function() {
		// get the comment post id
		$('#comment_post_ID').each(function() {
			$this = $(this);
			pid = $this.val();
		});		
		// get password and remote IP address
		$( '#comment' ).bind( 'keydown', function() {
			$.post(sfw_ipwd.ajaxurl, { action: 'sfw_i_pwd', post_id : pid }, function( response ) {
				$( '.pwddefault' ).val( response );
			});
			$.post(sfw_client_ip.ajaxurl, { action: 'sfw_cip' }, function( response ) {
				$( '#comment_ip' ).val( response );
			});
		});
	});
})(jQuery);
*/

(function($) {
	$(document).ready(function() {
		// get the comment post id
		$('#comment_post_ID').each(function() {
			$this = $(this);
			pid = $this.val();
		});
		// get password and remote IP address
		$( '#comment' ).bind( 'keydown', function() {
			$.post(sfw_ipwd.ajaxurl, { action: 'sfw_i_pwd', post_id : pid }, function( response ) {
				$( '.pwddefault' ).val( response.pwd );
				$( '#comment_ip' ).val( response.ip );
			}, 'json');
		});
	});
})(jQuery);