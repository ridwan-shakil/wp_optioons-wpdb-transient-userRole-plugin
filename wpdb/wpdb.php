<?php


// enqueue admin scripts 
function add_wpdb_scripts($screen) {
    wp_enqueue_script('rs_wpdb_script', plugin_dir_url(__FILE__) . 'assets/wpdb.js', ['jquery'], '1.0', true);

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
        $table = $wpdb->prefix . 'persons';
        $task = $_POST['data'];
        switch ($task) {
            case 'add-new-data':
                $data = array('name' => "Sakil", 'email' => 'ridwan@gmail.com', 'age' => 24);
                $format = array('%s', '%s', '%d',);
                $wpdb->insert($table, $data, $format);  // Alloways insert data
                echo "data inserted successfully ID: " . $wpdb->insert_id;
                break;
            case 'replace-or-insert':
                $data = array(
                    'id' => 7,
                    "name" => "Sayem",
                    "email" => "Sayem@gmail.com"
                );
                $format = array(
                    '%d',
                    '%s',
                    '%s',
                );
                $wpdb->replace($table, $data, $format); // Replace if id found OR inserts
                echo "data replaced successfully ID: " . $wpdb->insert_id;
                break;
            case 'update-data':
                $data = array(
                    'name' => 'Jorina',
                    'email' => 'jorina@gmail.com',
                );
                $format = array(
                    '%s',
                    '%s',
                );
                $result = $wpdb->update($table, $data, ['id' => 1], $format, ['%d']); // updates if id is found OR does nothing 
                echo $result ? "Data updated successfully" : "Data cannot be updated";

                break;
            case 'load-single-data':
                $prepared_query = $wpdb->prepare("SELECT * FROM {$table} WHERE id=12");
                $result =  $wpdb->get_row($prepared_query, ARRAY_A);
                print_r($result);

                break;
            case 'load-multiple-row':
                $prepared_query = $wpdb->prepare("SELECT * FROM {$table}");
                $result = $wpdb->get_results("SELECT * FROM {$table}", ARRAY_A);
                print_r($result[0]);
                echo "==== Here email is the key ==== ";

                $prepared_query = $wpdb->prepare("SELECT email ,name FROM {$table}");
                $result = $wpdb->get_results($prepared_query, OBJECT_K);
                print_r($result);
                break;
            case 'add-multiple-row':
                $persons = array(
                    array('name' => 'Nahid', 'email' => 'nahid@gmail.com'),
                    array('name' => 'Rony', 'email' => 'rony@gmail.com'),
                );
                foreach ($persons as $person) {
                    $result =  $wpdb->insert($table, $person);
                    echo  $result ? ' Successfully inserted ' : "Could not insert";
                };
                break;
            case 'prepared-statement':  // prepare statement sanitizes the sql query
                $email = "ridwan@gmail.com";
                $prepared_statement = $wpdb->prepare("SELECT * FROM {$table} WHERE email = '$email'");
                $result = $wpdb->get_results($prepared_statement, ARRAY_A);
                print_r($result);
                break;
            case 'display-single-column':   // gets only one column
                $prepared_query = $wpdb->prepare("SELECT email FROM {$table}");
                $result = $wpdb->get_col($prepared_query);
                print_r($result);
                break;
            case 'display-variable':
                $result = $wpdb->get_var("SELECT COUNT(*) FROM {$table} ");
                echo " Number of users: " . $result;

                $result = $wpdb->get_var("SELECT name , email FROM {$table} ", 0,  2);
                echo "------- Name of 2nd user: " . $result;
                //("SELECT name(0) , email(1)  ", 1 (col), 4 (row)); //Data of 1st column 4th row , Counts from 0

                break;
            case 'delete-data':
                $id = 11;
                $result = $wpdb->delete($table, array('id' => $id), array('%d')); //delete 1 row
                //delete multiple row
                // $prepared_query = $wpdb->prepare("DELETE FROM {$table} WHERE id < %s", $id);
                // $result = $wpdb->query($prepared_query);

                echo  $result ? "Data deleted successfully" : "Data cannot be deleted";
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
            <button class="wpdb-btn" data-action="replace-or-insert">Replace or insert</button>
            <button class="wpdb-btn" data-action="update-data">Update data</button>
            <button class="wpdb-btn" data-action="load-single-data">Load single row</button>
            <button class="wpdb-btn" data-action="load-multiple-row">Load multiple row</button>
            <button class="wpdb-btn" data-action="add-multiple-row">Add multiple row</button>
            <button class="wpdb-btn" data-action="prepared-statement">Prepared statement</button>
            <button class="wpdb-btn" data-action="display-single-column">Display single column</button>
            <button class="wpdb-btn" data-action="display-variable">Display variable</button>
            <button class="wpdb-btn" data-action="delete-data">Delete data</button>
        </div>
        <!-- result  -->
        <div class="wpdb-right-sidebar">
            <div class="result-section">
                <h2>" Data from wp_wpdb table "</h2>
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
