(function($){$(document).ready(function(){$('#comment_post_ID').each(function(){$this=$(this);pid=$this.val()});$('#pwdbtn').bind('click',function(){$.post(sfw_click_pwd_button.ajaxurl,{action:'sfw_cpb',post_id:pid},function(response){$('#pwdfield').val(response);$('#pwdbtn').remove();$('#comment_ready').html('<strong>Please leave your comment now.</strong>');return false})});$('#comment').keydown(function(){$.post(sfw_client_ip.ajaxurl,{action:'sfw_cip'},function(response){$('#comment_ip').val(response);return false})})})})(jQuery);