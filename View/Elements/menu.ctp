<?php
/**
 *  Element: menu
 *
 *  Shared menu for all pages
 */
?>
 <div id="menu">
    <ul>
        <li>
            <?php
                echo $this->Html->link('Films', array('controller' => 'films', 'action' => 'index')); 
                echo $this->Html->link('Directors', array('controller' => 'directors', 'action' => 'index'));
                echo $this->Html->link('Actors', array('controller' => 'actors', 'action' => 'index'));
                echo $this->Html->link('Stats', array('controller' => 'films', 'action' => 'stats')); 
                echo $this->Html->link('New Film', array('controller' => 'films', 'action' => 'add')); 
            ?>
        </li>
    </ul>
 </div>