<?php

function getBgImage($page){
	$node=current($page['content']['system_main']['nodes']);
	
	if(isset($node['#node']->field_backgrounds) && isset($node['#node']->field_backgrounds['und'])){
		$backgrounds_nid=$node['#node']->field_backgrounds['und'][0]['nid'];
		$bg_node=node_load($backgrounds_nid);
	}else{		
		$param = array(
		    'type' => 'page',
		    'title' => $node['#bundle'],
		    'status' => 1
		);
		$node = node_load($param);
		
		if(isset($node->field_backgrounds) && isset($node->field_backgrounds['und'])){
			$backgrounds_nid=$node->field_backgrounds['und'][0]['nid'];
			$bg_node=node_load($backgrounds_nid);
		}		
	}
	
	if($bg_node){
		
		$time=date('G');
		$field='field_night_image';
		if($time>5 && $time<=12) $field='field_morning_image';
		elseif($time>12 && $time<=16) $field='field_day_image';
		elseif($time>16 && $time<=19) $field='field_afternoon_image';
		else $field='field_night_image';
		
			
		$field_picture = current(field_get_items('node', $bg_node, $field));
		
		
		
		$field_picture['style_name'] = 'section_backgrounds';
	  $field_picture['path'] = $field_picture['uri'];
		$picture = theme('image_style', $field_picture);	
		return $picture;
	}
		
		
	
}

function get_start_record($displayedRecords){
	
	$startRecord=1; 
	
	if(isset($_GET['page']) && $_GET['page']!=''){
		
		$startRecord=($_GET['page']*$displayedRecords)-$displayedRecords+1;

	}
	
	return $startRecord;
}
function pager($url,$totalRecords,$displayedRecords,$displayedIndexes,$instance, $template=null,$divider='&bull;'){
	global $pagination_template;
	$pagination_template=$template;
	global $output;
	global $totalIndexes;
	global $prev_link;
	global $next_link;
	
	global $search_link;
	global $order_link;
	
	global $extralink;
	//$extralink=$search_link.$order_link;
	$divider="<li>{$divider}</li>";
	
	//if($pagination_template==null) $pagination_template=ROOT.DS.'templates/pagination.tpl.php';
	
	$startRecord=get_start_record($displayedRecords);

	
	$remainingRecords = $totalRecords%$displayedRecords;
	$currentIndex=($startRecord + ($displayedRecords-1))/$displayedRecords -1;
	$totalIndexes= ceil($totalRecords/$displayedRecords);
	if($totalRecords<$displayedRecords*$displayedIndexes-$displayedRecords+1){
		$displayedIndexes = ceil($totalRecords/$displayedRecords);
		$remainingIndexes=1;
		$totalBlocks=1;
		$currentBlock=1;
	}else{
		$remainingIndexes=($totalIndexes-1)%($displayedIndexes-2);
		$totalBlocks=ceil (($totalIndexes-1)/($displayedIndexes-2));
		$currentBlock=ceil($currentIndex/($displayedIndexes-2));
	}
	$recordsInBlock = ($displayedIndexes-2)*$displayedRecords;
	if($remainingIndexes==1){
		$totalBlocks--;
	}

	if($currentBlock>$totalBlocks){
		$currentBlock--;
	}
	$output="";
	$blockDif=0;
	$remainderDif=0;
	
	if($totalRecords==0){$totalRecords=1;}
	
	if($totalRecords>0){
		
		for ($i=1;$i<=$displayedIndexes;$i++){
			$_divider=$divider;
			$dots="";
			$class2="";
			$lastBase=$i*$displayedRecords;
			$firstBase=$lastBase-($displayedRecords-1);
			if($currentBlock!=0){
				$blockDif=$recordsInBlock*($currentBlock-1);
			}
			if($currentBlock==$totalBlocks){
				if($remainingIndexes==0){
					$remainderDif=-$displayedRecords;
				}
				if($remainingIndexes>1){
					$remainderDif=-($displayedIndexes-1-$remainingIndexes)*$displayedRecords;	
				}		
			}			
			$first=($firstBase+$blockDif+$remainderDif+$displayedRecords-1)/$displayedRecords;			
			if($i==1){			
				$first=1;
				if($currentBlock>1 && $totalRecords>$displayedRecords*$displayedIndexes){
					$dots="<li class='dots'>...</li>";
					$_divider='';
				}
				
			}
			if($currentBlock!= $totalBlocks && $i==$displayedIndexes-1 && $totalIndexes>$displayedIndexes){
				$_divider='';
			}
			if( $i==$displayedIndexes){
				$_divider='';
			}
			if($i==$displayedIndexes){
				if($currentBlock!= $totalBlocks && $totalRecords>$displayedRecords*$displayedIndexes){
					$output.="<li class='dots'>...</li>";
					$_divider='';
				}
				$first=$totalIndexes;
				$class2=" last_index";
				
			}	
			
			if($first==$currentIndex+1){$class="index_selected";}else{$class="index_unselected";}
			$link=$first;
			$output.= "<li><a href='/".$url."?page={$link}".getUrlParameters('page')."' class=\"".$class.$class2."\">{$link}</a></li>{$_divider}".$dots;
		}

		$next_link=(($startRecord+$displayedRecords-1)/$displayedRecords) +1;
		$prev_link=$next_link-2;	
		if($startRecord+$displayedRecords>$totalRecords){
			$next_link="1";
		}
		
		if($startRecord-$displayedRecords<1){
			$prev_link= $totalIndexes;
		}
		
		if($totalIndexes>1){
				$output2= "<div class='pager'><li><a href='/".$url."?page={$prev_link}".getUrlParameters('page')."'>prev</a></li>".$output."<li><a href='/".$url."?page={$next_link}".getUrlParameters('page')."'>next</a></li></div>";	
		}else{
			$output2= "";
		}

	
	}else{ 
		
		$output2= "";
	}
	if (isset($output2)){return $output2;}
}

