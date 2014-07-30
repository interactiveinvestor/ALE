
<?php include ('header.tpl.php'); ?>

	<div id="page"  class="content-holder clearfix">	
		<?php if (!empty($messages)): ?>
		<div class='anon-messages'>
			<?php print $messages; ?>
		</div>
		<?php endif ?>
		<?php print render($page['content_top']);?>
		
		<div class='page-holder'>
			<?php print render($page['help']); ?>	
			<?php if ($action_links): ?>
			<ul class="action-links">
				<?php print render($action_links); ?>
			</ul>
			<?php endif; ?>	
			<?php if ($title): ?>
	      <h1 class="page-title"><?php print $title; ?></h1>
	    <?php else:?>
				<h1 class='page-title mobile-title'>ALE Property Group</h1>
			<?php endif; ?>	
			<?php //if(!$is_front){echo $breadcrumb;} ?>
			<?php print render($page['content']); ?>	
			<?php 
			$_tabs=trim(render($tabs));
			if (!empty($_tabs)): ?>
				<div class="tabs">
					<?php print $_tabs; ?>
				</div>
			<?php endif; ?>	
		</div>
	</div> 
</div>

<?php include ('footer.tpl.php'); ?>	
