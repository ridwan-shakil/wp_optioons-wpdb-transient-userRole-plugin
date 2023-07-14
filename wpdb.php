<?php

// enqueue admin scripts 
function add_wpdb_scripts($screen) {
    wp_enqueue_script('rs_wpdb_script', plugin_dir_url(__FILE__) . 'wpdb.js', ['jquery'], '1.0', true);

    wp_localize_script(
        'rs_wpdb_script',  // this name must be same as enqued script name
        'wpdb_ajax_obj', // this has to be unique for every request
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wpdb_nonce'),
        )
    );
}
add_action('admin_enqueue_scripts', 'add_wpdb_scripts');

// Handlin Ajax callback 
function clbc_ajax_rs_wpdb_actions() {
    $verified_nonce = wp_verify_nonce($_POST['nonce'], 'wpdb_nonce');
    if ($verified_nonce) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'persons';
        $task = $_POST['data'];
        switch ($task) {
            case 'add-new-data':
                $wpdb->insert($table_name, array(
                    'name' => "Sakil",
                    'email' => 'ridwan@gmail.com'
                ));

                break;
            case 'load-data':
                # code...
                break;
            case 'add-multiple-row':
                # code...
                break;
            case 'search-data':
                # code...
                break;
            case 'display-single-row':
                # code...
                break;
            case 'display-single-column':
                # code...
                break;
            case 'display-variable':
                # code...
                break;
            case 'update-data':
                # code...
                break;
            case 'replace-data':
                # code...
                break;
            case 'delete-dat':
                # code...
                break;

            default:
                # code...
                break;
        }
    } else {
        echo "Access denied";
    };


    die();
};

add_action('wp_ajax_rs_wpdb_actions', 'clbc_ajax_rs_wpdb_actions');




function add_wpdb_menu_page() {
    add_menu_page('wpdsb', 'Wpdb Menu', 'manage_options', 'wpdb-menu', 'clbc_wpdb_menu_page');
}
add_action('admin_menu', 'add_wpdb_menu_page');

function clbc_wpdb_menu_page() {

?>
    <div class="container">
        <div class="leftsidevar">
            <button class="wpdb-btn" data-action="add-new-data">Add New Data</button>
            <button class="wpdb-btn" data-action="load-data">Load data</button>
            <button class="wpdb-btn" data-action="add-multiple-row">Add multiple row</button>
            <button class="wpdb-btn" data-action="search-data">Search data</button>
            <button class="wpdb-btn" data-action="display-single-row">Display single row</button>
            <button class="wpdb-btn" data-action="display-single-column">Display single column</button>
            <button class="wpdb-btn" data-action="display-variable">Display variable</button>
            <button class="wpdb-btn" data-action="update-data">Update data</button>
            <button class="wpdb-btn" data-action="replace-data">Replace data</button>
            <button class="wpdb-btn" data-action="delete-dat">Delete data</button>
        </div>
        <!-- result  -->
        <div class="wpdb-right-sidebar">
            <div class="result-section">
                <h2>Options value " Data from wp_options table "</h2>
                <pre>
        <div class="wpdb-result">
            <!-- Result will be displayed here through Ajax -->
        </div>
        </pre>
            </div>
        </div>
    </div>
<?php
}
