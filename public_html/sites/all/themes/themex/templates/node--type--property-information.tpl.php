<?php
global $themePath;
$state=array_pop(explode('/',strtok($_SERVER['REQUEST_URI'],'?')));
$properties =	 Properties::getByState($state);

if(empty($properties)) $properties =	 Properties::getAll();
$totalLandArea = Properties::getTotal('field_land_area',$properties);
?>
<h2 class="page-title"><?php if (isset($_GET['pdf-view'])):?>State Overview: <?php endif ?><?php echo $node->title; ?></h2>

<div class='left-column'>
	<div id="node-<?php print $node->nid; ?>" class="n-<?php echo _DM::machine_name($node->title) ?> <?php print $classes; ?> clearfix"<?php print $attributes; ?>>
	   <?php print render($content);?>
	</div>
	
	<?php if (isset($_GET['pdf-view'])): ?>
	<div class='state-map'>
		<img src='/sites/all/themes/themex/images/state_maps/<?php echo $state; ?>.jpg' alt='<?php echo Text::humanize($state) ?>' />
	</div>
	<?php endif ?>
	<div class='portfolio'>
		<ul>
			<li>
				<h3>
					<div class='label'>Total value of State portfolio</div>
					<div class='value'>$<?php echo nice_number(Properties::getTotal('field_valuation',$properties)) ?></div>
				</h3>
			</li>
			<li>
				<h3>
					<div class='label'>Average Value</div>
					<div class='value'>$<?php echo nice_number(Properties::getAverage('field_valuation',$properties)) ?></div>
				</h3>
			</li>
			<li>
				<h3>
					<div class='label'>Properties</div>
					<div class='value'><?php echo count($properties) ?></div>
				</h3>
			</li>
			<li>
				<h3>
					<div class='label'>Average Land Area</div>
					<div class='value'>
						<?php echo number_format(Properties::getAverage('field_land_area',$properties)) ?><span class='lowercase'>m</span><sup>2</sup>
					</div>
				</h3>
			</li>
			<li>
				<h3>
					<div class='label'>Total Land Area</div>
					<div class='value'>
						<?php echo number_format(Properties::getTotal('field_land_area',$properties)) ?><span class='lowercase'>m</span><sup>2</sup>
					</div>
				</h3>
			</li>
			
			<li class='accordion'>
				<a href='JavaScript:void(0)'>View All Properties</a>
				<?php include($themePath.DS.'templates'.DS.'property-list.php') ?>
			</li>
		</ul>
	</div>
</div>
<?php $note=current(taxonomy_get_term_by_name('Highlights notes'))->description; ?>
<div class='note'><?php echo $note ?></div>
<?php if (!isset($_GET['pdf-view'])): ?>
	<div id='property-info-map'>
		<div id='property-info-map-holder'></div>
		<div class='map-instructions popup'>
			<a class='handler' hred='JavaScript:void(0)'><?php echo getTermDescription('Map Instructions Title') ?></a>
			<div class='content'>
				<?php echo getTermDescription('Map Instructions') ?>
			</div>
		</div>
	</div>
	<div class='map-data'>
	<?php
		foreach ($properties as $key => $property) {
			$teaser= node_view($property, 'teaser');
			$teaser['links']['node']['#links']=array();
			echo render($teaser);
		}		
	?>
	</div>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry,places&sensor=true"></script>
	<script type="text/javascript" src="/sites/all/themes/themex/js/marker-clusterer.js"></script>
	<script type="text/javascript" src="/sites/all/themes/themex/js/maps.js"></script>
	<script type="text/javascript" src="/sites/all/themes/themex/js/infobox.js"></script>
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function(){var map=new Map('property-info-map-holder',true);});	
	</script>
<?php endif ?>
