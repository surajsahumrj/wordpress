<?php
function medical_blog_post_type() {
    $labels = array(
        'name' => 'Medical Blogs',
        'singular_name' => 'Medical Blog',
        'menu_name' => 'Medical Blogs',
        'add_new_item' => 'Add New Medical Blog',
        'edit_item' => 'Edit Medical Blog',
        'new_item' => 'New Medical Blog',
        'view_item' => 'View Medical Blog',
        'search_items' => 'Search Medical Blogs',
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'thumbnail'),
        'menu_icon' => 'dashicons-plus-alt',
        'show_in_rest' => true,
    );
    register_post_type('medical_blog', $args);
}
add_action('init', 'medical_blog_post_type');

function add_custom_card_excerpt_field() {
    add_meta_box(
        'card_excerpt_meta_box',
        'Card Excerpt',
        'render_card_excerpt_field',
        'medical_blog',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_card_excerpt_field');

function render_card_excerpt_field($post) {
    $value = get_post_meta($post->ID, '_card_excerpt', true);
    wp_nonce_field('save_card_excerpt_data', 'card_excerpt_nonce');
    echo '<label for="card_excerpt_field">Brief Excerpt for the card:</label>';
    echo '<textarea id="card_excerpt_field" name="card_excerpt_field" class="large-text" rows="4">' . esc_textarea($value) . '</textarea>';
}

function save_custom_card_excerpt($post_id) {
    if (!isset($_POST['card_excerpt_nonce']) || !wp_verify_nonce($_POST['card_excerpt_nonce'], 'save_card_excerpt_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['card_excerpt_field'])) {
        update_post_meta($post_id, '_card_excerpt', sanitize_textarea_field($_POST['card_excerpt_field']));
    }
}
add_action('save_post_medical_blog', 'save_custom_card_excerpt');

function medical_blog_selection_meta_box() {
    add_meta_box(
        'medical_blog_selection',
        'Select Medical Blogs to Display',
        'render_medical_blog_selection',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'medical_blog_selection_meta_box');

function render_medical_blog_selection($post) {
    $selected_blogs = get_post_meta($post->ID, '_selected_medical_blogs', true);
    $selected_blogs = is_array($selected_blogs) ? $selected_blogs : array();
    $medical_blogs = get_posts(array(
        'post_type' => 'medical_blog',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    wp_nonce_field('save_medical_blogs_selection', 'medical_blogs_nonce');
    echo '<select name="selected_medical_blogs[]" multiple size="4" style="width:100%;">';
    foreach ($medical_blogs as $blog) {
        $selected = in_array($blog->ID, $selected_blogs) ? 'selected' : '';
        echo '<option value="' . esc_attr($blog->ID) . '" ' . $selected . '>' . esc_html($blog->post_title) . '</option>';
    }
    echo '</select>';
    echo '<p>Hold down the Ctrl (Windows) or Cmd (Mac) key to select multiple blogs.</p>';
}

function save_medical_blogs_selection($post_id) {
    if (!isset($_POST['medical_blogs_nonce']) || !wp_verify_nonce($_POST['medical_blogs_nonce'], 'save_medical_blogs_selection')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['selected_medical_blogs'])) {
        $selected_blogs = array_map('intval', $_POST['selected_medical_blogs']);
        update_post_meta($post_id, '_selected_medical_blogs', $selected_blogs);
    } else {
        delete_post_meta($post_id, '_selected_medical_blogs');
    }
}
add_action('save_post', 'save_medical_blogs_selection');

function medical_blog_card_scripts() {
    wp_enqueue_script('medical-blog-card-js', get_template_directory_uri() . '/js/medical-blog-cards.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'medical_blog_card_scripts');
?>