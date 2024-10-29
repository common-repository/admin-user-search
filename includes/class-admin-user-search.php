<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */

class Admin_User_Search {


	protected $loader;

	protected $admin_user_search;

	protected $version;

	public $admin;

	public $main;

	public function __construct() {

		$this->admin_user_search = 'admin-user-search';
		$this->version = '1.0.1';

		/*************************************************************
		 * ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE
		 *
		 * @tutorial access_plugin_admin_public_methodes_from_inside.php
		 */
		$this->main = $this;
		// ACCESS PLUGIN ADMIN PUBLIC METHODES FROM INSIDE

		$this->load_dependencies();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Admin_User_Search_Loader. Orchestrates the hooks of the plugin.
	 * - Admin_User_Search_Admin. Defines all hooks for the admin area.
	 * - Admin_User_Search_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-admin-user-search-loader.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin-user-search-admin.php';

		        /**
         * The class responsible for defining all actions for AJAX
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-admin-user-search-ajax.php';



		$this->loader = new Admin_User_Search_Loader();

	}

	private function define_admin_hooks() {


		$this->admin = new Admin_User_Search_Admin( $this->get_admin_user_search(), $this->get_version(), $this->main );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $this->admin, 'aus_menu', 5 );
		$this->loader->add_action( 'admin_bar_menu', $this->admin, 'admin_bar_menu', 1000 );

		$this->loader->add_action('wp_ajax_prefix_post_search_users',  $this->admin, 'post_search_users');
		$this->loader->add_action('wp_ajax_nopriv_prefix_post_search_users',  $this->admin, 'post_search_users');
	
	
	}

	public function run() {
		$this->loader->run();
	}

	public function get_admin_user_search() {
		return $this->admin_user_search;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
