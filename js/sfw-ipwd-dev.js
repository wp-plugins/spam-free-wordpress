/*(function($) {
	$(document).ready(function() {
		// get the comment post id
		$('#comment_post_ID').each(function() {
			$this = $(this);
			pid = $this.val();
		});		
		// get password and remote IP address
		$( '#comment' ).on( 'keydown', function() {
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
		$( '#comment' ).on( 'keydown', function() {
			// Fetch AJAX data
			$.post(sfw_ipwd.ajaxurl, { action: 'sfw_i_pwd', post_id : pid }, function( response ) {
				pwd = $( '.pwddefault' ).val( response.pwd );
				ip = $( '#comment_ip' ).val( response.ip );
				if( pwd && ip ) {
					return false;
				}
			}, 'json');
			// Time to turn off AJAX
			$( '#comment' ).off( 'keydown' );
		});
	});
})(jQuery);