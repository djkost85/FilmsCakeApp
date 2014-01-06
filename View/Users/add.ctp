<h1>
    Create User
</h1>

<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
        <?php
            echo $this->Form->input('username');
            echo $this->Form->input('password', array(
                'allowEmpty' => true
            ),
            echo $this->Form->input('role', array(
                'options' => array(
                    'editor' => 'Editor',
                    'viewer' => 'Viewer',
                    'guest' => 'Guest'
                )
            ));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>