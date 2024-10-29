<?php


class Admin_User_Search_Create_Tables {
    
    private $admin_user_search_db_version;
    private $wpdb;


    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->admin_user_search_db_version = '1.0';
    }

    private function create_admin_user_search_tables() {


        $table_name = $this->wpdb->prefix . 'admin_user_search_logs';
        
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            
            aus_id mediumint(9) NOT NULL AUTO_INCREMENT,

            aus_chunk_key              varchar(100),
            aus_file_name              text,
            aus_user_id                mediumint(20),
         
            created datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY  (aus_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( 'admin_user_search_db_version', $this->admin_user_search_db_version );
    }

    private function create_users_cache(){



    }


    public function create_tables_and_datas() {
        
        $this->create_admin_user_search_tables();

        $this->create_users_cache();
       
    }

}