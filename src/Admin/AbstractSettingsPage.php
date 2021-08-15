<?php

namespace LoginWP\Core\Admin;

use LoginWP\Libsodium\Redirections\Integrations;

abstract class AbstractSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_core_menu'));

        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));

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

        $this->register_menu_page();

        do_action('loginwp_admin_hooks');

        add_filter('admin_body_class', [$this, 'add_admin_body_class']);
    }

    abstract function register_menu_page();

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

    public function settings_page_header($active_menu)
    {
        $logo_url = PTR_LOGINWP_ASSETS_URL . 'images/loginwp.png';
        ?>
        <div class="loginwp-admin-banner<?= defined('LOGINWP_DETACH_LIBSODIUM') ? ' loginwp-pro' : '' ?>">
            <div class="loginwp-admin-banner__logo">
                <img src="<?= $logo_url ?>" alt="">
            </div>
            <div class="loginwp-admin-banner__helplinks">
                <?php if (defined('LOGINWP_DETACH_LIBSODIUM')) : ?>
                    <span><a rel="noopener" href="https://loginwp.com/submit-ticket/" target="_blank">
                        <span class="dashicons dashicons-admin-users"></span> <?= __('Request Support', 'peters-login-redirect'); ?>
                    </a></span>
                <?php else : ?>
                    <span><a class="lwp-active" rel="noopener" href="https://loginwp.com/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=loginwp_header_topright_menu" target="_blank">
                        <span class="dashicons dashicons-info"></span> <?= __('Pro Upgrade', 'peters-login-redirect'); ?>
                    </a></span>
                <?php endif; ?>
                <span><a rel="noopener" href="https://wordpress.org/support/plugin/peters-login-redirect/reviews/?filter=5#new-post" target="_blank">
                    <span class="dashicons dashicons-star-filled"></span> <?= __('Review', 'peters-login-redirect'); ?>
                </a></span>
                <span><a rel="noopener" href="https://loginwp.com/docs/" target="_blank">
                    <span class="dashicons dashicons-book"></span> <?= __('Documentation', 'peters-login-redirect'); ?>
                </a></span>
            </div>
            <div class="clear"></div>
            <?php $this->settings_page_header_menus($active_menu); ?>
        </div>
        <?php
    }

    public function settings_page_header_menus($active_menu)
    {
        $menus = apply_filters('loginwp_header_menu_tabs', []);

        if (count($menus) < 2) return;
        ?>
        <div class="loginwp-header-menus">
            <nav class="loginwp-nav-tab-wrapper nav-tab-wrapper">
                <?php foreach ($menus as $id => $menu) : ?>
                    <a href="<?php echo add_query_arg('tab', $id, PTR_LOGINWP_REDIRECTIONS_PAGE_URL); ?>" class="loginwp-nav-tab nav-tab<?= $id == $active_menu ? ' loginwp-nav-active' : '' ?>">
                        <?php echo $menu ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
        <?php
    }

    public function admin_page_callback()
    {
        $active_menu = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'rules';

        $this->settings_page_header($active_menu);

        do_action('loginwp_admin_settings_page_' . $active_menu);
    }

    public function sidebar_args()
    {
        $sidebar_args = [
            [
                'section_title' => esc_html__('Upgrade to Premium', 'peters-login-redirect'),
                'content'       => $this->pro_upsell(),
            ],
            [
                'section_title' => esc_html__('Need Support?', 'peters-login-redirect'),
                'content'       => $this->sidebar_support_docs(),
            ]
        ];

        if(defined('LOGINWP_DETACH_LIBSODIUM')) {
            unset($sidebar_args[0]);
        }

        return $sidebar_args;
    }

    public function pro_upsell()
    {
        $integrations = [
            'WooCommerce',
            'Gravity Forms',
            'WPForms',
            'LearnDash',
            'MemberPress',
            'Restrict Content Pro',
            'Lifter LMS',
            'Easy Digital Downloads',
            'Ultimate Member',
            'WP User Manager',
            'User Registration (WPEverest)',
            'Theme My Login'
        ];

        $content = '<p>';
        $content .= esc_html__('Enhance the power of LoginWP with the Premium version featuring integrations with many plugins.', 'peters-login-redirect');
        $content .= '</p>';

        $content .= '<ul>';

        foreach ($integrations as $integration) :
            $content .= sprintf('<li>%s</li>', $integration);
        endforeach;

        $content .= '</ul>';

        $url     = 'https://loginwp.com/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=sidebar_upsell';
        $content .= '<a href="' . $url . '" target="__blank" class="button-primary">' . esc_html__('Get LoginWP Premium →', 'peters-login-redirect') . '</a>';

        return $content;
    }

    public function sidebar_support_docs()
    {
        $link_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="linkIcon"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg>';

        $content = '<p>';

        $support_url = 'https://wordpress.org/support/plugin/peters-login-redirect/';

        if (defined('LOGINWP_DETACH_LIBSODIUM')) {
            $support_url = 'https://loginwp.com/submit-ticket/';
        }

        $content .= sprintf(
            esc_html__('Whether you need help or have a new feature request, let us know. %sRequest Support%s', 'peters-login-redirect'),
            '<a class="loginwp-link" href="' . $support_url . '" target="_blank">', $link_icon . '</a>'
        );

        $content .= '</p>';

        $content .= '<p>';
        $content .= sprintf(
            esc_html__('Detailed documentation is also available on the plugin website. %sView Knowledge Base%s', 'peters-login-redirect'),
            '<a class="loginwp-link" href="https://loginwp.com/docs/" target="_blank">', $link_icon . '</a>'
        );

        $content .= '</p>';

        $content .= '<p>';
        $content .= sprintf(
            esc_html__('If you are enjoying LoginWP and find it useful, please consider leaving a ★★★★★ review on WordPress.org. %sLeave a Review%s', 'peters-login-redirect'),
            '<a class="loginwp-link" href="https://wordpress.org/support/plugin/peters-login-redirect/reviews/?filter=5#new-post" target="_blank">', $link_icon . '</a>'
        );
        $content .= '</p>';

        return $content;
    }
}