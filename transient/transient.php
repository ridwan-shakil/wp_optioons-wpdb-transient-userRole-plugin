<?php
function rs_transient_scripts($screen) {
    wp_enqueue_script('rs_transient', plugin_dir_url(__FILE__) . 'assets/transient.js', ['jquery'], '1.0', true);
    wp_localize_script(
        'rs_transient',  // this name must be same as enqued script name
        'rs_transient_obj', // this has to be unique for every request
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('transient_nonce'),
        )
    );
}
add_action('admin_enqueue_scripts', 'rs_transient_scripts');


// Handlin Ajax callback 
function clbc_ajax_rs_transient_actions() {
    $verified_nonce = wp_verify_nonce($_POST['nonce'], 'transient_nonce');
    if ($verified_nonce) {
        global $transient;
        $action = $_POST['data']; //Data passed from ajax
        switch ($action) {
            case 'add-new-transient':
                $key = "rs_country";
                $value = "Bangladesh";
                echo "Result : " . set_transient($key, $value);

                break;
            case 'set-expiry':
                $key = "rs_capital";
                $value = "Dhaka";
                $expiry =  60; // 1 minute
                echo "Result :" . set_transient($key, $value, $expiry);
                break;
            case 'display-transient':
                $key1 = "rs_country";
                $key2 = "rs_capital";
                echo "Result : " . get_transient($key1) . "\n";
                echo "Result : " . get_transient($key2);
                break;
            case 'importance-of-===':
                $key = "rs_temparatur_rajshahi";
                $value = 0;
                set_transient($key, $value);
                $result = get_transient($key);
                if ($result === false) {
                    echo "Result not found";
                } else {
                    echo "Rajshahi's temparatur is {$result} Degree";
                };
                break;
            case 'add-complex-transient':
                global $wpdb;
                $table = $wpdb->prefix . 'posts';

                // Get any existing copy of our transient data
                if (false === ($special_query_results = get_transient('rs_posts_title'))) {
                    // It wasn't there, so regenerate the data and save the transient
                    $prepared_query = $wpdb->prepare("SELECT post_title FROM {$table} limit 6");
                    $result = $wpdb->get_results($prepared_query, ARRAY_A);
                    $key = 'rs_posts_title';
                    $expiry = 2 * MINUTE_IN_SECONDS; // expires in 2 minuts
                    $transient = set_transient($key, $result, $expiry);
                };
                // Use the data like you would have normally...
                $result2 = get_transient("rs_posts_title");
                print_r($result2);

                break;
            case 'transient-filter-hook':
                // Look at below for the filter hook
                break;
            case 'delete-transient':
                $key = 'rs_country';
                echo "Before deleting transient :" . get_transient($key) . "\n";
                delete_transient($key);
                echo "After deleting transient :" . get_transient($key);

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

add_action('wp_ajax_rs_transient_actions', 'clbc_ajax_rs_transient_actions');


// add_filter("pre_transient_rs_country", function ($data) {
//     // transient filter does not give transient value to modify  
//     return "Is my country";
// });


// Create a simple function to delete our transient
function rs_edit_term_delete_transient() {
    delete_transient('rs_country');
}
// Add the function to the edit_term hook so it runs when categories/tags are edited
add_action('edit_term', 'rs_edit_term_delete_transient');







// Admin menu page 
function rs_transient_menu() {
    add_menu_page('Transient menu', 'Transient', 'manage_options', 'rs-transient', 'clbc_rs_transient_menu');
}
add_action('admin_menu', 'rs_transient_menu');


function clbc_rs_transient_menu() {
?>
    <div class="container">
        <div class="leftsidevar">
            <button class="transient-btn" data-action="add-new-transient">Add New transient</button>
            <button class="transient-btn" data-action="set-expiry">Set expiry</button>
            <button class="transient-btn" data-action="display-transient">Display transient</button>
            <button class="transient-btn" data-action="importance-of-===">Importance of ===</button>
            <button class="transient-btn" data-action="add-complex-transient">Add complex transient</button>
            <button class="transient-btn" data-action="transient-filter-hook">Transient filter hook</button>
            <button class="transient-btn" data-action="delete-transient">Delete transient</button>
        </div>
        <!-- result  -->
        <div class="transient-right-sidebar">
            <div class="result-section">
                <h2>Transient values " Data from wp_options table "</h2>
                <pre>
        <div class="transient-result">
            <!-- Result will be displayed here through Ajax -->
        </div>
        </pre>
            </div>
        </div>
    </div>
<?php
}
