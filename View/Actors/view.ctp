
<h2><?php
    echo __('Actor: %s [#%s]', h($actor['Actor']['name']), h($actor['Actor']['id']));
?></h2>
<p>
    &nbsp;
</p>

<p>&nbsp;</p>

<h2><?php echo __('Films'); ?></h2>
    <?php if (!empty($roles)): ?>
    <table cellpadding = "0" cellspacing = "0">
    <tr>
        <th style="width: 200px"><?php echo __('Role / Character'); ?></th>
        <th style="width: 400px"><?php echo __('Movie'); ?></th>
        <th style="width: 60px"><?php echo __('Year'); ?></th>
        <th style="width: 110px"><?php echo __('Seen Last'); ?></th>
        <th style="width: 70px"><?php echo __('Link'); ?></th>
        <th style="width: 125px" class="actions"><?php echo __('Actions'); ?></th>
    </tr>
    <?php
        $i = 0;
        foreach ($roles as $role): ?>
        <tr>
            <td><?php echo h($role['ActorRole']['role']); ?></td>
            <td><?php echo h($role['Film']['title']); ?></td>
            <td><?php echo h($role['Film']['year']); ?></td>
            <td><?php echo $this->Time->format('Y-m-d', $role['Film']['seen_last']); ?></td>
            <td class="c">
            <?php if (!empty($role['Film']['imdb'])): ?>
                <?php
                    echo $this->Html->link(
                        'IMDb',
                        'http://www.imdb.com/title/tt' . $role['Film']['imdb'] . '/',
                        array('target' => '_blank')
                    );
                ?>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
        </td>
            <td class="actions">
                <?php
                    echo $this->Html->link(__('View'), array(
                        'controller' => 'films',
                        'action' => 'view', $role['Film']['id']
                    ));
                    echo $this->Html->link(__('Edit'), array(
                        'controller' => 'films',
                        'action' => 'edit', $role['Film']['id']
                    ));
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
        <li><?php echo $this->Html->link(__('List Actors'), array('action' => 'index')); ?></li>
    </ul>
</div>
