<?php
/**
 *	Default page template.
 */
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		
		// css
		echo $this->Html->css('cake.films');
		echo $this->Html->css('smoothness/jquery-ui-1.10.3.custom.min.css');
		echo $this->Html->css('qtip');

		// jquery / ui
		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery_plugins');		// third party jquery plugins
		echo $this->Html->script('jquery.dotdotdot');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<?php echo $this->element('page_header', array('title' => $title_for_layout)); ?>
		</div>
		<div id="menu">
			<?php echo $this->element('menu'); ?>
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->Session->flash('auth'); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo $this->element('page_footer'); ?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
