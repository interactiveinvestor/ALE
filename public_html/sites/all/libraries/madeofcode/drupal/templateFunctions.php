<?php
function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function wrapViewRows($rows,$frequency, $holderClass){
	$_rows=explode('<div class="views-row',$rows);
	foreach ($_rows as $key => $row) $_rows[$key]='<div class="views-row '.$_rows[$key];
	unset($_rows[0]);
	$rows='';
	$counter=0;
	$rowCount=0;
	foreach ($_rows as $key => $row) {
		$counter++;
		 if($counter%$frequency==1){
			$rowCount++;
			$specialClass='';
			if($rowCount==1){
				$specialClass=' row-first';
			}
			
			$rows.='<div class="'.$holderClass.'">';	
		} 
		 $rows.=$row;	
		if($counter%$frequency==0){
			$rows.='</div>';
		} 	
	}
	if($counter%$frequency!=0){
		//echo 'yeah';
		$rows.='</div>';
	}	
	$return=array('rows'=>$rows,'wrap_count'=>$rowCount);	
	return $return;	
}
function countViewRows($rows){	
	$pattern='/views-row /i';
	preg_match_all($pattern, $rows,$matches);
	return count($matches[0]);
}

function setLanguage(){
	
	

}


function getTwitterFeed($username,$num = 5){
	//$username = "your-user-name";
	$file = "http://search.twitter.com/search.json?q=from:" . $username . "&amp;rpp=" . $num;

	// $newfile = dirname(__FILE__)."/twitternew.json";
	// $file = dirname(__FILE__)."/twitter.json";

	// copy($feed, $newfile);
	// 
	// $oldcontent = @file_get_contents($file);
	// $newcontent = @file_get_contents($newfile);
	// 
	// if($oldcontent != $newcontent) {
	// 	copy($newfile, $file);
	// }
	
	$tweets = @file_get_contents($file);

	$tweets = json_decode($tweets);
	$output='';
	$output.= "<ul>";
	for($x=0;$x<$num;$x++) {
		$str = preg_replace("#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#","<a href=\"\\0\">\\0</a>", $tweets->results[$x]->text);
		$pattern = '/[#|@][^\s]*/';
		preg_match_all($pattern, $str, $matches);	

		foreach($matches[0] as $keyword) {
			$keyword = str_replace(")","",$keyword);
			$link = str_replace("#","%23",$keyword);
			$link = str_replace("@","",$keyword);
			if(strstr($keyword,"@")) {
				$search = "<a href=\"http://twitter.com/$link\">$keyword</a>";
			} else {
				$link = urlencode($link);
				$search = "<a href=\"http://twitter.com/#search?q=$link\" class=\"grey\">$keyword</a>";
			}
			$str = str_replace($keyword, $search, $str);
		}

		$output.= "<li>".$str."</li>\n";
	}
	$output.= "</ul>";
	
	return $output;
}

function getMaps(){
	
	$output='<ul>';
	
	$terms=taxonomy_get_tree(2);	
	$locations=array();	
	foreach ($terms as $key => $term) {
		$locations[]=taxonomy_term_load($term->tid);
	}	

	$count=0;
	foreach ($locations as $key => $location) {
		$count++;
			$output.='<li>';
			$output.="<div class='map-holder'><div class='map' id='google-map$count'></div></div>";	
			$output.='<div class="info">';
				$output.='<h3>'.$location->name.'</h3>';
				$output.='<div class="address">'.$location->description.'</div>';	
			$output.='</div>';			
			$output.='<div class="hidden">';
				$output.='<div class="latitude">'.$location->field_latitude['und'][0]['value'].'</div>';
				$output.='<div class="longitude">'.$location->field_longitude['und'][0]['value'].'</div>';		
			$output.='</div>';	
			$output.='</li>';
	}
	$output.='</ul>';
	return $output;
}


//function ur