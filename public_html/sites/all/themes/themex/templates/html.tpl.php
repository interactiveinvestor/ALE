<!doctype html<?php //print $rdf_header; ?>>
<html lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?><?php //print $html_attributes; ?>>
<head<?php //print $rdf_profile?>>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
	<meta name="viewport" content="width=device-width">
  <?php print $styles; ?>
  <?php print $scripts; ?>	
	
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>
	 
	<script type="text/javascript" charset="utf-8">
		if(jQuery(window).width()<760) jQuery('body').addClass('mobile-site');
		else if(jQuery(window).width()>=760 ) jQuery('body').addClass('desktop-site');
		/*@cc_on
		@if (@_jscript_version == 10)

		document.write(' <link type= "text/css" rel="stylesheet" href="/sites/all/themes/themex/css/ie10.css" />');
		@end
		@*/
	</script>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>


	
</body>
</html>