<?php

// Add the "Registration Date" column to the Users table
function custom_add_user_registration_column($columns) {
    $columns['registration_date'] = 'Registration Date';
    return $columns;
}
add_filter('manage_users_columns', 'custom_add_user_registration_column');

// Populate the column with data
function custom_show_user_registration_column($value, $column_name, $user_id) {
    if ($column_name == 'registration_date') {
        $user = get_userdata($user_id);
        return date("Y-m-d H:i:s", strtotime($user->user_registered));
    }
    return $value;
}
add_filter('manage_users_custom_column', 'custom_show_user_registration_column', 10, 3);

// Make the column sortable
function custom_make_user_registration_column_sortable($columns) {
    $columns['registration_date'] = 'user_registered';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'custom_make_user_registration_column_sortable');

// Modify the query to apply sorting
function custom_orderby_user_registration_date($query) {
    if (!is_admin() || $query->get('orderby') !== 'user_registered') {
        return;
    }
    
    $query->set('orderby', 'user_registered'); // Sort by registration date
    $query->set('order', 'DESC'); // Default to newest first
}
add_action('pre_get_users', 'custom_orderby_user_registration_date');
