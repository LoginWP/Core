<?php

use LoginWP\Core\Admin\RedirectWPList;

?>
<div class="submitbox" id="submitpost">

    <div id="major-publishing-actions">
        <div id="delete-action">
            <?php if (isset($_GET['action']) && 'edit' == $_GET['action']) : ?>
                <a class="submitdelete deletion loginwp-delete-prompt" href="<?php echo esc_url(RedirectWPList::delete_rule_url(absint($_GET['id']))); ?>">
                    <?= esc_html__('Delete', 'peters-login-redirect') ?>
                </a>
            <?php endif; ?>
        </div>

        <div id="publishing-action">
            <?php wp_nonce_field('loginwp_save_rule', 'rul-loginwp-nonce') ?>
            <input type="submit" name="loginwp_save_rule" class="button button-primary button-large" value="<?= esc_html__('Save Rule', 'peters-login-redirect') ?>">
        </div>
        <div class="clear"></div>
    </div>

</div>