<?php
/**
 *	View for Home page
 */

if (!Configure::read('debug')):
	throw new NotFoundException();
endif;

App::uses('Debugger', 'Utility');
?>

<h2>Films Home</h2>
<p>
	Film database.
</p>
