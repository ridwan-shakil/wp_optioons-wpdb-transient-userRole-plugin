<?php






function add_wpdb_menu_page() {
    add_menu_page('wpdsb', 'Wpdb Menu', 'manage_options', 'wpdb-menu', 'clbc_wpdb_menu_page');
}
add_action('admin_menu', 'add_wpdb_menu_page');

function clbc_wpdb_menu_page() {

?>
    <div class="container">
        <div class="leftsidevar">
            <button class="wpdb-btn" data-task="add-option">Add option</button>
            <button class="wpdb-btn" data-task="add-array-option">Add array option</button>
            <button class="wpdb-btn" data-task="display-option-array">Display array option -></button>
            <button class="wpdb-btn" data-task="display-saved-option">Display option -></button>
            <button class="wpdb-btn" data-task="add-filter-to-option">Add filter to option</button>
            <button class="wpdb-btn" data-task="update-option">Update option</button>
            <button class="wpdb-btn" data-task="update-array-option">Update array option</button>
            <button class="wpdb-btn" data-task="delete-option">Delete option</button>
            <button class="wpdb-btn" data-task="export-option">Export option</button>
            <button class="wpdb-btn" data-task="import-option">Import option</button>
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
