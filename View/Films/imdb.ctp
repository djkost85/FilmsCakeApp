

<h2><?php
    echo h($film['Film']['title']) . __(' (%s)',$film['Film']['id']);
?></h2>


<?php
    // table with IMDb data retrieved...
    
?>
<div class="films editform">
<?php echo $this->Form->create('Film', array('novalidate' => true)); ?>
    <fieldset>
        <legend>
            <?php echo __( 'Edit IMDb data for film #%s', $this->Form->value('Film.id') ); ?>
        </legend>
        <div class="fieldset_box">
        <?php
            echo $this->Form->hidden('id');
            echo $this->Form->input('imdb', array(
                'label' => 'IMDb Code',
                'style' => 'width: 90px',
            ));
            echo $this->Form->input('imdb_status', array(
                'label' => 'IMDb Status',
                'type' => 'select',
                'options' => $imdbStatuses,
                'default' => 0,
                'style' => 'width: 90px',
            ));

            echo $this->Form->input('imdb_title', array(
                'label' => 'Title',
                'style' => 'width: 400px',
            ));
            echo $this->Form->input('imdb_year', array(
                'label' => 'Year',
                'style' => 'width: 45px',
            ));

            echo $this->Form->input('imdb_grade', array(
                'label' => 'Grade',
                'style' => 'width: 45px',
            ));
            echo $this->Form->input('imdb_genres', array(
                'label' => 'Genres',
                'style' => 'width: 400px',
            ));
            echo $this->Form->input('imdb_country', array(
                'label' => 'Countries',
                'style' => 'width: 400px',
            ));
            echo $this->Form->input('imdb_langs', array(
                'label' => 'Languages',
                'style' => 'width: 400px',
            ));
            echo $this->Form->input('imdb_color', array(
                'label' => 'Colors',
                'style' => 'width: 200px',
            ));

            echo $this->Form->input('imdb_trivia', array(
                'label' => 'Trivia',
                'type' => 'textarea',
                'style' => 'width: 400px',
                'cols' => 60,
                'rows' => 10,
            ));
        ?>
        </div>
    </fieldset>

    <fieldset class="container">
        <legend>
            <?php echo __( 'Directors'); ?>
        </legend>

        <fieldset>
            <legend>
                <?php echo __( 'New Director'); ?>
            </legend>

            <div class="fieldset_box">
                <?php
                    $count = count( $film['Director'] );

                    echo $this->Form->input( __('Director.%s.name', $count), array(
                        'type' => 'text',
                        'name' => __('data[Director][%s][name]', $count),
                        'style' => 'width: 200px',
                    ));
                    echo $this->Form->input( __('Director.%s.imdb_id', $count), array(
                        'type' => 'text',
                        'name' => __('data[Director][%s][imdb_id]', $count),
                        'style' => 'width: 100px',
                    ));
                    echo $this->Form->input( __('Director.%s.last_name', $count), array(
                        'type' => 'text',
                        'name' => __('data[Director][%s][last_name]', $count),
                        'style' => 'width: 150px',
                    ));
                    echo $this->Form->input( __('Director.%s.first_name', $count), array(
                        'type' => 'text',
                        'name' => __('data[Director][%s][first_name]', $count),
                        'style' => 'width: 150px',
                    ));
                ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo __( 'IMDb Directors'); ?>
            </legend>
        <?php
            if ( !empty($film['Director']) ):
            foreach ( $film['Director'] as $count => $director ):
        ?>
        <div class="fieldset_box">
            <?php
                echo $this->Form->hidden( __('Director.%s.id', $count) );
                echo $this->Form->input( __('Director.%s.name', $count), array(
                    'type' => 'text',
                    'name' => __('data[Director][%s][name]', $count),
                    'style' => 'width: 200px',
                ));
                echo $this->Form->input( __('Director.%s.imdb_id', $count), array(
                    'type' => 'text',
                    'name' => __('data[Director][%s][imdb_id]', $count),
                    'style' => 'width: 100px',
                ));
                echo $this->Form->input( __('Director.%s.last_name', $count), array(
                    'type' => 'text',
                    'name' => __('data[Director][%s][last_name]', $count),
                    'style' => 'width: 150px',
                ));
                echo $this->Form->input( __('Director.%s.first_name', $count), array(
                    'type' => 'text',
                    'name' => __('data[Director][%s][first_name]', $count),
                    'style' => 'width: 150px',
                ));
            ?>
        </div>
        <?php
            endforeach;
            endif;
        ?>
        </fieldset>
    </fieldset>

    <?php echo $this->Form->end( __('Edit IMDb data') ); ?>
</div>


<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        <li><?php
            echo $this->Html->link(__('Load data from IMDb'), array('action' => 'imdb', $film['Film']['id'],
                true)
            );
        ?></li>
        <li><?php echo $this->Html->link(__('Edit Film'), array('action' => 'edit', $film['Film']['id'])); ?></li>
        <li><?php echo $this->Html->link(__('List Films'), array('action' => 'index')); ?></li>
    </ul>
</div>