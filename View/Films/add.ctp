<?php
	
	/*
		add javascript for datepicker
		make datepicker appear to the right of the input field
	*/
	$this->Html->scriptBlock( "
    	$(document).ready(function(){
    		$( '.datepicker' ).datepicker( {
    			dateFormat: 'yy-mm-dd',
    			beforeShow: function(input, inst) {
        			inst.dpDiv.css( { marginTop: -input.offsetHeight + 'px',"
        .	" marginLeft: (input.offsetWidth + 5) + 'px' } );
    			}
    		} );
    	});",
		array( 'inline' => false )
	);
?>

<div class="films editform">
<?php echo $this->Form->create('Film'); ?>
	<fieldset>
		<legend>
			<?php echo __('Add New Film'); ?>
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
			echo $this->Form->input('year', array('style' => 'width: 45px') );
			echo $this->Form->input('duration', array('style' => 'width: 45px') );
			echo $this->Form->input('imdb', array('style' => 'width: 90px') );
			echo $this->Form->input('imdb_status', array(
				'type' => 'select',
				'options' => $imdbStatuses,
				'default' => 0,
				'style' => 'width: 90px',
			) );
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
			<?php echo __( 'Viewing'); ?>
		</legend>

		<div class="fieldset_box">
			<?php
				$count = 0;	// only adding one, if more, use $count and loop

				echo $this->Form->input( __('Viewing.%s.date', $count), array(
					'type' => 'text',
					'name' => __('data[Viewing][%s][date]', $count),
					'class' => 'datepicker',
					'style' => 'width: 200px',
    			) );
				echo $this->Form->input( __('Viewing.%s.type', $count), array(
					'type' => 'select',
					'options' => $viewingTypes,
					'default' => 'avi',
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

	<?php echo $this->Form->end( __('Add Film') ); ?>
</div>


<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php
			echo $this->Html->link( __('List Films'), array('action' => 'index') );
		?></li>
	</ul>
</div>
