<?php

if( !class_exists('SFW_CLEANUP' ) ) {
	
	class SFW_CLEANUP {
	
		function count_spam() {
			global $wpdb;

			$sql =
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_approved = 'spam'
				";
	
			$count_spam = $wpdb->get_var( $wpdb->prepare( $sql ) );
			
			if( $count_spam == 0 || is_null( $count_spam || empty( $count_spam ) ) ) {
				$count_spam = '0';
			} else {
				$count_spam;
			} 
			
			return $count_spam;
		}
		
		function count_trackbacks() {
			global $wpdb;

			$sql =
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_type = 'trackback' OR comment_type = 'pingback'
				";
	
			$count_trackbacks = $wpdb->get_var( $wpdb->prepare( $sql ) );
			
			if( $count_trackbacks == 0 || is_null( $count_trackbacks || empty( $count_trackbacks ) ) ) {
				$count_trackbacks = '0';
			} else {
				$count_trackbacks;
			}
			
			return $count_trackbacks;
		}
		
		function count_unapproved() {
			global $wpdb;

			$sql =
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_approved = '0'
				";
	
			$count_unapproved = $wpdb->get_var( $wpdb->prepare( $sql ) );
			
			if( $count_unapproved == 0 || is_null( $count_unapproved || empty( $count_unapproved ) ) ) {
				$count_unapproved = '0';
			} else {
				$count_unapproved;
			}
			
			return $count_unapproved;
		}
		
		function delete_spam() {
			global $wpdb;

			$sql =
				"
				DELETE FROM $wpdb->comments
				WHERE comment_approved = 'spam'
				";

			$delete_spam = $wpdb->query( $wpdb->prepare( $sql ) );
		}
		
		function delete_trackbacks() {
			global $wpdb;

			$sql =
				"
				DELETE FROM $wpdb->comments
				WHERE comment_type = 'trackback' OR comment_type = 'pingback'
				";
	
			$delete_trackbacks = $wpdb->query( $wpdb->prepare( $sql ) );
		}
		
		function delete_unapproved() {
			global $wpdb;

			$sql =
				"
				DELETE FROM $wpdb->comments
				WHERE comment_approved = '0'
				";
			
			$delete_unapproved = $wpdb->query( $wpdb->prepare( $sql ) );
		}
		
	}

	$sfw_cleanup = new SFW_CLEANUP();

}

?>