<?php
    $this->Html->scriptBlock( "
        $(document).ready(function(){
            // set truncated cells
            $('table td.trunc').dotdotdot({
                height      : 20,
                callback    : function(isTruncated, orgContent) {
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

            // clear the search form
            $('#search-clear').click(function () {
                $('.searchform input[type=text], .searchform input[type=select]').val('');
            });
    
            // make field row clicks open the view action on table cells
            $('table td.cview').click(function() {
                document.location.href = '"
                .   $this->Html->url(array('action' => 'view', '__ID__'))
                .   "'.replace('__ID__', $(this).parent().find('.idcontainer').text());
            });
            
        });",
        array('inline' => false)
    );
?>

<div class="actors index">
    <h2><?php echo __('Actors'); ?></h2>

    <div class="films searchform">
        <?php echo $this->Form->create('Actor', array('action' => 'search')); ?>

        <fieldset>
            <legend><?php __('Search'); ?></legend>
            <?php
                echo $this->Form->input('Search.name', array(
                    'after' => __('wildcard: *',true),
                ));

                echo $this->Form->submit('Search');

                echo $this->Html->link(__('Clear Fields', true), 'javascript:void(0)', array(
                    'id' => 'search-clear',
                    'class' => 'searchlink',
                ));
            ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>

    <table cellpadding="0" cellspacing="0">
    <tr>
            <th style="width: 70px"><?php echo $this->Paginator->sort('id'); ?></th>
            <th style="width: 250px"><?php echo $this->Paginator->sort('name'); ?></th>
            <th style="width: 70px"><?php
                echo $this->Paginator->sort('actorrole_count', 'Films', array('direction' => 'desc'));
            ?></th>
            <th style="width: 70px"><?php echo __('link'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
    </tr>
    <?php foreach ($actors as $actor): ?>
    <tr>
        <td class="id cview"><?php
            echo h($actor['Actor']['id']);
            echo $this->Html->tag('span', $actor['Actor']['id'], array('class' => 'hidden idcontainer'));
        ?>&nbsp;</td>
        <td class="trunc cview"><?php echo h($actor['Actor']['name']); ?>&nbsp;</td>
        <td class="trunc cview"><?php echo h($actor['Actor']['actorrole_count']); ?>&nbsp;</td>
        <td class="c">
            <?php if (!empty($actor['Actor']['imdb_id'])): ?>
                <?php
                    echo $this->Html->link(
                        'IMDb',
                        'http://www.imdb.com/name/nm' . $actor['Actor']['imdb_id'] . '/',
                        array('target' => '_blank')
                    );
                ?>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
        </td>
        <td class="actions"><?php
                echo $this->Html->link(__('View'), array('action' => 'view', $actor['Actor']['id']));
        ?></td>
    </tr>
<?php endforeach; ?>
    </table>
    <p>
    <?php
    echo $this->Paginator->counter(array(
        'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total')
    ));
    ?></p>
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
        <li><?php
            echo $this->Html->link(__('List Films'), array('controller' => 'films', 'action' => 'index'));
        ?></li>
    </ul>
</div>
