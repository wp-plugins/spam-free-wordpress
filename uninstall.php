<?php

// Make sure that we are uninstalling
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

global $wpdb;

// Removes all Spam Free Wordpress data from the database
delete_option( 'spam_free_wordpress' );
//delete_option( 'sfw_spam_hits' );
delete_option('sfw_version');
$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'sfw_comment_form_password'");

// The post comment passwords can be deleted also using the following SQL statement.
// DELETE from wp_postmeta WHERE meta_key = "sfw_comment_form_password" ;

?>