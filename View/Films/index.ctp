<?php
	
	/*
		add javascript for datepicker
		make datepicker appear to the right of the input field
	*/
	$this->Html->scriptBlock( "
    	$(document).ready(function(){
    		// set truncated cells
		    $('table td.trunc').dotdotdot({
        		height 		: 20,
        		callback 	: function(isTruncated, orgContent) {
        			if (isTruncated) {
        				$(this).prop('title', orgContent.text());
        				$(this).addClass('is_truncated');
        				$(this).qtip({
			                position: {
			                    my: 'top center',
			                    at: 'bottom center',
			                    viewport: $(window),
			                    adjust: { x: 50, y: 1 }
			                },
			                show: {
			                    delay: 0
			                },
			                style: {
			                    classes: 'qtip-coen'
			                }
			    		});
        			}
        		},
    		});

			// set qtip bootstrap tooltip
    		$('.tooltip_qtip').qtip({
                position: {
                    my: 'top center',
                    at: 'bottom center',
                    viewport: $(window),
                    adjust: { x: 0, y: 5 }
                },
                show: {
                    delay: 0
                },
                style: {
                    classes: 'qtip-coen',
                    width: 600,
                    tip: {
                        corner: true,
                        offset: 200
                    }
                }
    		});

			// clear the search form
			$('#search-clear').click(function () {
				$('.searchform input[type=text], .searchform input[type=select]').val('');
			});
	
			// make field row clicks open the view action on table cells
			$('table td.cview').click(function() {
				document.location.href = '"
				.	$this->Html->url(array('action' => 'view', '__ID__'))
				. 	"'.replace('__ID__', $(this).parent().find('.idcontainer').text());
			});
			
    	});",
		array('inline' => false)
	);
?>

<?php
	//debug($films[0]['ActorRole']);
?>

