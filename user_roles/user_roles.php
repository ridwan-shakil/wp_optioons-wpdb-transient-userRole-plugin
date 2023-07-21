<?php
// enquing sctipts for user roles admin area 
function rs_user_role_scripts() {
    wp_enqueue_script("rs_user_roles", plugin_dir_url(__FILE__) . "assets/user_role.js", ['jquery'], 'rs_plugin_var', true);
    wp_localize_script(
        'rs_user_roles',  // this name must be same as enqued script name
        'rs_user_roles_obj', // this has to be unique for every request
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('user_roles_nonce'),
        )
    );
}
add_action("admin_enqueue_scripts", "rs_user_role_scripts");


// Handlin Ajax callback 
function ajax_rs_user_roles_actions() {
    $verified_nonce = wp_verify_nonce($_POST['nonce'], 'user_roles_nonce');
    if ($verified_nonce) {
        $data = $_POST['data']; //Data passed from ajax
        switch ($data) {
            case 'get-current-user-details':
                if (is_user_logged_in()) {
                    $user = wp_get_current_user();
                    print_r($user->roles);
                    print_r($user);
                }
                break;
            case 'get-any-user-details':
                $user = new WP_User(2);
                print_r($user);
                break;
            case 'detect-any-user-role':
                # code...
                break;
            case 'get-all-roles-list':
                global $wp_roles;
                foreach ($wp_roles->roles as $role => $value) {
                    echo $role . "\n";
                }
                echo "\n";
                // Alternative way to get all user roles
                $roles = get_editable_roles();
                foreach ($roles as $role => $value) {
                    echo $role . "\n";
                }

                break;
            case 'current-user-capability':
                $current_user = wp_get_current_user();
                print_r($current_user->allcaps);
                break;
            case 'check-user-capability':
                // Check current user capability
                // $cap = 'edit_posts';
                $cap = 'manage_options';
                if (current_user_can($cap)) {
                    echo "Yes current user can " .  $cap . "\n";
                } else {
                    echo "No current user can not " . $cap . "\n";
                }

                // Check any user capabilities
                $user = new WP_User(2);
                $cap = "edit_posts";
                if ($user->has_cap($cap)) {
                    echo "{$user->user_nicename} can  {$cap} \n";
                } else {
                    echo "{$user->user_nickname} can not {$cap} ";
                }
                print_r($user);
                break;
            case 'create-new-user':
                $result = wp_create_user("Ratul", 'ratul1234', 'ratul@gmail.com');
                echo 'Result: ' . $result;
                break;
            case 'assogn-role-to-new-user':
                $user = new WP_User(3); // get any user by id
                $user->remove_role('subscriber');
                $user->add_role('editor');
                print_r($user);
                break;
            case 'login-as-a-user':

                // -----------------------------
                //1st way ,If we know the user id
                // -----------------------------
                // wp_set_auth_cookie(2);
                // -----------------------------
                //2nd Alternative way to login
                // -----------------------------
                // $user = wp_signon([
                //     'user_login' => "ratul",
                //     'user_password' => "ratul1234",
                //     'user_email' => "ratul@gmail.com",
                // ]);
                // if (is_wp_error($user)) {
                //     echo "error while logging ";
                // } else {
                //     wp_set_current_user($user->ID);
                //     echo wp_get_current_user()->user_email;
                // }

                // -----------------------------
                //3rd Alternative way to login
                // -----------------------------
                $user = wp_authenticate('ratul', 'ratul1234');
                if (is_wp_error($user)) {
                    echo "Can not authenticate / login";
                } else {
                    wp_set_current_user('ratul');
                    wp_set_auth_cookie($user->id); // for this method we need to set the auth cookie
                    echo wp_get_current_user()->user_email;
                }

                break;
            case 'find-all-user-from-role':
                $users = get_users(['role' => 'editor', 'orederby' => 'name', 'order' => 'desc']);
                print_r($users);
                break;
            case 'change-user-role':
                $user = new WP_User(3);
                $user->remove_role('editor');
                $user->add_role('author');
                print_r($user);
                break;
            case 'create-new-role':
                // Create a new role 
                $new_role = add_role(
                    "super_admin",
                    "Super Admin",
                    [
                        'edit_posts' => true,
                        'custom_cap_one' => true,
                        'custom_cap_two' => true,
                        'custom_cap_three' => false
                    ]
                );
                echo $new_role ? "New Role added" : "Can't add a new role , Maybe the role already exists. \n";
                // Assign a user to the new role
                $user = new WP_User(3);
                $user->add_role('super_admin');

                // Check the user capabilities
                if ($user->has_cap('custom_cap_one')) {
                    echo "This user has Custom_cap_one capability";
                } else {
                    echo "This user doesn't have Custom_cap_one capability";
                };

                break;
            default:
                echo "Scilence is golden :)";
                break;
        }
    } else {
        echo "Access denied";
    };


    die();
};

add_action('wp_ajax_rs_user_role_actions', 'ajax_rs_user_roles_actions');




// Adding menu page for user roles 
function rs_user_roles_menu() {
    add_menu_page("User roles", "User roles", "manage_options", "user-roles", 'clbc_user_roles_menu_page');
}
add_action("admin_menu", "rs_user_roles_menu");

function clbc_user_roles_menu_page() {
?>

    <div class="container">
        <div class="leftsidevar">
            <button class="user-role-btn" data-action="get-current-user-details">Get current user details</button>
            <button class="user-role-btn" data-action="get-any-user-details">Get any user details</button>
            <button class="user-role-btn" data-action="detect-any-user-role">Detect any user role</button>
            <button class="user-role-btn" data-action="get-all-roles-list">Get all roles list</button>
            <button class="user-role-btn" data-action="current-user-capability">Current user capability</button>
            <button class="user-role-btn" data-action="check-user-capability">Check user capability</button>
            <button class="user-role-btn" data-action="create-new-user">Creat a new user</button>
            <button class="user-role-btn" data-action="assogn-role-to-new-user">Assign role to a new user</button>
            <button class="user-role-btn" data-action="login-as-a-user">Login as a user</button>
            <button class="user-role-btn" data-action="find-all-user-from-role">Find all user from role</button>
            <button class="user-role-btn" data-action="change-user-role">Change user role</button>
            <button class="user-role-btn" data-action="create-new-role">Create new role</button>
        </div>
        <!-- result  -->
        <div class="user-role-right-sidebar">
            <div class="result-section">
                <h2>user-role values " Data from wp_options table "</h2>
                <pre>
                 <div id="user-role-result">
                  <!-- Result will be displayed here through Ajax -->
                 </div>
             
        </pre>
            </div>
        </div>
    </div>


<?php
}
