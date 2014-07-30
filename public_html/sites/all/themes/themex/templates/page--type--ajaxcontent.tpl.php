<?php print render($page['help']); ?>	
<?php if ($action_links): ?>
<ul class="action-links">
	<?php print render($action_links); ?>
</ul>
<?php endif; ?>	
<?php if ($title): ?>
    <h1 class="page-title"><?php print $title; ?></h1>
  <?php endif; ?>
<?php if(!$is_front){echo $breadcrumb;} ?>
<?php print render($page['content']); ?>	
<?php 
$_tabs=trim(render($tabs));
if (!empty($_tabs)): ?>
	<div class="tabs">
		<?php print $_tabs; ?>
	</div>
<?php endif; ?>	