<div class="films index">
	<h2><?php echo __('Films'); ?></h2>


	<div class="films searchform">
		<?php echo $this->Form->create('Film', array('action' => 'search')); ?>

		<fieldset>
	 		<legend><?php __('Search'); ?></legend>
			<?php
				echo $this->Form->input('Search.title', array(
					'after' => __('wildcard: *',true),
				));
				echo $this->Form->input('Search.year', array(
					'type' => 'text',
					'style' => 'width: 50px',
					//'after' => __('wildcard: *',true),
				));
				//echo $this->Form->input('Search.director', array('after' => __('wildcard is *',true)));

				echo $this->Form->submit('Search');

				echo $this->Html->link(__('Clear Fields', true), 'javascript:void(0)', array(
					'id' => 'search-clear',
					'class' => 'searchlink',
				));
			?>
		</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>

	<table class="truncate" cellpadding="0" cellspacing="1">
		<tbody>
	<tr>
			<th style="width: 60px"><?php
				echo $this->Paginator->sort('id', null, array('direction' => 'desc'));
			?></th>
			<th><?php
				echo $this->Paginator->sort('title');
			?></th>
			<th style="width: 60px"><?php
				echo $this->Paginator->sort('year', null, array('direction' => 'desc'));
			?></th>
			<th style="width: 150px"><?php echo __('direction'); ?></th>
			<th style="width: 60px"><?php
				echo $this->Paginator->sort('duration', 'dur.', array('direction' => 'desc'));
			?></th>
			<th style="width: 60px"><?php
				echo $this->Paginator->sort('viewing_count', 'Seen', array('direction' => 'desc'));
			?></th>
			<th style="width: 110px"><?php
				echo $this->Paginator->sort('seen_last', null, array('direction' => 'desc'));
			?></th>
			<th style="width: 100px"><?php
				echo $this->Paginator->sort('best_quality', 'Quality');
			?></th>
			<th style="width: 60px"><?php
				echo $this->Paginator->sort('grade', 'Rated', array('direction' => 'desc'));
			?></th>
			<th style="width: 60px"><?php
				echo $this->Paginator->sort('imdb_grade', 'IMDb', array('direction' => 'desc'));
			?></th>
			<th style="width: 70px"><?php echo __('notes'); ?></th>
			<th style="width: 70px"><?php echo __('IMDb'); ?></th>
			<th style="width: 70px"><?php echo __('link'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
<?php
		$count = 0;
		foreach ($films as $film) {
			// create a concatenated list of directors
			$dirList = "";
			foreach($film['Director'] as $dir) {
				$dirList .= (($dirList) ? "; " : "") . $dir["last_name"]
						 . 	(($dir["first_name"]) ? ", " . $dir["first_name"] : "");
			}

			$count++;
?>
	<tr>
		<td class="id r"><?php
			echo h($film['Film']['id']);
			echo $this->Html->tag('span', $film['Film']['id'], array('class' => 'hidden idcontainer'));
		?>&nbsp;</td>
		<?php /* <td id="title_cell_<?php echo $count; ?>" class="trunc" style="width: 400px"><div><?php echo h($film['Film']['title']); ?></div>&nbsp;</td> */ ?>
		<td class="trunc cview" style="width: 400px"><?php echo h($film['Film']['title']); ?>&nbsp;</td>
		<td class="r cview"><?php echo h($film['Film']['year']); ?>&nbsp;</td>
		<td class="trunc cview" style="width: 150px"><?php echo h( $dirList ); ?>&nbsp;</td>
		<td class="r cview"><?php echo h($film['Film']['duration']); ?>&nbsp;</td>
		<td class="r cview"><?php echo $film['Film']['viewing_count']; ?>&nbsp;</td>
		<td class="cview"><?php
			if (!empty($film['Film']['seen_last'])) {
				echo CakeTime::format('Y-m-d', $film['Film']['seen_last']);
			}
		?>&nbsp;</td>
		<td class="c xs cview"><?php echo $viewingTypes[$film['Film']['best_quality']]; ?>&nbsp;</td>
		<td class="r cview"><?php echo $film['Film']['grade']; ?>&nbsp;</td>
		<td class="r cview"><?php echo $film['Film']['imdb_grade']; ?>&nbsp;</td>
		<td class="c">
			<?php if (!empty($film['Film']['notes'])): ?>
			<span class="tooltip tooltip_qtip" title="<?php
				echo str_replace("\"", "'", $film['Film']['notes']); ?>">notes</span>
			<?php else: ?>
					&nbsp;
			<?php endif; ?>
		</td>
		<td class="c">
			<?php if (!empty($film['Film']['imdb'])): ?>
			<span class="tooltip tooltip_qtip" title="<?php
				// actor list
				$actors = '';
				$count = 0;
				foreach ($film['ActorRole'] as $act) {
					$actors .= '<div class="actor">'
							.	'<div class="name">' . h($act['Actor']['name']) . '</div>'
							.	'<div class="role">' . $act['role'] . '</div>'
							.	'</div>';
					$count++;
					if ($count > 10) { break; }
				}

				echo str_replace('"', "'",
					'<div class="tooltip_info">'
					.	'<div class="label">Title</div><div class="value">' . h($film['Film']['imdb_title'])
					. 	' (' . h($film['Film']['imdb_year']) .')</div>'
					.	'<div class="label">Genre</div><div class="value">' . h($film['Film']['imdb_genres']) . '</div>'
					.	'<div class="label">Country</div><div class="value">' . h($film['Film']['imdb_country']) . '</div>'
					.	'<div class="label">Language</div><div class="value">' . h($film['Film']['imdb_langs']) . '</div>'
					.	'<div class="label">Actors</div>' . $actors
					.	'</div>'
				); ?>">info</span></td>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
		</td>
		<td class="c">
			<?php if (!empty($film['Film']['imdb'])): ?>
				<?php
					echo $this->Html->link(
						'IMDb',
	    				'http://www.imdb.com/title/tt' . $film['Film']['imdb'] . '/',
	    				array('target' => '_blank')
					);
				?>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
		</td>
		<td class="actions"><?php
			echo $this->Html->link(__('View'), array('action' => 'view', $film['Film']['id']));
			echo $this->Html->link(__('Edit'), array('action' => 'edit', $film['Film']['id']));
		?></td>
	</tr>
<?php
		}
?>
		</tbody>
	</table>
	<p>
	<?php
		echo $this->Paginator->counter(array(
			'format' => __(
				'Page <b>{:page}</b> of <b>{:pages}</b>, showing <b>{:current}</b> records out of'
				. ' <b>{:count}</b> total'
			)
		));
	?>
	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Film'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Directors'), array('controller' => 'directors', 'action' => 'index')); ?> </li>
	</ul>
</div>
