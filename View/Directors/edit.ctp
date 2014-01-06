
<div class="directors editform">
	<?php echo $this->Form->create('Director'); ?>
	<fieldset>
		<legend>
			<?php echo __('Edit Director'); ?>
		</legend>
		<?php
			echo $this->Form->hidden('id');

			echo $this->Form->input('name', array(
				'label' => 'Full IMDb Name',
				'style' => 'width: 300px',
			));
			echo $this->Form->input('last_name', array('style' => 'width: 250px'));
			echo $this->Form->input('first_name', array('style' => 'width: 200px'));
			echo $this->Form->input('imdb_id', array(
				'type' => 'text',
				'label' => 'IMDb Code',
				'style' => 'width: 100px',
			));
		?>
	</fieldset>
	<?php echo $this->Form->end(__('Edit')); ?>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php
			echo $this->Form->postLink(__('Delete'),
				array('action' => 'delete', $this->Form->value('Director.id')),
				null, __('Are you sure you want to delete # %s?', $this->Form->value('Director.id')
			));
		?></li>
		<li><?php
			echo $this->Html->link(__('List Directors'), array('action' => 'index'));
		?></li>
	</ul>
</div>
