<?php

// Typically this function is used in templates, similarly to the wp_register function
// It returns a link to the administration panel or the one that was custom defined
// If no user is logged in, it returns the "Register" link
// You can specify tags to go around the returned link (or wrap it with no tags); by default this is a list item
// You can also specify whether to print out the link or just return it
use LoginWP\Core\Helpers;

function rul_register($before = '<li>', $after = '</li>', $give_echo = true)
{
    global $current_user;

    if ( ! is_user_logged_in()) {
        if (get_option('users_can_register')) {
            $link = $before . '<a href="' . wp_registration_url() . '">' . __('Register', 'peters-login-redirect') . '</a>' . $after;
        } else {
            $link = '';
        }
    } else {
        $link = $before . '<a href="' . Helpers::login_redirect_logic_callback('', '', $current_user) . '">' . __('Site Admin', 'peters-login-redirect') . '</a>' . $after;
    }

    if ($give_echo) {
        echo $link;
    } else {
        return $link;
    }
}

// doing this so we can pass current user logging out since it is no longer active after logout
// and also because it is a pluggable function
if ( ! function_exists('wp_logout')) :
    /**
     * Log the current user out.
     *
     * @since 2.5.0
     */
    function wp_logout()
    {
        $current_user = wp_get_current_user();
        wp_destroy_current_session();
        wp_clear_auth_cookie();
        wp_set_current_user(0);

        /**
         * Fires after a user is logged-out.
         *
         * @since 1.5.0
         */
        do_action('wp_logout', $current_user);
    }
endif;

function loginwpPOST_var($key, $default = false, $empty = false, $bucket = false)
{
    $bucket = ! $bucket ? $_POST : $bucket;

    if ($empty) {
        return ! empty($bucket[$key]) ? $bucket[$key] : $default;
    }

    return isset($bucket[$key]) ? $bucket[$key] : $default;
}

function loginwpGET_var($key, $default = false, $empty = false)
{
    $bucket = $_GET;

    if ($empty) {
        return ! empty($bucket[$key]) ? $bucket[$key] : $default;
    }

    return isset($bucket[$key]) ? $bucket[$key] : $default;
}

function loginwp_var($bucket, $key, $default = false, $empty = false)
{
    if ($empty) {
        return ! empty($bucket[$key]) ? $bucket[$key] : $default;
    }

    return isset($bucket[$key]) ? $bucket[$key] : $default;
}

function loginwp_var_obj($bucket, $key, $default = false, $empty = false)
{
    if ($empty) {
        return ! empty($bucket->$key) ? $bucket->$key : $default;
    }

    return isset($bucket->$key) ? $bucket->$key : $default;
}