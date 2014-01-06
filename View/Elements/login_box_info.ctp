<?php
/**
 *  Element: loginBoxInfo
 *
 *  Box containing login information about currently logged in user
 *  for inclusion in header.
 */
?>
<div class="login_box">
    Logged in user: <?php echo AuthComponent::user('username'); ?>
    
    <div class="logout">
        <?php echo $this->Html->link('log out', array('controller'=>'users', 'action'=>'logout')); ?>
    </div>
</div>