function getUrlParameters($exeption){	
	$params=explode('&',$_SERVER['QUERY_STRING']);	
	foreach ($params as $key => $param) {
		if(str_replace(strstr($param,'='),'',$param)==$exeption){
			unset($params[$key]);
		}
	}	
	$params=join('&',$params);	
	
	if(!empty($params)) $params='&'.$params;
	
	return $params;	
}

function extract_values($value,$field,$length){
	
	$value=str_replace('%20',' ',$value);	
	$pattern = "/$value/i";
	preg_match_all($pattern, $field, $matches, PREG_OFFSET_CAPTURE);
	$trimmed='';
	
	if(empty($matches[0])){	
		
		$trimmed = substr($field, 0, $length);
		$trimmed = preg_replace('/ [^ ]*$/', ' ...', $trimmed);	
			
	}else{
		
		$matchCount=count($matches[0]);
		
		$wordCount=$matchCount*strlen($value);	
			
		$charsPerResult=round(($length - $wordCount)/$matchCount);	
		
		if($charsPerResult<20){
			$charsPerResult=$length;
			foreach ($matches[0] as $key => $value) {
				if($key!=0){
					unset($matches[0][$key]);
				}
			}
		}
		$trimmed='';		
		$count=0;		
		$prev_position=0;
		
		foreach ($matches[0] as $key => $value) {		
			$count++;
			$dif=0;	
			$lengthDif=0;	
			$half=round($charsPerResult/2);
			
			if($value[1]-round($charsPerResult)>0){
				
				$dif=round($charsPerResult/2);
				if(substr($field, $value[1]- $dif,1)!='' ){		
													
					for ($i=1; $i <20 ; $i++) { 
						
						if(substr($field, $value[1]- $dif-$i,1)==' '){
							
							$dif=$dif+$i;
							
							break;
							
						}
					}			
						
					for ($i=0; $i <20 ; $i++) { 
						
						if(substr($field, $value[1]- $dif + $charsPerResult + $i, 1)==' '){					
							
							$lengthDif=$i;
							
							break;
							
						}
					}	
				}	
			}else{
				$dif=$value[1];
			}	 

			$prev=$dif+$prev_position;
			$prev_position=$value[1];
			
			if($prev> $value[1]){
				continue;
			}
			$trimmed.='<span class="search-excerpt"> ... </span>';	
				
			if($dif!=0){}	
				
			$trimmedValue=str_replace($value[0],'<span class="searched-term">'.$value[0].'</span>', substr($field, $value[1]- $dif, $charsPerResult + $lengthDif ));					
			$trimmed.=$trimmedValue;
		}		
		//if(!rtrim('<span class="search-excerpt"> ... <span>',$trimmed)){
			$trimmed=$trimmed.'<span class="search-excerpt"> ... </span>';	
		//}
			
	}	
	return $trimmed;
}
