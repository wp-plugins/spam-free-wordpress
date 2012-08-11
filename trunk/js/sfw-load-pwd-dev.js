(function($) {
	$(document).ready(function() {
		// Only able to use if theme is using post class to identify post-id
		/*
		$('.post').each(function() {
			$this = $(this);
			id = $this.attr('id').replace('post-', '');
		});
		*/
		// Grab post-id from hidden comment form field 
		$('#comment_post_ID').each(function() {
			$this = $(this);
			pid = $this.val();
		});
		$( '#pwdbtn' ).on( 'click', function() {
			$.post(sfw_pwd.ajaxurl, { action: 'sfw_ajax_hook', post_id : pid }, function( response ) {
				$( '#pwdfield' ).val( response );
				$( '#pwdbtn' ).remove();
				$( '#comment_ready' ).html('<strong>Please leave your comment now.</strong>');
				return false;
			});
		});
		$( '#comment' ).keydown(function() {
			$.post(sfw_client_ip.ajaxurl, { action: 'sfw_ajax_client_ip_hook' }, function( response ) {
				$( '#comment_ip' ).val( response );
				return false;
			});
		});
	});
})(jQuery);


/* Notes

http://stackoverflow.com/questions/890090/jquery-call-function-after-load
http://api.jquery.com/click/
To put it all together, the following code simulates a click when the document finishes loading:

(function($) {
	$(document).ready(function() {
		$('#button').click();
		then .post ajax request  for most popularar post counter
 	});
})(jQuery);

*/