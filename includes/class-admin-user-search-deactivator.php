<?php

/**
 * Fired during plugin deactivation
 */
class Admin_User_Search_Deactivator {

	public static function deactivate() {

		global $wpdb;

		$table_name = $wpdb->prefix .  'admin_user_search_logs';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);

	}

}
