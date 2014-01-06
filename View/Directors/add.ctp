<div class="directors form">
<?php echo $this->Form->create('Director'); ?>
	<fieldset>
		<legend><?php echo __('Form Director'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('last_name');
		echo $this->Form->input('first_name');
		echo $this->Form->input('Film');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Director.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Director.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Directors'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Films'), array('controller' => 'films', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Film'), array('controller' => 'films', 'action' => 'add')); ?> </li>
	</ul>
</div>
