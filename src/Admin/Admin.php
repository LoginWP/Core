<?php

namespace LoginWP\Core\Admin;

class Admin
{
    public function __construct()
    {
        RedirectionsPage::get_instance();

        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));

        add_filter('admin_footer_text', [$this, 'custom_admin_footer']);

        add_action('admin_menu', array($this, 'register_core_menu'));
    }

    public function register_core_menu()
    {
        add_menu_page(
            __('LoginWP Redirections', 'peters-login-redirect'),
            __('LoginWP', 'peters-login-redirect'),
            'manage_options',
            PTR_LOGINWP_ADMIN_PAGE_SLUG,
            '',
            $this->getMenuIcon(),
            '80.0015'
        );

        do_action('loginwp_register_menu_page');

        do_action('loginwp_admin_hooks');

        add_filter('admin_body_class', [$this, 'add_admin_body_class']);
    }

    public function admin_assets()
    {
        if (isset(get_current_screen()->base) && strpos(get_current_screen()->base, 'loginwp') !== false) {
            wp_enqueue_style('ptr-loginwp-admin', PTR_LOGINWP_ASSETS_URL . 'css/admin.css', [], PTR_LOGINWP_VERSION_NUMBER);
            wp_enqueue_script('ptr-loginwp-admin', PTR_LOGINWP_ASSETS_URL . 'js/admin.js', ['jquery', 'wp-util'], PTR_LOGINWP_VERSION_NUMBER, true);

            wp_localize_script('ptr-loginwp-admin', 'loginwp_globals', [
                'confirm_delete' => esc_html__('Are you sure?', 'peters-login-redirect')
            ]);
        }
    }

    public function add_admin_body_class($classes)
    {
        $current_screen = get_current_screen();

        if (empty ($current_screen)) return $classes;

        if (false !== strpos($current_screen->id, 'loginwp')) {
            // Leave space on both sides so other plugins do not conflict.
            $classes .= ' loginwp-admin ';
        }

        return $classes;
    }

    public function custom_admin_footer($text)
    {
        if (strpos(loginwpGET_var('page'), 'loginwp') !== false) {
            $text = sprintf(
                __('Thank you for using LoginWP. Please rate the plugin %1$s on %2$sWordPress.org%3$s to help us spread the word.', 'block-visibility'),
                '<a href="https://wordpress.org/support/plugin/peters-login-redirect/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer">★★★★★</a>',
                '<a href="https://wordpress.org/support/plugin/peters-login-redirect/reviews/?filter=5#new-post" target="_blank" rel="noopener">',
                '</a>'
            );
        }

        return $text;
    }

    private function getMenuIcon()
    {
        return 'data:image/svg+xml;base64,' . base64_encode('<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" viewBox="0 0 58.78 58.78" xmlns:v="https://vecta.io/nano"><path d="M55.87 25.04c.81.76 1.31 1.84 1.31 3.03v25.57a4.19 4.19 0 0 1-4.17 4.17H32.35c-1-17.92 4.14-19.85 4.14-24.27 0-3.92-3.18-7.1-7.1-7.1s-7.1 3.18-7.1 7.1c0 4.42 5.14 6.34 4.14 24.27H5.77a4.19 4.19 0 0 1-4.17-4.17V28.07a4.14 4.14 0 0 1 1.42-3.12L26.69 2.1c.69-.69 1.65-1.12 2.71-1.12 1.08 0 1.94.4 2.71 1.12 3.79 3.55 7.52 7.26 11.26 10.87l12.52 12.08z" fill="#a6aaad"/></svg>');
    }

    /**
     * @return self
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}