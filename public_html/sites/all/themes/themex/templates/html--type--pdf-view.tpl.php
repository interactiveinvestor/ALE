<!doctype html<?php //print $rdf_header; ?>>
<html lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?><?php //print $html_attributes; ?>>
<head<?php //print $rdf_profile?>>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
	<meta name="viewport" content="width=device-width">
  <?php print $styles; ?>
  <?php print $scripts; ?>	
</head>
<body class="<?php print $classes; ?> pdf-view" <?php print $attributes;?>>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>