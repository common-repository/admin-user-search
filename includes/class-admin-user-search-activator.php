<?php

/**
 * Fired during plugin activation
 */
class Admin_User_Search_Activator {
	
	public static function activate() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/db/class-admin-user-search-create-tables.php';

		$plugin_tables = new Admin_User_Search_Create_Tables();
		$plugin_tables->create_tables_and_datas();

	}

}
