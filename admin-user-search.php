<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://adminusersearch.com
 * @since             1.0.0
 * @package           Admin_User_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Admin User Search
 * Plugin URI:        
 * Description:       Search users on wpadmin using firstname, lastname, email and more.
 * Version:           1.0.2
 * Author:            AdminUserSearch.com
 * Author URI:        https://adminusersearch.com
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       TBD
 * Domain Path:       /languages
 */

if ( !function_exists( 'aus_fs' ) ) {
    // Create a helper function for easy SDK access.
    function aus_fs()
    {
        global  $aus_fs ;
        
        if ( !isset( $aus_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_9755_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_9755_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $aus_fs = fs_dynamic_init( array(
                'id'             => '9755',
                'slug'           => 'admin-user-search',
                'premium_slug'   => 'Admin-User-Search-premium',
                'type'           => 'plugin',
                'public_key'     => 'pk_b7fe5a6d62f7beca79421fe389eee',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => false,
                'menu'           => array(
                'slug'    => 'search-users',
                'support' => false,
                'parent'  => array(
                'slug' => 'users.php',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $aus_fs;
    }
    
    // Init Freemius.
    aus_fs();
    // Signal that SDK was initiated.
    do_action( 'aus_fs_loaded' );
}

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-admin-user-search-activator.php
 */
function activate_admin_user_search()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-user-search-activator.php';
    Admin_User_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-admin-user-search-deactivator.php
 */
function deactivate_admin_user_search()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-user-search-deactivator.php';
    Admin_User_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_admin_user_search' );
register_deactivation_hook( __FILE__, 'deactivate_admin_user_search' );
function aus_fs_uninstall_cleanup()
{
    /**
     * Fired when the plugin is uninstalled.
     *
     * When populating this file, consider the following flow
     * of control:
     *
     * - This method should be static
     * - Check if the $_REQUEST content actually is the plugin name
     * - Run an admin referrer check to make sure it goes through authentication
     * - Verify the output of $_GET makes sense
     * - Repeat with other user roles. Best directly by using the links/query string parameters.
     * - Repeat things for multisite. Once for a single site in the network, once sitewide.
     *
     * This file may be updated more in future version of the Boilerplate; however, this is the
     * general skeleton and outline for how the file should work.
     *
     * For more information, see the following discussion:
     * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
     *
     * @link       https://adminusersearch.com
     * @since      1.0.0
     *
     * @package    Admin_User_Search
     */
    // If uninstall not called from WordPress, then exit.
    if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
    }
}

aus_fs()->add_action( 'after_uninstall', 'aus_fs_uninstall_cleanup' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-admin-user-search.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
// function run_admin_user_search() {
// 	$plugin = new Admin_User_Search();
// 	$plugin->run();
// }
// run_admin_user_search();
/********************************************
 * THIS ALLOW YOU TO ACCESS YOUR PLUGIN CLASS
 * eg. in your template/outside of the plugin.
 *
 * Of course you do not need to use a global,
 * you could wrap it in singleton too,
 * or you can store it in a static class,
 * etc...
 *
 * @tutorial access_plugin_and_its_methodes_later_from_outside_of_plugin.php
 */
global  $pbt_prefix_admin_user_search ;
$pbt_prefix_admin_user_search = new Admin_User_Search();
$pbt_prefix_admin_user_search->run();
// END THIS ALLOW YOU TO ACCESS YOUR PLUGIN CLASS