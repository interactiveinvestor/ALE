<div class='manage-compendium'>
	<form method='post' action='' >		
		<ul class='page-sub-menu'>		
			<li><input type='submit' name='email-compendium' value ='Email my compendium'></li>
			<li><input type='submit' name='create-compendium' value ='Create my compendium'></li>
			<!-- <li class='note'>Your included pages are listed below.</li> -->
		</ul>	
		<div class='note'><?php echo getTermDescription('Compendium Instructions') ?></div>	
		<div class='included-pages'>Included Pages</div>
	<?php foreach ($compendium as $key => $page): ?>		
		<?php $pageId='page-'.$key ?>
		<?php $page['display']==1 ? $checked='checked' : $checked='' ?>
		<div class='page<?php if(!isset($page['state'])) echo " default-page"?>' >
			<input id='<?php echo $pageId ?>' type='checkbox' name='<?php echo $pageId ?>' value='<?php echo $pageId ?>' <?php echo $checked ?>>
			<label for='<?php echo $pageId ?>'><?php echo $page['title'] ?></label>
		</div>
	<?php endforeach ?>
	</form>
</div>