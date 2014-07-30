<?php print $rows; ?>
<script type="text/javascript" charset="utf-8">
	var gallery=new _Gallery({
		holder:'.home-slide-gallery ul',
		items:'.home-slide-gallery li',
		fitAlign:'center',
		controls:{
			autoplay:true,
			playInterval:5000,
			index:true,						
			placement:'.region-content-top',
			cssClass:'slide-pager',
			swipe:{
				enabled:true,
				responseLimit:50
			}
		},
		animation:{
			transition:'slide',
			transitionSpeed:0.9,
			transitionInEasing:'easeOutExpo',
			transitionOutEasing:'easeOutExpo',
			fadeSpeed:1,
			fadeEasing:'linear'
		},
	});
	
</script>

