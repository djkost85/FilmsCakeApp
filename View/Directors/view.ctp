
<h2><?php
	echo __('Director: %s [#%s]', h($director['Director']['name']), h($director['Director']['id']));
?></h2>
<p>
	Last edited: <?php echo h(
		$this->Time->format('Y-m-d h:i', $director['Director']['modified'])
  	); ?>
</p>

<p>&nbsp;</p>

<h2><?php echo __('Films'); ?></h2>
	<?php if (!empty($director['Film'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th style="width: 400px"><?php echo __('Title'); ?></th>
		<th style="width: 60px"><?php echo __('Year'); ?></th>
		<th style="width: 60px"><?php echo __('Rating'); ?></th>
		<th style="width: 110px"><?php echo __('Seen Last'); ?></th>
		<th style="width: 125px" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($director['Film'] as $film): ?>
		<tr>
			<td><?php echo h($film['title']); ?></td>
			<td><?php echo h($film['year']); ?></td>
			<td><?php echo $film['grade']; ?></td>
			<td><?php echo $this->Time->format('Y-m-d', $film['seen_last']); ?></td>
			<td class="actions">
				<?php
					echo $this->Html->link(__('View'), array('controller' => 'films', 'action' => 'view', $film['id']));
					echo $this->Html->link(__('Edit'), array('controller' => 'films', 'action' => 'edit', $film['id']));
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

<p>&nbsp;</p>

<div class="actions">
	<h2><?php echo __('Actions'); ?></h2>
	<ul>
		<li><?php
			echo $this->Html->link(__('Edit Director'), array('action' => 'edit', $director['Director']['id']));
		?></li>
		<li><?php
			echo $this->Form->postLink(__('Delete Director'), array(
				'action' => 'delete', $director['Director']['id']),
				null, __('Are you sure you want to delete # %s?', $director['Director']['id'])
			);
		?></li>
		<li><?php echo $this->Html->link(__('List Directors'), array('action' => 'index')); ?></li>
	</ul>
</div>
