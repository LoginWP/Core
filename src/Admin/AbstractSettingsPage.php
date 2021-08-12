<?php

namespace LoginWP\Core\Admin;

abstract class AbstractSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_core_menu'));

        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('admin_menu', [$this, 'register_settings_page']);

        add_filter('loginwp_header_menu_tabs', [$this, 'header_menu_tabs']);
    }

    public function register_core_menu()
    {
        add_menu_page(
            __('LoginWP Redirections', 'peters-login-redirect'),
            __('LoginWP', 'peters-login-redirect'),
            'manage_options',
            PTR_LOGINWP_ADMIN_PAGE_SLUG,
            '',
            'dashicons-shield',
            '80.0015'
        );

        add_filter('admin_body_class', [$this, 'add_admin_body_class']);
    }

    public function header_menu_tabs($tabs)
    {
        return $tabs;
    }

    public function add_admin_body_class($classes)
    {
        $current_screen = get_current_screen();

        if (empty ($current_screen)) return;

        if (false !== strpos($current_screen->id, 'loginwp')) {
            // Leave space on both sides so other plugins do not conflict.
            $classes .= ' loginwp-admin ';
        }

        return $classes;
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

    public function settings_page_header()
    {
        $logo_url = PTR_LOGINWP_ASSETS_URL . 'images/loginwp.png';
        ?>
        <div class="loginwp-admin-banner">
            <div class="loginwp-admin-banner__logo">
                <img src="<?= $logo_url ?>" alt="">
            </div>
            <div class="loginwp-admin-banner__helplinks">
                <a rel="noopener" href="https://loginwp.com/docs/" target="_blank">
                    <span class="dashicons dashicons-book"></span> <?= __('Documentation', 'mailoptin'); ?>
                </a>
                <?php if (defined('LOGINWP_DETACH_LIBSODIUM')) : ?>
                    <a rel="noopener" href="https://loginwp.com/submit-ticket/" target="_blank">
                        <span class="dashicons dashicons-admin-users"></span> <?= __('Request Support', 'mailoptin'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
            <?php $this->settings_page_header_menus(); ?>
        </div>
        <?php
    }

    public function settings_page_header_menus()
    {
        $menus = apply_filters('loginwp_header_menu_tabs', []);

        var_dump($menus);

        ?>
        <div class="loginwp-header-menus">
            <nav class="loginwp-nav-tab-wrapper nav-tab-wrapper">
                <a href="#" class="loginwp-nav-tab nav-tab loginwp-nav-active">General</a>
                <a href="#" class="loginwp-nav-tab nav-tab">Integration</a>
            </nav>
        </div>
        <?php
    }

    abstract function register_settings_page();
}