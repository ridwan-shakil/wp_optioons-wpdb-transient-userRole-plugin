<?php
/* 
* Plugin Name:       wp_ptions & Wpdb oprations 
* Plugin URI:        
* Description:       store and retrive data from database options table
* Version:  1.0.0
* Requires at least: 5.2
* Requires PHP: 7.2
* Author:            MD.Ridwan
* Author URI:        
* License:           GPL v2 or later
* License URI:       https: //www.gnu.org/licenses/gpl-2.0.html
* Update URI:        
* Text Domain:       options-api
* Domain Path:       /languages
*/
defined('ABSPATH') or die('Cannot access pages directly.');
require_once('wpdb/wpdb.php');
require_once("transient/transient.php");
require_once("user_roles/user_roles.php");

// Plugin activation hook
register_activation_hook(__FILE__, 'create_persons_table');

// Function to create the persons table
function create_persons_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'persons';

    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // Table doesn't exist, create it--
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            age INT(11) ,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}


/**
 * Enqueue scripts for Adminsitration area
 */

function my_enqueue($screen) {
    // if ('options_api.php' !== $screen) {
    //     return;
    // }
    wp_enqueue_style('options_api', plugin_dir_url(__FILE__) . 'options/assets/options.css');
    wp_enqueue_script('ajax-script', plugins_url('options/assets/options_api.js', __FILE__), array('jquery'), '1.0.0', true);

    wp_localize_script(
        'ajax-script',
        'my_ajax_obj',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('title_example'),
        )
    );
}
add_action('admin_enqueue_scripts', 'my_enqueue');



/**
 * The function "crud_on_options_table" is added as an action to be executed when the
 * "rs_actions" AJAX request is made in WordPress.
 */

function crud_on_options_table() {
    $nonce_verified = wp_verify_nonce($_POST['_ajax_nonce'], 'title_example');
    echo 'options_nonce_verified' . $nonce_verified;
    if ($nonce_verified) {
        $task = $_POST['data'];
        switch ($task) {
            case 'add-option':
                $key = 'rs_country';
                $value = 'Bangladesh';
                echo add_option($key, $value); // add option if doesn't exist

                break;
            case 'add-array-option':
                $key = 'rs_countries';
                $value = ['India', 'South Africa', 'America', 'Soudiarab', 'Albania', 'Naizaria', 'Bangladesh'];
                echo update_option($key, $value);
                // save data in json format
                $key = 'rs_json_countries';
                $value2 = json_encode($value); //saving data in json format increase readiblity of database
                echo add_option($key, $value2);
                break;
            case 'display-saved-option':
                $key = 'rs_country';
                echo get_option($key); // display options data

                break;
            case 'display-option-array':
                $key = 'rs_countries';
                print_r(get_option($key));
                echo '<br />--------------- Data saved in json format-------------<br />';
                $key = 'rs_json_countries';
                $result = json_decode(get_option($key));
                print_r($result);
                break;

            case 'add-filter-to-option':
                $key = 'rs_country';
                echo get_option($key);
                break;
            case 'update-option':
                $key = 'rs_country';
                $value = 'India';
                echo update_option($key, $value); // update option if exists or create ( most useful)
                break;
            case 'update-array-option':
                $key = 'rs_countries';
                $value = ['Nepal', 'Vutan', 'Nagaland'];
                echo update_option($key, $value);
                break;
            case 'delete-option':
                $key = 'rs_country';
                echo delete_option($key);
                break;
            case 'export-option':
                $Key_normal = ['rs_country'];  // all normal keys
                $key_array = ['rs_countries']; // all array keys
                $key_json = ['rs_json_countries']; // all json keys
                $export_data = [];
                foreach ($Key_normal as $key) {
                    $value = get_option($key);
                    $export_data[$key] = $value;
                };
                foreach ($key_array as $key) {
                    $value = get_option($key);
                    $export_data[$key] = $value;
                }
                foreach ($key_json as $key) {
                    $value = json_decode(get_option($key));
                    $export_data[$key] = $value;
                }
                echo json_encode($export_data);
                break;
            case 'import-option':
                $exported_data = '{"rs_country":"INDIA IS A OVER POPULATED COUNTRY","rs_countries":["Nepal","Vutan","Nagaland"],"rs_json_countries":["India","South Africa","America","Soudiarab","Albania","Naizaria","Bangladesh"]}';
                $array_data = json_decode($exported_data, true);
                print_r($array_data);
                foreach ($array_data as $key => $value) {
                    if ($key == 'rs_json_countries') {
                        $key = json_encode($key);
                    }
                    update_option($key, $value);
                }
                break;

            default:
                echo 'error message for 0add-option';
                break;
        };
    } else {
        echo 'Nonce is not varified';
    }

    wp_die(); // All ajax handlers die when finished
}
add_action('wp_ajax_rs_actions', 'crud_on_options_table');


add_filter('option_rs_country', function ($data) {
    return  strtoupper($data);
});

add_action('admin_menu', 'register_Options_api_menu_page');
function register_Options_api_menu_page() {
    add_menu_page("Wp Options page", 'Wp Options menu', 'manage_options', 'options_api.php', 'clbc_options_api_page', '', null);

    // add_submenu_page('wpdbmenu.php', 'wpdb insert', 'wpdb insert', 'manage_options', 'wpdb_insert.php', 'clbc_wpdb_insert');
};


function clbc_options_api_page() {
?>
    <div class="container">
        <div class="leftsidevar">
            <button class="action-btn" data-task="add-option">Add option</button>
            <button class="action-btn" data-task="add-array-option">Add array option</button>
            <button class="action-btn" data-task="display-option-array">Display array option -></button>
            <button class="action-btn" data-task="display-saved-option">Display option -></button>
            <button class="action-btn" data-task="add-filter-to-option">Add filter to option</button>
            <button class="action-btn" data-task="update-option">Update option</button>
            <button class="action-btn" data-task="update-array-option">Update array option</button>
            <button class="action-btn" data-task="delete-option">Delete option</button>
            <button class="action-btn" data-task="export-option">Export option</button>
            <button class="action-btn" data-task="import-option">Import option</button>
        </div>
        <!-- result  -->
        <div class="right-sidebar">
            <div class="result-section">
                <h2>Options value " Data from wp_options table "</h2>
                <pre>
                <div class="result">
                    <!-- Result will be displayed here through Ajax -->
                </div>
                </pre>
            </div>
        </div>
    </div>


<?php
}
?>