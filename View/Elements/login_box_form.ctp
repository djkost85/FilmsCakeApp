<?php
/**
 *  Element: loginBoxForm
 *
 *  Box containing a login form for inclusion in the header.
 */
?>
<div class="login_box form">
    <p>
        <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <?php
                echo $this->Form->input('username');
                echo $this->Form->input('password');
            ?>
        </fieldset>
        <?php echo $this->Form->end(__('Login')); ?>
    </p>
</div>
