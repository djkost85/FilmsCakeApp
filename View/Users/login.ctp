<?php
/**
 *  View for User::login
 *
 *  Since the login form is already displayed in the header,
 *  this shows nothing but the authorization flash message.
 */
?>

<?php echo $this->Session->flash('auth'); ?>

<?php /*
    // old CakePHP login form
    <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <legend><?php echo __('Please enter your username and password'); ?></legend>
            <?php echo $this->Form->input('username');
            echo $this->Form->input('password');
        ?>
        </fieldset>
    <?php echo $this->Form->end(__('Login')); ?>
*/ ?>