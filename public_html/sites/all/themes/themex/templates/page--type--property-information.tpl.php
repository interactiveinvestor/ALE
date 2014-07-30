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
	      <h1 class="page-title">Property Information</h1>
	    <?php endif; ?>
			<?php echo $breadcrumb; ?>			
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
