<?php
	
	/*
		add javascript for datepicker
		make datepicker appear to the right of the input field
	*/
	$this->Html->scriptBlock("
    	$(document).ready(function(){
    		$( '.datepicker' ).datepicker( {
    			dateFormat: 'yy-mm-dd',
    			beforeShow: function(input, inst) {
        			inst.dpDiv.css( { marginTop: -input.offsetHeight + 'px',"
        .	" marginLeft: (input.offsetWidth + 5) + 'px' } );
    			}
    		} );
    	});",
		array('inline' => false)
	);
?>

<div class="films editform">
<?php echo $this->Form->create('Film'); ?>
	<fieldset>
		<legend>
			<?php echo __('Edit Film #%s', $this->Form->value('Film.id')); ?>
		</legend>
		<div class="fieldset_box">
		<?php
			echo $this->Form->hidden('id');
			echo $this->Form->input('status', array(
				'type' => 'select',
				'options' => $filmStatuses,
				'default' => Configure::read('filmStatuses.seen'),
				'style' => 'width: 110px',
			));
			echo $this->Form->input('title', array('style' => 'width: 350px') );

			// imdb title for display underneath
			echo $this->Html->div('input text',
				$this->Html->tag('label', 'IMDb Title')
				.	$this->Html->tag('input', null, array(
					'type' => 'text',
					'style' => 'width: 350px',
					'readonly' => 'readonly',
					'value' => $film['Film']['imdb_title'],
				))
			);
			/*
			echo '<div class="input text">'
				.	'<label>IMDb Title</label>'
				.	'<input name="IMDbTitle" style="width: 350px" maxlength="250" readonly="readonly" type="text"'
				.	' value="" id="IMDbTitleInput"/>'
				.	'</div>';
			*/
		
			echo $this->Form->input('year', array('style' => 'width: 45px') );
			echo $this->Form->input('duration', array('style' => 'width: 45px') );
			echo $this->Form->input('imdb', array('style' => 'width: 90px') );
			echo $this->Form->input('imdb_status', array(
				'type' => 'select',
				'options' => $imdbStatuses,
				'default' => 0,
				'style' => 'width: 90px',
			));
			echo $this->Form->input('grade', array('style' => 'width: 45px') );
			echo $this->Form->input('notes', array(
				'type' => 'textarea',
				'style' => 'width: 350px',
				'cols' => 60,
				'rows' => 3,
			));

			// imdb fields
			//echo $this->Form->hidden('Director');	// hide, problems with providing only the ids anyway
			//echo $this->Form->hidden('Actor');
			echo $this->Form->hidden('imdb_status');
			echo $this->Form->hidden('imdb_title');
			echo $this->Form->hidden('imdb_year');
			echo $this->Form->hidden('imdb_grade');
			echo $this->Form->hidden('imdb_country');
			echo $this->Form->hidden('imdb_langs');
			echo $this->Form->hidden('imdb_color');
			echo $this->Form->hidden('imdb_genres');
			echo $this->Form->hidden('imdb_trivia');
		?>
		</div>
	</fieldset>

	<fieldset class="container">
		<legend>
			<?php echo __( 'Viewings'); ?>
		</legend>

		<fieldset>
			<legend>
				<?php echo __( 'New Viewing'); ?>
			</legend>

			<div class="fieldset_box">
				<?php
					$count = count( $film['Viewing'] );

					echo $this->Form->input( __('Viewing.%s.date', $count), array(
						'type' => 'text',
						'name' => __('data[Viewing][%s][date]', $count),
						'class' => 'datepicker',
						'style' => 'width: 200px',
	    			) );
					echo $this->Form->input( __('Viewing.%s.type', $count), array(
						'type' => 'select',
						'options' => $viewingTypes,
						'default' => '',
						'style' => 'width: 200px',
					) );
					echo $this->Form->input( __('Viewing.%s.notes', $count), array(
						'type' => 'textarea',
						'style' => 'width: 200px',
						'cols' => 30,
						'rows' => 2,
					) );
				?>
			</div>
		</fieldset>

		<fieldset>
			<legend>
				<?php echo __( 'Stored Viewings'); ?>
			</legend>
		<?php
			if ( !empty($film['Viewing']) ):
			foreach ( $film['Viewing'] as $count => $viewing ):
		?>
		<div class="fieldset_box">
			<?php
				echo $this->Form->hidden( __('Viewing.%s.id', $count) );
				echo $this->Form->input( __('Viewing.%s.date', $count), array(
					'type' => 'text',
    				'name' => __('data[Viewing][%s][date]', $count),
    				'class' => 'datepicker',
    				'value' => (!empty($viewing['date'])) ? $this->Time->format('Y-m-d', $viewing['date']) : null,
    				'style' => 'width: 200px',
	    		) );
				echo $this->Form->input( __('Viewing.%s.type', $count), array(
					'type' => 'select',
					'options' => $viewingTypes,
					'style' => 'width: 200px',
				) );
				echo $this->Form->input( __('Viewing.%s.notes', $count), array(
					'type' => 'textarea',
					'style' => 'width: 200px',
					'cols' => 30,
					'rows' => 2,
				) );
			?>
		</div>
		<?php
			endforeach;
			endif;
		?>
		</fieldset>
	</fieldset>

	<?php echo $this->Form->end( __('Edit Film') ); ?>
</div>



<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php
			echo $this->Html->link(__('Load data from IMDb'), array('action' => 'imdb', $film['Film']['id'], true));
		?></li>
		<li><?php
			echo $this->Html->link(__('View/edit IMDb data'), array('action' => 'imdb', $film['Film']['id'], false));
		?></li>
		<li><?php
			echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Film.id')),
				null, __('Are you sure you want to delete # %s?', $this->Form->value('Film.id'))
			);
		?></li>
		<li><?php
			echo $this->Html->link( __('List Films'), array('action' => 'index') );
		?></li>
	</ul>
</div>
