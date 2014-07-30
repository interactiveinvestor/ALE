<?php //addProperties(); ?>
<?php
global $madeofcode;
include($madeofcode.'classes'.ds.'InteractiveMap.php');



?>
<div class='interactive-map-holder'>
	<div id='interactive-map'></div>
	<div class='map-instructions popup'>
		<a class='handler' hred='JavaScript:void(0)'><?php echo getTermDescription('Map Instructions Title') ?></a>
		<div class='content'>
			<?php echo getTermDescription('Map Instructions') ?>
		</div>
	</div>
</div>

<div class='map-data'>
	<?php echo InteractiveMap::getLocations(); ?>
</div>


<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry,places&sensor=true"></script>
<script type="text/javascript" src="/sites/all/themes/themex/js/marker-clusterer.js"></script>
<script type="text/javascript" src="/sites/all/themes/themex/js/maps.js"></script>
<script type="text/javascript" src="/sites/all/themes/themex/js/infobox.js"></script>
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function(){
		setTimeout(function(){
			var map=new Map('interactive-map');
		},1000)
		
	});
	
</script>