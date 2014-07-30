var googlemap;

function USGSOverlay(bounds, image, map) {

  // Now initialize all properties.
  this.bounds_ = bounds;
  this.image_ = image;
  this.map_ = map;

  // We define a property to hold the image's div. We'll 
  // actually create this div upon receipt of the onAdd() 
  // method so we'll leave it null for now.
  this.div_ = null;

  // Explicitly call setMap on this overlay
  this.setMap(map);
}

USGSOverlay.prototype.onAdd = function() {

  // Note: an overlay's receipt of onAdd() indicates that
  // the map's panes are now available for attaching
  // the overlay to the map via the DOM.

  // Create the DIV and set some basic attributes.
  var div = document.createElement('DIV');
  div.style.borderStyle = "none";
  div.style.borderWidth = "0px";
  div.style.position = "absolute";
	div.setAttribute("class", "customBox"); //For Most Browsers
	div.setAttribute("className", "customBox"); //For IE; harmless to other browsers.
//	div.style.backgroundColor = "red";
	
	
	//add text
	var txtNode = document.createTextNode("1"); 
	div.appendChild(txtNode);
	
	
  // Create an IMG element and attach it to the DIV.
  
	var img = document.createElement("img");
  img.src = this.image_;
  img.style.width = "35px";
  img.style.height = "43px";
  img.style.position = 'absolute';
	div.appendChild(img);
	
  // Set the overlay's div_ property to this DIV
  this.div_ = div;

  // We add an overlay to a map via one of the map's panes.
  // We'll add this overlay to the overlayImage pane.
  var panes = this.getPanes();
  panes.overlayImage.appendChild(div);
}

USGSOverlay.prototype.draw = function() {

  // Size and position the overlay. We use a southwest and northeast
  // position of the overlay to peg it to the correct position and size.
  // We need to retrieve the projection from this overlay to do this.
  var overlayProjection = this.getProjection();

  // Retrieve the southwest and northeast coordinates of this overlay
  // in latlngs and convert them to pixels coordinates.
  // We'll use these coordinates to resize the DIV.
  // var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
  // var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());

  // Resize the image's DIV to fit the indicated dimensions.
  var div = this.div_;
  // div.style.left = sw.x + 'px';
  // div.style.top = ne.y + 'px';
  // div.style.width = (ne.x - sw.x) + 'px';
  // div.style.height = (sw.y - ne.y) + 'px';
}

USGSOverlay.prototype.onRemove = function() {
  this.div_.parentNode.removeChild(this.div_);
  this.div_ = null;
}

USGSOverlay.prototype = new google.maps.OverlayView();

