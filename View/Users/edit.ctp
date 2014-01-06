<h1>
    Edit user &mdash; <?php echo $user['User']['username']; ?>
</h1>

<div class="form user_edit">

    
    <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <legend><?php echo __('Edit User information'); ?></legend>
            <?php
                echo $this->Form->input('username');
                echo $this->Form->input('password');
                echo $this->Form->input('role', array(
                    'options' => array(
                        'editor' => 'Editor',
                        'viewer' => 'Viewer',
                        'guest' => 'Guest'
                    )
                ));
            ?>
        </fieldset>
    <?php echo $this->Form->end(__('Edit User')); ?>

</div>