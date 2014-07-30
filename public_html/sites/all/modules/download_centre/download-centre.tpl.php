<div class='download-centre'>
	<div class='note instructions'>
		<?php echo getTermDescription('Download Full Compendium Instructions') ?>
	</div>
	<ul class='tabs'>
		<li class='first'>
			<a class='handler' href="JavaScript:void(0)">Sort By: 
				<span class='sort-criteia'>
					&nbsp;
					<?php if (empty($_GET['year'])): ?>
					All
					<?php else: ?>
					<?php echo $_GET['year'] ?>
					<?php endif; ?>
				</span>
			</a>
			<ul >
				<li>
					<?php !isset($_GET['year']) ? $class='selected' : $class='unselected' ?>
					<a class="<?php echo $class ?>" href='<?php echo $base_url ?>'>All</a>
				</li>
				<?php foreach ($years as $key => $year): ?>
				<li>
					<?php isset($_GET['year']) && $_GET['year']==$year ? $class='selected' : $class='unselected' ?>
					<a class='<?php echo $class ?>' href='<?php echo $base_url ?>?year=<?php echo $year ?>'>
						<?php echo $year ?>
					</a>
				</li>
				<?php endforeach ?>
			</ul>
		</li>
		
	</ul>
	<ul class='downloads'>

	<?php foreach ($downloads as $key => $download): ?>
		<li class='download'>
			<?php print(render($download)) ?>
		</li>
	<?php endforeach ?>
	</ul>
</div>