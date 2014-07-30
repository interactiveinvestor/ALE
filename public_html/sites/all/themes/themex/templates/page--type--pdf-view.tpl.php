<?php global $base_url;  ?>
<script type="text/javascript" charset="utf-8">var BaseUrl="<?php echo $base_url; ?>";</script>
<div id="wrapper" class='content-holder'>	
	<div id="header" > 
		<div id='logo'>
			<a href='<?php print $base_url; ?>'>
				<img  src='<?php echo $base_url ?>/sites/all/themes/themex/images/logo.gif' alt='ALE Property Group'/>
				<h2 id="site-slogan"><?php echo $site_slogan ?></h2>
			</a>
		</div>
		<?php print render($page['header']); ?>	
	</div>
	<div id="page"  class="content-holder clearfix">	
		<div class='page-holder'>
			<?php print render($page['help']); ?>	
			<?php if ($action_links): ?>
			<ul class="action-links">
				<?php print render($action_links); ?>
			</ul>
			<?php endif; ?>	
			<?php if ($title): ?>

	    <?php endif; ?>
			<?php //if(!$is_front){echo $breadcrumb;} ?>
			<?php 
			
			$content='';			
			foreach ($page['content']['system_main']['nodes'] as $key => $value) {				
				if(is_numeric($key)){		
					$content.=render(node_view(node_load($key), 'pdf_view'));
				}
			}	
			print ($content);			
				
		
			if(drupal_get_path_alias($_GET['q'])=='highlights'){
				print views_embed_view('nodequeue_2','block');
			}
			
			?>	
			
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
<div class='pdf-footnote'>
	<img  src='<?php echo $base_url ?>/sites/all/themes/themex/images/logo-print.jpg' alt='ALE Property Group'/>
	<p>
		<?php echo current(taxonomy_get_term_by_name('Compendium PDF Footnote'))->description ?><br/>
		<?php echo "http://www.aleproperties.com.au".strtok($_SERVER['REQUEST_URI'],'?') ?>
	</p>
</div>