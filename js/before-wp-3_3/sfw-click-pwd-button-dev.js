// For jQuery older than version 1.7, and WordPress prior to version 3.3
(function($) {
	$(document).ready(function() {
		// Grab post-id from hidden comment form field 
		$('#comment_post_ID').each(function() {
			$this = $(this);
			pid = $this.val();
		});
		
		// get password
		$( '#pwdbtn' ).bind( 'click', function() {
			$.post(sfw_click_pwd_button.ajaxurl, { action: 'sfw_cpb', post_id : pid }, function( response ) {
				$( '#pwdfield' ).val( response );
				$( '#pwdbtn' ).remove();
				$( '#comment_ready' ).html('<strong>Please leave your comment now.</strong>');
				return false;
			});
		});
		
		// get remote IP address
		$( '#comment' ).keydown(function() {
			$.post(sfw_client_ip.ajaxurl, { action: 'sfw_cip' }, function( response ) {
				$( '#comment_ip' ).val( response );
				return false;
			});
		});
	});
})(jQuery);