Map=function(mapId,setBounds){	
	
	if(setBounds===undefined) setBounds=false;
	//setBounds=true;
	
	var self=this;
	this.mapId=mapId;
	map_center= new google.maps.LatLng('-28.3456','134.4346');
	var myOptions = {
		center: map_center,
		zoom: 4, 
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};			
	
	this.markers=[];
	this.infoBoxes=[];
	
	this.map = googlemap = new google.maps.Map(document.getElementById(mapId),myOptions);	
	
	
	
	var bounds = new google.maps.LatLngBounds();
	
	jQuery('.map-data > div').each(function(i,e){

		var latitude=parseFloat(jQuery(e).children('.field-latitude').html().replace(/^\s+|\s+$/g, "")).toFixed(6);
		var longitude=parseFloat(jQuery(e).children('.field-longitude').html().replace(/^\s+|\s+$/g, "")).toFixed(6);
		var pos=new google.maps.LatLng(latitude.toString(),longitude.toString());

		bounds.extend(pos);
		var marker=new google.maps.Marker({
			__index:i,
			map:self.map,
			position:pos,
			clickable:true, 
			visible: true,
			linkUrl:jQuery(e).find('.title-').children('a').attr('href'),
			icon: "/sites/all/themes/themex/images/marker.png",
		});
		
		self.markers.push(marker);

		var infoboxOptions = {
			content: '<div class="marker-info" id="marker-info'+i+'"><div class="close-btn" ></div>'+jQuery(e).html()+'</div>',
			boxStyle: {
				margin:'-222px 0px 0px -66px',
				top:'-222px'
			},							
			closeBoxURL: ""
		}							
		self.infoBoxes.push(new InfoBox(infoboxOptions));	
		self.infoBoxes[i].__index=i;
		self.infoBoxes[i].__hide=true;

		google.maps.event.addListener(marker, 'mouseover', function () {
			if(jQuery('body').hasClass('desktop-site') && !jQuery('body').hasClass('ipad-site')){
				self.infoBoxes[marker.__index].__hide=false;
				openWindow(marker.__index);
			}
		});
		
		self.infoBoxes[marker.__index].open(self.map,self.markers[marker.__index]);		

		google.maps.event.addListener(self.infoBoxes[i], 'domready',function(el){
			var infobox=this;
			jQuery('#marker-info'+this.__index).css({opacity:0,'display':'none'});
	
			jQuery('#marker-info'+this.__index).mouseover(function(){
				if(jQuery('body').hasClass('desktop-site') && !jQuery('body').hasClass('ipad-site')){
					setTimeout(function(){
						self.infoBoxes[infobox.__index].__hide=false;
					},1000);				
				}
			});	
	
			jQuery('#marker-info'+this.__index).mouseout(function(){
				if(jQuery('body').hasClass('desktop-site') && !jQuery('body').hasClass('ipad-site')){
					setTimeout(function(){
						self.infoBoxes[infobox.__index].__hide=true;
					},1000);	
					closeWindow('#marker-info'+infobox.__index,infobox.__index);
				}			
										
			});	
			
			jQuery('#marker-info'+this.__index+' .close-btn').click(function(){
				closeWindow('#marker-info'+infobox.__index,infobox.__index,0);
			});
			
		});
		
		google.maps.event.addListener(marker, 'click', function () {
			if(jQuery('body').hasClass('mobile-site') || jQuery('body').hasClass('ipad-site') ){
				var display=jQuery('#marker-info'+marker.__index).css('display');
				if(display=='none'){
					openWindow(marker.__index);
				}else{
					closeWindow('#marker-info'+marker.__index,marker.__index,0);
				}
			}
			//window.open(marker.linkUrl);
		});		
		
		google.maps.event.addListener(marker, 'mouseout', function () {
			if(jQuery('body').hasClass('desktop-site') && !jQuery('body').hasClass('ipad-site')){
				self.infoBoxes[marker.__index].__hide=true;
				closeWindow('#marker-info'+marker.__index,marker.__index);
			}
		});
		
			
	});
	
	jQuery('.map-data').remove();

			
	function closeWindow(_window,index,timeout){
		if(timeout===undefined){
			timeout=2000;
		}
		setTimeout(function(){
			if(self.infoBoxes[index].__hide===true){
				jQuery(_window).stop();
				var duration=500;
				jQuery(_window).transition({
					opacity:0
				},duration,'easeOutQuart');
				setTimeout(function(){
					jQuery(_window).css({'display':'none'});
				},500);
			}
		},timeout);
	}
	
	function openWindow(index){
		jQuery('#marker-info'+index).stop();
		jQuery('#marker-info'+index).css({opacity:0,'display':'block'});
		jQuery('#marker-info'+index).transition({
			opacity:1
		},500,'easeInQuart');
	}
	
	
	mcOptions = {
		styles: [
			{height: 53, url: "/sites/all/themes/themex/images/cluster-53.png",width: 53,opt_textSize: 14},
			{height: 56, url: "/sites/all/themes/themex/images/cluster-53.png",width: 56,opt_textSize: 14},
			{height: 66,url: "/sites/all/themes/themex/images/cluster-53.png", width: 66},
			{height: 78,url: "/sites/all/themes/themex/images/cluster-53.png",width: 78},
			{height: 90,url: "",width: 90 }]
	};
	// 
	this.clusterer = new MarkerClusterer(this.map,this.markers,mcOptions);
	
	if(setBounds===true){
		setTimeout(function(){
			self.map.fitBounds(bounds);
			var zoom = self.map.getZoom();
			self.map.setZoom(zoom > 16 ? 16 : zoom);
		},1300);
	} 
	
	
	
	setTimeout(function(){
		self.map.setCenter(map_center);
		if(jQuery(window).width()<400){			
			self.map.setZoom(3);
		}
		
	},1000);
	

	
}

Map.prototype={

	
}
