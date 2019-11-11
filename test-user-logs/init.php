<?php
/*
Plugin Name: Test User Logs
Plugin URI:https://wordpress.org/plugins/
Description: A simple user log plugins.
Author: B.M. Rafiul Alam
Author URI: https://themeforest.net/user/themezbyte/portfolio
Text Domain: ra-wp
Version: 1.0
*/

//======== Admin Menu ==========
add_action('admin_menu', 'user_log_menu');
	function user_log_menu() {
		add_menu_page( 'User Logs', 'User Logs', 'manage_options', 'user-list.php', 'user_logs', 'dashicons-admin-users', 50  );
	}


//=========Hook Page Menu ============
if ( file_exists( dirname( __FILE__ ) . '/user-list.php' ) ) {
	require_once dirname( __FILE__ ). '/user-list.php';
}
