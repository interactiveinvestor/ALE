<!-- <?php if($logged_in): ?>
<div class='messages-holder'><?php print $messages; ?></div>
<?php endif; ?> -->
<div id="footer" >
	<div class='footer-holder'>	
		<div class='footer-menu'>
			<?php _DM::recursiveMenu(menu_tree_all_data('menu-footer-menu'),$footerMenu); echo $footerMenu;?>
		</div>
		<div class='copyright'>
			<a href='http://interactiveinvestor.com.au' target='_blank'>Interactive Investor &copy; <?php echo date('Y') ?></a>
		</div>
	</div>
</div>
