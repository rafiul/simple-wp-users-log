<?php

if (!defined('ABSPATH')) {
    exit;
}
define( 'RA_URL', plugins_url( '/', __FILE__ ) );

add_action( 'admin_init', 'ra_enqueue_style' );

function ra_enqueue_style() {
    wp_enqueue_style( 'rt-preview', RA_URL  . 'plugin-style.css', array());
}


include_once(ABSPATH . 'wp-includes/pluggable.php');

global $jal_db_version;
$jal_db_version = '1.0';

/********* Create Table  *********/
function jal_install() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'ra_user_info';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		user_name varchar(50) NOT NULL,
		user_role varchar(50) NOT NULL,
		user_email varchar(50) NOT NULL,
		ip_address varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}
register_activation_hook( __FILE__, 'jal_install' );
jal_install();

/********* Insert User data *********/
add_action('init', 'insert_user_data');

function insert_user_data(){
	if ( is_user_logged_in() ){
        // code
	global $wpdb;
	global $current_user;
	$current_user_id = get_current_user_id();
	$user = new WP_User($current_user_id);
	$modified_date = current_time('mysql');
	$table_name = $wpdb->prefix . "ra_user_info";
	
	if (!empty($user->roles) && is_array($user->roles)) {
		foreach ($user->roles as $user_ra) {
			$user_ra;
		}
	}
	$user_row = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_email='$user->user_email' " );
	//print_r($user_row);
	$row_email="";
	if (!empty($user_row) && is_array($user_row)) {
		foreach($user_row as $row){
			$row_email = $row->user_email;
		}
	}
		if( $row_email == $user->user_email ){
			$wpdb->update( 
				$table_name, 
				array( 
					'date' => $modified_date, 'ip_address' => $_SERVER['REMOTE_ADDR']
				), 
				
				array( 
				'user_email' => $user->user_email,
				)
			);
		}elseif(isset($row_email->user_email) != $user->user_email ){
			 $insert_query = $wpdb->insert(
				$table_name,
				array(
					'date' => $modified_date,
					'user_name' => $current_user->display_name,
					'user_email' => $user->user_email,
					'user_role' => $user_ra,
					'ip_address' => $_SERVER['REMOTE_ADDR'],
				)
			);
		}
		}
}

//insert_user_data();

/********* Retrive User Data  *********/
function user_logs(){
 global $wpdb;
 $table_name = $wpdb->prefix . "ra_user_info";
 ?>
<div class="wrap">
	<h3>User Activity Log</h3>
	 <table class="wp-list-table widefat fixed striped posts">
	 <thead>
	 <tr>
		<td>ID</td>
		<td>Login Date</td>
		<td>User Name</td>
		<td>User Role</td>
		<td>IP Address</td>
	 </tr>
	 </thead>
	 <?php
	 $user = $wpdb->get_results( "SELECT * FROM $table_name" );
	 foreach ($user as $row){ ?>
		<tr>
			<td>
			   <?php echo $row->id; ?>
			</td>
			<td>
			   <?php 
			   $original_date = $row->date; 
			   $timestamp = strtotime($original_date);
			   echo $new_date = date("d-M-Y h:i:s A", $timestamp);
			   ?>
			</td> 
			<td>
			   <div class="ra-avatar">
				<?php echo get_avatar( $row->user_email, 32 ); ?>
			   </div>  
			   <?php echo $row->user_name; ?>
			</td>
			<td>
			   <?php echo $row->user_role; ?>
			</td>
			<td>
			   <?php echo $row->ip_address; ?>
			</td>
		</tr>
	<?php } ?>
	 <tfoot>
		 <tr>
			<td>ID</td>
			<td>Login Date</td>
			<td>User Name</td>
			<td>User Role</td>
			<td>IP Address</td>
		 </tr>
	 </tfoot>
	</table>
</div>
<?php 
}
