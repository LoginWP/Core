<?php

namespace LoginWP\Core\Admin;

abstract class AbstractSettingsPage
{
    public function init_menu()
    {
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
            'dashicons-shield',
            '80.0015'
        );

        add_filter('admin_body_class', [$this, 'add_admin_body_class']);
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
        </div>
        <?php
    }
}