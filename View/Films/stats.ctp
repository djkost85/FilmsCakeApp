
<h2><?php echo __('Statistics'); ?></h2>


<?php
    // show image with year graph
    echo $this->Html->image(array('action' => 'statsImage', 'years'));
    echo $this->Html->image(array('action' => 'statsImage', 'months'));
?>

<p>&nbsp;</p>

<?php if (!empty($stats['main'])): ?>
<table cellpadding="0" cellspacing="0">
    
    <tr>
        <th class="stats_h"><?php echo __('Films Seen / Viewings'); ?></th>
        <td class="stats_a r"><?php echo h($stats['main']['film_count']); ?>&nbsp;</td>
        <td class="stats_b r"><?php echo h($stats['main']['viewing_count']); ?>&nbsp;</td>
    </tr>
    <tr>
        <th class="stats_h"><?php echo __('Viewings with Heleen'); ?></th>
        <td class="stats_a r" colspan="2"><?php
            echo $stats['main']['heleen_count'] / 60;
        ?>&nbsp;</td>
    </tr>
    <tr>
        <th class="stats_h"><?php echo __('Total Hours Watched'); ?></th>
        <td class="stats_a r" colspan="2"><?php
            printf('%.2f', $stats['main']['total_duration'] / 60);
        ?>&nbsp;</td>
    </tr>
</table>

<p>&nbsp;</p>
<?php endif; ?>


<?php if (!empty($stats['years'])): ?>
<table cellpadding="0" cellspacing="0">
    <tr>
        <th><?php echo __('Year'); ?></th>
        <th><?php echo __('Films'); ?></th>
        <th><?php echo __('Viewings'); ?></th>
        <th><?php echo __('Hours'); ?></th>
    </tr>
    <?php foreach ($stats['years'] as $key => $year): ?>
    <tr>
        <th class="stats_h"><?php echo h('Year: ' . $key); ?></th>
        <td class="stats_a r"><?php echo h($year['film_count']); ?>&nbsp;</td>
        <td class="stats_b r"><?php echo h($year['viewing_count']); ?>&nbsp;</td>
        <td class="stats_a r" colspan="2"><?php
            printf('%.2f', $year['total_duration'] / 60);
        ?>&nbsp;</td>
    </tr>
    <?php endforeach; ?>
</table>

<p>&nbsp;</p>
<?php endif; ?>

<?php if (!empty($stats['genres'])): ?>
<table cellpadding="0" cellspacing="0">
    <tr>
        <th><?php echo __('Genre'); ?></th>
        <th><?php echo __('Films'); ?></th>
        <th><?php echo __('Viewings'); ?></th>
        <th><?php echo __('Hours'); ?></th>
    </tr>
    <?php foreach ($stats['genres'] as $key => $genre): ?>
    <tr>
        <th class="stats_h"><?php echo __($genre['genre']); ?></th>
        <td class="stats_a r"><?php echo h($genre['film_count']); ?>&nbsp;</td>
        <td class="stats_b r"><?php echo h($genre['viewing_count']); ?>&nbsp;</td>
        <td class="stats_a r" colspan="2"><?php
            printf('%.2f', $genre['total_duration'] / 60);
        ?>&nbsp;</td>
    </tr>
    <?php endforeach; ?>
</table>

<p>&nbsp;</p>
<?php endif; ?>


<?php if (!empty($stats['countries'])): ?>
<?php
    // collect some countries under an 'other' category
    $max = 20;
    if (count($stats['countries']) > $max) {
        for ($x = count($stats['countries']) - 1; $x > $max; $x--) {
            $stats['countries'][$max]['film_count'] += $stats['countries'][$x]['film_count'];
            $stats['countries'][$max]['viewing_count'] += $stats['countries'][$x]['viewing_count'];
            $stats['countries'][$max]['total_duration'] += $stats['countries'][$x]['total_duration'];
            unset($stats['countries'][$x]);
        }
        // change name
        $stats['countries'][$max]['country'] = 'Other Countries...';
    }
?>
<table cellpadding="0" cellspacing="0">
    <tr>
        <th><?php echo __('Country'); ?></th>
        <th><?php echo __('Films'); ?></th>
        <th><?php echo __('Viewings'); ?></th>
        <th><?php echo __('Hours'); ?></th>
    </tr>
    <?php foreach ($stats['countries'] as $key => $country): ?>
    <tr>
        <th class="stats_h"><?php echo __($country['country']); ?></th>
        <td class="stats_a r"><?php echo h($country['film_count']); ?>&nbsp;</td>
        <td class="stats_b r"><?php echo h($country['viewing_count']); ?>&nbsp;</td>
        <td class="stats_a r" colspan="2"><?php
            printf('%.2f', $country['total_duration'] / 60);
        ?>&nbsp;</td>
    </tr>
    <?php endforeach; ?>
</table>

<p>&nbsp;</p>
<?php endif; ?>


<div class="actions">
    <h2><?php echo __('Actions'); ?></h2>
    <ul>
        <li><?php echo $this->Html->link(__('List Films'), array('action' => 'index')); ?> </li>
    </ul>
</div>

<p>&nbsp;</p>