<?php
$pro_conditions = [
    'LearnDash' => [
        esc_html__('Course enrolled in', 'peters-login-redirect'),
        esc_html__('Group user belongs to', 'peters-login-redirect')
    ],
    'WooCommerce' => [
        esc_html__('Product customer bought', 'peters-login-redirect'),
        esc_html__('Product category customer bought from', 'peters-login-redirect')
    ]
];
?>

<div class="ptr-loginwp-pro-conditions-wrap">
    <?php foreach ($pro_conditions as $label => $condition): ?>
        <div class="ptr-loginwp-pro-condition">
            <strong><?= $label ?>:</strong> <?= implode(', ', $condition) ?>.
        </div>
    <?php endforeach; ?>
    <div>
        <a href="https://loginwp.com/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=pro_conditions_metabox" target="__blank" class="button-primary">
            <?php esc_html_e('Get LoginWP Pro →', 'peters-login-redirect') ?>
        </a>
    </div>
</div>