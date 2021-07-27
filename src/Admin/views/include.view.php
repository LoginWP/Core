<?php

use LoginWP\Core\Helpers;

$ruleData = [];

if (isset($_GET['id'])) {
    $ruleData = Helpers::get_rule_by_id(absint($_GET['id']));
}

add_action('add_meta_boxes', function () use ($ruleData) {
    add_meta_box(
        'ptr-loginwp-redirection-rule-condition',
        esc_html__('Rule Condition', 'peters-login-redirect'),
        function () use ($ruleData) {
            require dirname(__FILE__) . '/view.condition-rule.php';
        },
        'ptrloginwpredirection'
    );
});

add_action('add_meta_boxes', function () use ($ruleData) {
    add_meta_box(
        'ptr-loginwp-redirection-rule-urls',
        esc_html__('Redirect URLs', 'peters-login-redirect'),
        function () use ($ruleData) {
            require dirname(__FILE__) . '/view.redirect-urls.php';
        },
        'ptrloginwpredirection'
    );
});

add_action('add_meta_boxes', function () {
    add_meta_box(
        'submitdiv',
        __('Publish', 'peters-login-redirect'),
        function () {
            require dirname(__FILE__) . '/include.view-sidebar.php';
        },
        'ptrloginwpredirection',
        'sidebar'
    );
});

do_action('add_meta_boxes', 'ptrloginwpredirection', '');

?>
<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">

        <div id="postbox-container-1" class="postbox-container">
            <?php do_meta_boxes('ptrloginwpredirection', 'sidebar', ''); ?>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <?php do_meta_boxes('ptrloginwpredirection', 'advanced', ''); ?>
        </div>
    </div>
    <br class="clear">
</div>