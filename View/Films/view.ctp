<h2><?php
	echo __(
		'%s (%s) [#%s]',
		h($film['Film']['title']),
		h($film['Film']['year']),
		h($film['Film']['id'])
	);
?></h2>
<p>
	Last edited: <?php echo h(
		$this->Time->format('Y-m-d h:i', $film['Film']['modified'])
  	); ?>
</p>


<?php if ( !empty($film['Viewing']) ): ?>
  	<h2><?php echo __('Viewings'); ?></h2>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th style="width: 100px"><?php echo __('Date'); ?></th>
		<th style="width: 100px"><?php echo __('Type'); ?></th>
		<th style="width: 400px"><?php echo __('Notes'); ?></th>
		<th style="width: 100px" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ( $film['Viewing'] as $viewing ): ?>
		<tr>
			<td><?php echo $this->Time->format('Y-m-d', $viewing['date']); ?></td>
			<td><?php echo $viewing['type']; ?></td>
			<td><?php echo h($viewing['notes']); ?></td>
			<td class="actions">
				<?php
					echo $this->Form->postLink(__('Delete'), array(
						'controller' => 'viewings', 'action' => 'delete', $viewing['id']),
						null, __('Are you sure you want to delete # %s?', $viewing['id'])
					);
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

<p>&nbsp;</p>

<?php if (!empty($film['Director'])): ?>
	<h2><?php echo __('Related Directors'); ?></h2>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th style="width: 50px"><?php echo __('Id'); ?></th>
		<th style="width: 200px"><?php echo __('IMDb Name'); ?></th>
		<th style="width: 150px"><?php echo __('Last Name'); ?></th>
		<th style="width: 150px"><?php echo __('First Name'); ?></th>
		<th style="width: 125px" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($film['Director'] as $director): ?>
		<tr>
			<td><?php echo $director['id']; ?></td>
			<td><?php echo h($director['name']); ?></td>
			<td><?php echo h($director['last_name']); ?></td>
			<td><?php echo h($director['first_name']); ?></td>
			
			<td class="actions"><?php
				echo $this->Html->link(__('View'), array(
					'controller' => 'directors',
					'action' => 'view',
					$director['id'],
				));
				echo $this->Html->link(__('Edit'), array(
					'controller' => 'directors',
					'action' => 'edit',
					$director['id'],
				));
			?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

<p>&nbsp;</p>

<div class="actions">
	<h2><?php echo __('Actions'); ?></h2>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Film'), array('action' => 'edit', $film['Film']['id'])); ?> </li>
		<li><?php
			echo $this->Form->postLink(__('Delete Film'), array(
				'action' => 'delete', $film['Film']['id']),
				null, __('Are you sure you want to delete # %s?', $film['Film']['id'])
			); 
		?> </li>
		<li><?php echo $this->Html->link(__('List Films'), array('action' => 'index')); ?> </li>
	</ul>
</div>

<p>&nbsp;</p>

<?php
	if (	!empty($film['Film']['imdb_status'])
		&& 	(	$film['Film']['imdb_status'] == Configure::read('imdbStatuses.partialdata')
			||	$film['Film']['imdb_status'] == Configure::read('imdbStatuses.fulldata')
		)
	):
?>
<div class="imdb">
	<h2><?php echo __('IMDb Data'); ?></h2>

	<div class="info">
		<div class="label"><?php echo __('Title/Year'); ?></div>
		<div class="value"><?php
			echo h(__('%s (%s)', $film['Film']['imdb_title'], $film['Film']['imdb_year']));
		?></div>
	</div>
	<div class="info">
		<div class="label"><?php echo __('Genres'); ?></div>
		<div class="value"><?php echo str_replace(";", " &mdash; ", h($film['Film']['imdb_genres'])); ?></div>
	</div>
	<div class="info">
		<div class="label"><?php echo __('Countries'); ?></div>
		<div class="value"><?php echo str_replace(";", " &mdash; ", h( $film['Film']['imdb_country'])); ?></div>
	</div>
	<div class="info">
		<div class="label"><?php echo __('Languages'); ?></div>
		<div class="value"><?php echo str_replace(";", " &mdash; ", h($film['Film']['imdb_langs'])); ?></div>
	</div>
	<div class="info">
		<div class="label"><?php echo __('Colors'); ?></div>
		<div class="value"><?php echo str_replace(";", " &mdash; ", h($film['Film']['imdb_color'])); ?></div>
	</div>
	
	<?php if (!empty($film['Film']['imdb_trivia'])): ?>
	<div class="trivia">
		<h3><?php echo __('Trivia'); ?></h3>
		<?php 
			echo str_replace("\n", "<br />", preg_replace(
				'/href="([^"]+?)"/is',
				'href="http://www.imdb.com\\1" target="_blank"',
				$film['Film']['imdb_trivia']
			));
		?>
	</div>
	<?php endif; ?>

</div>
<?php endif; ?>
