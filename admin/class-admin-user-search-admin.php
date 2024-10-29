<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Admin_User_Search_Admin
{
    private  $admin_user_search ;
    private  $version ;
    public  $main ;
    public function __construct( $admin_user_search, $version, $plugin_main )
    {
        $this->admin_user_search = $admin_user_search;
        $this->version = $version;
        $this->main = $plugin_main;
    }
    
    public function enqueue_styles( $hook_suffix )
    {
        
        if ( $hook_suffix == "users_page_search-users" ) {
            // bootstrap 5 css
            wp_enqueue_style(
                'bootstrap_4_css',
                plugin_dir_url( __FILE__ ) . 'assets/bootstrap-5.1.3.min.css',
                array(),
                $this->version,
                'all'
            );
            // angularjs datatables
            wp_enqueue_style(
                "angular_datatables_css",
                plugin_dir_url( __FILE__ ) . 'assets/angular_datatables.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                'bootstrap_4_datatables',
                plugin_dir_url( __FILE__ ) . 'assets/dataTables.bootstrap4.min.css',
                array(),
                $this->version,
                'all'
            );
            // Select2 and Bootstrap 5 theme
            wp_enqueue_style(
                'select2_min_css',
                plugin_dir_url( __FILE__ ) . 'assets/select2-4.0.13.min.css',
                array(),
                time()
            );
            wp_enqueue_style(
                'select2_bootrap5_theme',
                plugin_dir_url( __FILE__ ) . 'assets/select2-bootstrap5-1.3.0.min.css',
                array(),
                time()
            );
            // Custom css
            wp_enqueue_style(
                'admin_user_search_admin_css',
                plugin_dir_url( __FILE__ ) . 'css/admin-user-search-admin.css',
                array(),
                time()
            );
        }
    
    }
    
    public function enqueue_scripts( $hook_suffix )
    {
        
        if ( $hook_suffix == "users_page_search-users" ) {
            // angularJS
            wp_enqueue_script(
                "angular_min_js",
                plugin_dir_url( __FILE__ ) . 'assets/angular.min.js',
                array( 'jquery' ),
                time()
            );
            // BootstrapJS
            wp_enqueue_script(
                "bootstrap_min_js",
                plugin_dir_url( __FILE__ ) . 'assets/bootstrap-5.1.3.min.js',
                array( 'jquery' ),
                time()
            );
            // AngularJS used libraries
            wp_enqueue_script(
                "angular_animate_js",
                plugin_dir_url( __FILE__ ) . 'assets/angular-animate.js',
                array( 'jquery' ),
                time()
            );
            wp_enqueue_script(
                "angular_sanitize_js",
                plugin_dir_url( __FILE__ ) . 'assets/angular-sanitize.js',
                array( 'jquery' ),
                time()
            );
            wp_enqueue_script(
                "ui_bootstrap",
                plugin_dir_url( __FILE__ ) . 'assets/ui-bootstrap-tpls-3.0.5.min.js',
                array( 'jquery' ),
                time()
            );
            wp_enqueue_script(
                "jquery_dataTables",
                plugin_dir_url( __FILE__ ) . 'assets/jquery.dataTables.min.js',
                array( 'jquery' ),
                time()
            );
            wp_enqueue_script(
                "dataTables_bootstrap4",
                plugin_dir_url( __FILE__ ) . 'assets/dataTables.bootstrap4.min.js',
                array( 'jquery' ),
                time()
            );
            wp_enqueue_script( "angular_datatables_js", plugin_dir_url( __FILE__ ) . '/assets/angular_datatables.js', array( 'jquery' ) );
            wp_enqueue_script( "select2_js", plugin_dir_url( __FILE__ ) . '/assets/select2-4.0.13.min.js', array( 'jquery' ) );
            wp_enqueue_script( "sweetalert_js", plugin_dir_url( __FILE__ ) . '/assets/sweetalert.js', array( 'jquery' ) );
            // Custom script
            wp_enqueue_script(
                $this->admin_user_search,
                plugin_dir_url( __FILE__ ) . 'js/admin-user-search-admin.js',
                array( 'jquery' ),
                time()
            );
            // AJAX
            wp_localize_script( $this->admin_user_search, 'search_users_url', array(
                'url' => admin_url( 'admin-ajax.php' ),
            ) );
            wp_localize_script( $this->admin_user_search, 'aus_get_users_url', array(
                'url' => admin_url( 'admin-ajax.php' ),
            ) );
            wp_localize_script( $this->admin_user_search, 'aus_check_get_users_url', array(
                'url' => admin_url( 'admin-ajax.php' ),
            ) );
        }
    
    }
    
    // Create menu for search page
    function aus_menu()
    {
        add_submenu_page(
            'users.php',
            'Search Users',
            'Search Users',
            'manage_options',
            'search-users',
            array( $this, 'aus_search_page' )
        );
    }
    
    // Create menu for search page
    function admin_bar_menu( WP_Admin_Bar $admin_bar )
    {
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        $logo = '<img src="' . plugin_dir_url( __FILE__ ) . '/img/Transparent-logo.png' . '" style="width:23px; height:23px; padding-top:4px;">';
        $admin_bar->add_menu( array(
            'id'     => 'menu-id',
            'parent' => null,
            'group'  => null,
            'title'  => $logo,
            'href'   => admin_url( 'users.php?page=search-users' ),
            'meta'   => [
            'title' => __( 'Search Users', 'textdomain' ),
        ],
        ) );
    }
    
    // Forms for search users with table results
    public function aus_search_page()
    {
        include_once 'partials/admin-user-search-admin.php';
    }
    
    public function post_search_users()
    {
        ini_set( 'memory_limit', '-1' );
        
        if ( isset( $_POST['aus_keyword'] ) && isset( $_POST['aus_keyword'] ) ) {
            // Init =======================================================================================================
            $response = [];
            global  $wpdb ;
            // table name
            $table = $wpdb->prefix . 'admin_user_search_logs';
            // get current session user
            $current_user = wp_get_current_user();
            // sanitize  ==================================================================================================
            // user inputed keywords
            $aus_search_keyword = sanitize_text_field( $_POST['aus_keyword'] );
            // used for cache filename and loop retreive user datas
            $aus_chunk_key = sanitize_text_field( $_POST['aus_chunk_key'] );
            // total users found also used for progress bar calculations
            $aus_total_users = sanitize_text_field( $_POST['aus_total_users'] );
            // used for offsetting and progress bar
            $aus_current_users_count = sanitize_text_field( $_POST['aus_current_users_count'] );
            // status if its a new search query or cache retrieval
            // after search, user ids are stored in cache file then later retrieve in chunk
            $aus_process_status = sanitize_text_field( $_POST['aus_process_status'] );
            // limit the search query
            $aus_limit_search = sanitize_text_field( $_POST['aus_limit_search'] );
            // limit the number of user details passed from server to client
            $aus_limit_chunk = sanitize_text_field( $_POST['aus_limit_chunk'] );
            // return status to let the front end know if its going to do new query or return user datas
            $response['aus_process_status'] = $aus_process_status;
            // cache file name
            $users_cache = "users-" . $aus_chunk_key . ".cache";
            // cache location
            $users_cache_location = plugin_dir_path( __FILE__ ) . "" . $users_cache;
            // There are 2 process on AUS search
            // 1. query search process
            // - In this process the backend will search via the keyword provided by user or a custom selected metas ( on premium version )
            // - it will only return user IDS.
            // - user IDs are then stored in cache file while saving the record to DB.
            // - return total found to front end - end process.
            // 2. return user datas process.
            // - this will be a loop process
            // - get the previously cached searched user ids using user id and chunk key.
            // - get user details using with chunk limit and using offset.
            // Query search ===============================================================================================
            
            if ( $aus_process_status == "new" ) {
                // delete previous cached user ids
                $existing_cache = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE `aus_chunk_key` != '" . $aus_chunk_key . "'  AND `aus_user_id` =  '" . $current_user->ID . "'" ) );
                
                if ( count( $existing_cache ) > 0 ) {
                    foreach ( $existing_cache as $exist_cache ) {
                        // delete cache file
                        $exist_cache_location = plugin_dir_path( __FILE__ ) . "" . $exist_cache->aus_file_name;
                        $delete = apply_filters( 'wp_delete_file', $exist_cache_location );
                        if ( !empty($delete) ) {
                            @unlink( $delete );
                        }
                    }
                    // delete record
                    $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE aus_chunk_key != '" . $aus_chunk_key . "' AND `aus_user_id` =  '" . $current_user->ID . "' " ) );
                }
                
                $aus_sql = $wpdb->prepare( "SELECT distinct a.ID FROM wp_users a JOIN wp_usermeta b ON a.ID = b.user_id WHERE a.display_name LIKE %s OR a.user_email LIKE %s OR b.meta_value LIKE %s ORDER BY a.ID ASC limit {$aus_limit_search}", [ "%{$wpdb->esc_like( $aus_search_keyword )}%", "%{$wpdb->esc_like( $aus_search_keyword )}%", "%{$wpdb->esc_like( $aus_search_keyword )}%" ] );
                $aus_users = $wpdb->get_results( $aus_sql );
                // SAVE ids to cache file for later use
                $aus_include_ids = [];
                foreach ( $aus_users as $user ) {
                    array_push( $aus_include_ids, $user->ID );
                }
                file_put_contents( $users_cache_location, json_encode( $aus_include_ids ) );
                // SAVE record of this search instance for later use
                $datas = array(
                    'aus_chunk_key' => $aus_chunk_key,
                    'aus_file_name' => $users_cache,
                    'aus_user_id'   => $current_user->ID,
                );
                $format = array(
                    '%s',
                    // practitioner_id
                    '%s',
                    // practitioner_name
                    '%d',
                );
                $wpdb->insert( $table, $datas, $format );
                $response['aus_limit_chunk'] = $aus_limit_chunk;
                $response['total_users'] = count( $aus_include_ids );
            } else {
                $aus_searched_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE `aus_chunk_key` = '" . $aus_chunk_key . "'  AND `aus_user_id` =  '" . $current_user->ID . "'" ) );
                
                if ( count( $aus_searched_ids ) > 0 ) {
                    
                    if ( file_exists( $users_cache_location ) ) {
                        // get file then append new queried users
                        $aus_include_ids = file_get_contents( $users_cache_location );
                        $aus_include_ids = json_decode( $aus_include_ids );
                        // -----------------------------------------------------------------------------------------------------------------
                        $dynamic_number = $aus_limit_chunk;
                        $dynamic_offset = $aus_current_users_count;
                        $response['aus_limit_chunk'] = $dynamic_number;
                        $response['total_users'] = $aus_total_users;
                        $get_users_args = [
                            'orderby' => 'ID',
                            'order'   => 'ASC',
                            'include' => $aus_include_ids,
                            'number'  => $dynamic_number,
                            'offset'  => $dynamic_offset,
                        ];
                        $response['users'] = get_users( $get_users_args );
                        $users = [];
                        foreach ( $response['users'] as $user ) {
                            // set empty role when custom role is missing
                            $role_empty = substr( $user->roles[0], 0, 6 );
                            if ( $role_empty == "ignite" ) {
                                $user->roles = [];
                            }
                            array_push( $users, (object) [
                                'ID'         => $user->ID,
                                'first_name' => $user->first_name,
                                'last_name'  => $user->last_name,
                                'email'      => $user->user_email,
                                'roles'      => $user->roles,
                            ] );
                        }
                        $response['users'] = $users;
                    }
                    
                    // If file exist
                }
                
                // if aus_searched_ids
            }
            
            // else process status is chunking
            echo  json_encode( $response ) ;
            die;
        }
    
    }

}