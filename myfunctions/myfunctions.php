<?php
/**
 * Plugin Name: MyFunctions
 * Plugin URI: https://github.com/PhilipMello/wp-plugin-MyFunctions
 * Description: A plugin to add custom code to the functions.php of the current theme.
 * Version: 1.2
 * Author: Philip Mello & ChatGPT
 * License: GPL2
 */

add_action('admin_menu', 'functions_editor_loader_menu');
add_action('wp_ajax_load_preset_file', 'load_preset_file_ajax');  // Add the AJAX action

function functions_editor_loader_menu() {
    add_menu_page(
        'MyFunctions',                  // Page title
        'MyFunctions',                  // Menu title
        'edit_theme_options',           // Capability
        'myfunctions',                  // Menu slug
        'load_functions_editor_page',   // Function to display content
        'dashicons-editor-code',        // Icon
        20                              // Position
    );
}

function load_functions_editor_page() {
    // Get the current theme's functions.php
    $theme = wp_get_theme();
    $functions_file = get_template_directory() . '/functions.php';
    $presets_dir = plugin_dir_path( __FILE__ ) . 'presets/';

    // Security check - Check if the user has permission to edit themes
    if ( !current_user_can('edit_theme_options') ) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Handle form submission to save changes to functions.php
    if (isset($_POST['save_functions_file'])) {
        // Perform a nonce check for security
        check_admin_referer('save_functions_nonce');

        // Get the content from the POST request and update functions.php
        $new_content = stripslashes($_POST['functions_content']);
        
        // Make sure to sanitize the input before saving to avoid any issues
        if (file_put_contents($functions_file, $new_content)) {
            echo '<div class="updated"><p>' . __('Functions.php has been updated.', 'myfunctions') . '</p></div>';
        } else {
            echo '<div class="error"><p>' . __('Failed to update Functions.php.', 'myfunctions') . '</p></div>';
        }
    }

    // Load the current content of functions.php
    $functions_content = file_get_contents($functions_file);

    // Fetch all .php files from the /presets folder
    $preset_files = glob($presets_dir . '*.php');
    ?>

    <div class="wrap">
        <h1>(MyFunctions) Edit Functions.php for Theme: <?php echo esc_html($theme->get('Name')); ?></h1>
        <form method="post" id="functions-editor-form">
            <?php wp_nonce_field('save_functions_nonce'); ?>
            
            <!-- Dropdown to select a preset file -->
            <label for="preset_file">Load preset:</label>
            <select name="preset_file" id="preset_file">
                <option value=""><?php _e('Select a preset', 'myfunctions'); ?></option>
                <?php foreach ($preset_files as $preset_file) : ?>
                    <?php $preset_filename = basename($preset_file); ?>
                    <option value="<?php echo esc_attr($preset_filename); ?>">
                        <?php echo esc_html($preset_filename); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="button" id="load-preset-btn" class="button" value="Load Preset">
            
            <!-- Textarea to display and edit the functions.php content -->
            <textarea name="functions_content" id="functions_content" rows="20" cols="100" style="width: 100%;"><?php echo esc_textarea($functions_content); ?></textarea>
            <p><input type="submit" name="save_functions_file" id="myfunctions_save_button" class="button button-primary" value="Save Functions.php"></p>
        </form>
    </div>

    <?php
    // Enqueue the AJAX script
    wp_enqueue_style('myfunctions-css', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('functions-editor-ajax', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
    wp_localize_script('functions-editor-ajax', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('load_preset_nonce')
    ));
}

// Handle the AJAX request to load preset content
function load_preset_file_ajax() {
    check_ajax_referer('load_preset_nonce');  // Security check

    if (isset($_POST['preset_file'])) {
        $preset_file = sanitize_text_field($_POST['preset_file']);
        $preset_path = plugin_dir_path(__FILE__) . 'presets/' . $preset_file;

        if (file_exists($preset_path)) {
            $preset_content = file_get_contents($preset_path);
            echo $preset_content;  // Return the content
        } else {
            echo 'Error: File not found.';
        }
    }
    wp_die(); // End AJAX response
}
