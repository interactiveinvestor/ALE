<?php global $base_url; ?>
<script type="text/javascript" charset="utf-8">
	var BaseUrl="<?php echo $base_url; ?>";
</script>

<div id="wrapper" class='content-holder'>	
	<div id="header" > 
		<div id='logo'>
			<a href='<?php print $base_url; ?>'>
				<img  src='<?php echo $base_url ?>/sites/all/themes/themex/images/logo.gif' alt='ALE Property Group'/>
				<h2 id="site-slogan"><?php echo $site_slogan ?></h2>
			</a>
		</div>
		<ul class='top-nav'>
			<li class='my-compendium'><a href='/my-compendium'>My property compendium</a></li>
			<li class='search'>
				<?php print render($page['top_nav']) ?>
			</li>
		</ul>		
		<nav>
			<a href='JavaScript:void(0)' title='Main Menu' class='button'></a>
			<?php _DM::recursiveMenu(menu_tree_all_data('main-menu'),$mainMenu); echo $mainMenu; ?>
		</nav>
		<?php print render($page['header']); ?>	
	</div>

	
	
	


	