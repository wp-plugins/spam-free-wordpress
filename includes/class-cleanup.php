<?php

if( !class_exists('SFW_CLEANUP' ) ) {
	
	class SFW_CLEANUP {
	
		public function count_spam() {
			global $wpdb;

			$sql =
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_approved = %s
				";
	
			$count_spam = $wpdb->get_var( $wpdb->prepare( $sql, 'spam' ) );
			
			if( $count_spam == 0 || is_null( $count_spam || empty( $count_spam ) ) ) {
				$count_spam = '0';
			} else {
				$count_spam;
			} 
			
			return $count_spam;
		}
		
		public function count_trackbacks() {
			global $wpdb;

			$sql =
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_type = %s OR comment_type = %s
				";
	
			$count_trackbacks = $wpdb->get_var( $wpdb->prepare( $sql, 'trackback', 'pingback' ) );
			
			if( $count_trackbacks == 0 || is_null( $count_trackbacks || empty( $count_trackbacks ) ) ) {
				$count_trackbacks = '0';
			} else {
				$count_trackbacks;
			}
			
			return $count_trackbacks;
		}
		
		public function count_unapproved() {
			global $wpdb;

			$sql =
				"
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_approved = %s
				";
	
			$count_unapproved = $wpdb->get_var( $wpdb->prepare( $sql, '0' ) );
			
			if( $count_unapproved == 0 || is_null( $count_unapproved || empty( $count_unapproved ) ) ) {
				$count_unapproved = '0';
			} else {
				$count_unapproved;
			}
			
			return $count_unapproved;
		}
		
		public function delete_spam() {
			global $wpdb;

			$sql =
				"
				DELETE FROM $wpdb->comments
				WHERE comment_approved = %s
				";

			$delete_spam = $wpdb->query( $wpdb->prepare( $sql, 'spam' ) );
		}
		
		public function delete_trackbacks() {
			global $wpdb;

			$sql =
				"
				DELETE FROM $wpdb->comments
				WHERE comment_type = %s OR comment_type = %s
				";
	
			$delete_trackbacks = $wpdb->query( $wpdb->prepare( $sql, 'trackback', 'pingback' ) );
		}
		
		public function delete_unapproved() {
			global $wpdb;

			$sql =
				"
				DELETE FROM $wpdb->comments
				WHERE comment_approved = %s
				";
			
			$delete_unapproved = $wpdb->query( $wpdb->prepare( $sql, '0' ) );
		}
		
	}

}

$sfw_cleanup = new SFW_CLEANUP();

?>