<?php



global $themePath;
$themePath=$_SERVER["DOCUMENT_ROOT"].base_path().drupal_get_path('theme', 'themex');
global $madeofcode;
$madeofcode=$_SERVER["DOCUMENT_ROOT"].base_path().libraries_get_path('madeofcode').ds;

include($madeofcode.'classes'.ds.'include.php');
include($madeofcode.'classes'.ds.'Text.php');
include($madeofcode.'drupal'.ds.'drupalMain.php');
include($madeofcode.'classes'.ds.'Properties.php');
include($madeofcode.'classes'.ds.'Template.php');
include($madeofcode.'classes'.ds.'Array.php');
// include($themePath.'/framework/drupal/dbObject.php');

// drupal_set_message('this is a custom message','status');
// drupal_set_message('this is another custom message','status');
// drupal_set_message('A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created f','error');
// drupal_set_message('another error message','error');
// drupal_set_message('and some warning why not','warning');
// Main =>


// function themex_js_alter(&$javascript) {
//   // Swap out jQuery to use an updated version of the library.
//   $javascript['misc/jquery.js']['data'] = drupal_get_path('theme', 'themex') . '/js/jquery.js';
// }

function getTermDescription($term){
	return current(taxonomy_get_term_by_name($term))->description;
}

function themex_preprocess_node(&$variables) {
	$node=&$variables['node'];
	
	$variables['date'] = format_date($node->created, 'custom', 'F j, Y \a\t G:i');
  $variables['submitted'] = t('!username on !datetime', array('!username' => $variables['name'], '!datetime' => $variables['date']));	
	$variables['theme_hook_suggestions'][] = 'node__type__'.$variables['type'];
	$variables['theme_hook_suggestions'][] = 'node__name__'.Text::machine_name($variables['title']);
	
	if($node->type=='property'){	
		
		$property_list=&$variables['content']['field_property_list'];
		$note=current(taxonomy_get_term_by_name('Highlights notes'))->description;$variables['elements']['#groups']['group_property_data']->children[]='field_property_list';
		$variables['elements']['#group_children']['field_property_list']='group_property_data';
		$property_list['#weight'] = 200;
		
		
		if($variables['elements']['#view_mode']=='pdf_view'){
			$property_list['#markup'] ="<div class='note'>$note</div>";
			
		}
		else if($variables['elements']['#view_mode']=='full'){			
			global $themePath;
			$state=current(taxonomy_get_parents($node->field_city[$node->language][0]['tid']))->name;
			$stateNodes=Properties::getByState($state);

			$template=new __Template($themePath.DS.'templates'.DS.'property-list.php',array('properties'=>$stateNodes));				
			$property_list['#markup'] = "<h2 class='accordion'><a href='JavaScript:void(0)'>View all properties in $state</a>";	
			$property_list['#markup'] .=$template->html.'</h2>'."<div class='note'>$note</div>";
			
			$actions=&$variables['content']['field_actions'];
			$sharethis='';
			if(isset($variables['content']['links']['sharethis'])){
				$sharethis= render($variables['content']['links']['sharethis']);
			}
			$forward='';
			if(isset($variables['content']['links']['forward'])){
				$forward= render($variables['content']['links']['forward']);
			}	
			$actionsTemplate=new __Template($themePath.DS.'templates'.DS.'property-actions.php',array('node'=>$node,'sharethis'=>$sharethis,'forward'=>$forward));
			$actions['#markup']=$actionsTemplate->html;
			$actions['#weight']=100;
			
			//dpm($node);
		}
	}	
}

function themex_preprocess_page(&$vars) {	

	global $base_url;	
	if(isset($_GET['pdf-view']) && $_GET['pdf-view'] ==true){
		$vars['theme_hook_suggestions'][] = 'page__type__pdf_view';
	}
	else if(isset($_GET['ajax']) && $_GET['ajax'] == 'true' ) {
		$vars['theme_hook_suggestions'][] = 'page__type__ajaxcontent';
	}
	else{
		global $themePath;
		$jsDir=$themePath.'/js/autoload/*.js';
		$root=$themePath.'/js/autoload/';	
		$jsFrontQueue=array(
			$root.'lib/jquery/jquery-easing.js'		
		);
		$jsBackQueue =array($root.'www.js');		
		foreach (__File::getFiles($jsDir,'','_',true,$jsFrontQueue,$jsBackQueue) as $key => $script) {
			drupal_add_js(str_replace($_SERVER["DOCUMENT_ROOT"],'',$base_url.$script));		
		}	
		
		// Get the entire main menu tree
		  $main_menu_tree = menu_tree_all_data('main-menu');

		  // Add the rendered output to the $main_menu_expanded variable
		  $vars['main_menu_expanded'] = menu_tree_output($main_menu_tree);
		
		if(isset($vars['page']['content']['system_main']['nodes'])){
			$nodes=$vars['page']['content']['system_main']['nodes'];		
			if(count($nodes)==2){
				$node=current($nodes);
				if(isset($node['body'])){
					$contentType=$node['body']['#object']->type;
					$vars['theme_hook_suggestions'][] = 'page__type__'.$contentType;
				}elseif(isset($node['#bundle'])){
					$contentType=$node['#bundle'];
					$vars['theme_hook_suggestions'][] = 'page__type__'.$contentType;
				}
			}			
		}	
	}			
}

function themex_preprocess_region(&$vars){	
	// if($vars['region']=='left_column'){				
	// 	$vars['content']=$vars['content'].=$content;
	// }	
}

function themex_preprocess_field(&$vars){
	

	$fieldName=$vars['element']['#field_name'];

	if($fieldName == 'field_valuation'){	
		$vars['items']['0']['#markup'] = '$'.nice_number($vars['items']['0']['#markup']);	
	}
	
	if($fieldName == 'field_cap_rate' || $fieldName=='field_site_utilisation'){	
		$vars['items']['0']['#markup'] = $vars['items']['0']['#markup'].'%';	
	}
	
	if($fieldName == 'field_land_area'){		
		$vars['items']['0']['#markup'] = number_format($vars['items']['0']['#markup']).'<span class="lowercase">m</span><sup>2</sup>';		
	}
	
	if($fieldName == 'field_city'){
		$suburb=$vars['element']['#object']->field_suburb;
		if(!empty($suburb)){
			$suburb=$suburb['und'][0]['value'];
			$vars['items'][0]['#markup']=$suburb.', '.$vars['items'][0]['#markup'];
		}
	}
	
	if($fieldName == 'field_images'){	
		if($vars['element']['#view_mode']=='teaser'){
			$mapImages=array();
			if(isset($vars['element']['#object']->field_map_images)){
				$mapImages=$vars['element']['#object']->field_map_images;
			}		
			if(!empty($mapImages)){
				foreach ($vars['items'] as $key => &$image) {
					if(isset($mapImages['und'][$key])){
						$image['#item']['filename']=$mapImages['und'][$key]['filename'];
						$image['#item']['uri']=$mapImages['und'][$key]['uri'];
					} else unset($image);
				}
			}	
		}
		$images=&$vars['items'];
		foreach ($images as $key => &$image) {
			$image['#item']['attributes']=array(
				'title'=>$vars['element']['#object']->title,
				'alt'=>$vars['element']['#object']->title,
			);
		}
	}
}

function themex_theme() {
	return array(
		'contact_site_form' => array(
			'render element' => 'form',
			'template' => 'contact-us',
			'path' => drupal_get_path('theme', 'themex').'/templates',
		),
	);
}

function themex_preprocess_html(&$vars) {
	
	if ( isset($_GET['ajax']) && $_GET['ajax'] == 'true' ) {
       $vars['theme_hook_suggestions'][] = 'html__type__ajaxcontent';
  }
	else if(isset($_GET['pdf-view']) && $_GET['pdf-view'] ==true){
		$vars['theme_hook_suggestions'][] = 'html__type__pdf_view';
	}
	else{
	
		drupal_add_css(drupal_get_path('theme', 'themex') . '/css/ie.css', array(
	    'group' => CSS_THEME,
	    'browsers' => array(
	      'IE' => 'IE',
	      '!IE' => FALSE
	      ),
	    'preprocess' => FALSE
	  ));

	  drupal_add_css(drupal_get_path('theme', 'themex') . '/css/ie8.css', array(
	    'group' => CSS_THEME,
	    'browsers' => array(
	      'IE' => 'IE 8',
	      '!IE' => FALSE
	      ),
	    'preprocess' => FALSE
	  ));

		drupal_add_css(drupal_get_path('theme', 'themex') . '/css/ie7.css', array(
	    'group' => CSS_THEME,
	    'browsers' => array(
	      'IE' => 'IE 7',
	      '!IE' => FALSE
	      ),
	    'preprocess' => FALSE));
		
		}

}

function themex_status_messages($variables) {
	
	if(isset($variables['display'])){
		$display = $variables['display'];
	}else{
		$display=array();
	}
  
  $status_heading = array(
    'status' => t('Success'), 
    'error' => t('Error'), 
    'warning' => t('Warning'),
  );
	$output = '<div class="messages">';
	$messages=drupal_get_messages($display);
	if(count($messages)==0){return '';}
  foreach ($messages as $type => $messages) {
    $output .= "<div class=\"message $type\">\n";
    if (!empty($status_heading[$type])) {
      // $output .= '<span class="status-heading">' . $status_heading[$type] . "</span>\n";
    }
    if (count($messages) > 1) {
		
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];			
    }
    $output .= "</div>\n";
  }
	$output .= "</div>\n";
	$output=str_replace(array('<pre>','</pre>'),array('<p>','</p>'),$output);
  return $output;
}

//end Main

//Navigation =>

function themex_preprocess_search_block_form(&$form) { 
	//$form['search_form']=str_replace('value="Search"','value="Go"',$form['search_form']);
}

function themex_menu_link__menu_supa_kids_menu(&$vars){	
	$classes=join(' ',$vars['element']['#attributes']['class']);
	$link_class='';
	$submenu='';	
	if($vars['element']['#title']=='Supa Kids Events'){
		
		$classes.=' sk-events';
		if(strstr($_GET['q'],'supakids')){
			$link_class.=' active';
		}
		
		$query = new EntityFieldQuery();
		$query
		  ->entityCondition('entity_type', 'node', '=')
		  ->propertyCondition('status', 1, '=')
		  ->propertyCondition('type', 'supa_kids_events')
		  ->propertyOrderBy('created', 'DESC')
		  ->range(0,10);
		;
		$result = $query->execute();
		
		if(count($result)>0){
			$submenu.='<ul>';
			foreach ($result['node'] as $key => $n) {		
				$node=node_load($n->nid);
				$alias=$node->title;		

				$submenu.="<li><a href=\"/supakids/supa-kids-events/$alias\">$node->title</a></li>";	
			}
			$submenu.='</ul>';
		}	
	}
	if(!empty($link_class)){
		$link="<li class='$classes'>".l(t($vars['element']['#title']),'javascript:void(0)', array('attributes' => array('class' => array($link_class))));
	}else{
		$link="<li class='$classes'>".l(t($vars['element']['#title']),$vars['element']['#original_link']['href']);
	}	
	$link.=$submenu;
	$link.='</li>';
	return $link;
}

function themex_menu_link__main_menu(&$vars){	
	$link='';
	$classes=join(' ',$vars['element']['#attributes']['class']);
	$url_paths=explode('/',$_SERVER['REQUEST_URI']);
	
	// if('proyectos'==$vars['element']['#original_link']['link_path']){
	// 	$projects=array();
	// 	$query = new EntityFieldQuery();
	// 	$firstProject =$query
	// 	->entityCondition('entity_type', 'node', '=')
	// 	->propertyCondition('status', 1, '=')
	// 	->propertyCondition('type', array('proyectos'))
	// 	->fieldOrderBy('field_date', 'value', 'DESC')
	// 	->range(0,1)
	// 	->execute();
	// 	$firstProject=array_keys($firstProject['node']);
	// 	!isset($_SESSION['language']) ? $language='es':$language=$_SESSION['language'];		
	// 	$firstProjectUrl=drupal_get_path_alias('node/'.$firstProject[0],$language);		
	// 	$link="<li class='$classes'>".l(t($vars['element']['#title']),$firstProjectUrl).'</li>';
	// }
	// else{
	$link="<li class='$classes'>".l(t($vars['element']['#title']),$vars['element']['#original_link']['href']).'</li>';
	//}
	return $link;
}

function themex_breadcrumb($variables) {	
	if(current(explode('/',$_GET['q']))=='user') return;
	global $base_url;
	$crumbs='';	
	$crumbs = '<ul class="breadcrumbs">';		
	$crumbs.="<li class='link'><a href='{$base_url}'>Home</a></li><li class='separator'>&gt;</li>";	
	$exceptions=array('');$corrections=array('');
	$alias=drupal_get_path_alias($_GET['q']);
	$fragments=explode('/',$alias);	
	foreach ($fragments as $key => $fragment) {
		$path='';
		foreach ($fragments as $key => $_fragment) {
			$path.='/'.$_fragment;
			if($_fragment==$fragment) break;
		}
		$path=ltrim($path,'/');		
		$nodePath=drupal_get_normal_path($path);
		if($nodePath!=$path){
			$isLink=true;
			$nid=str_replace('node/','',$nodePath);
			$title=current(db_query("SELECT title FROM node WHERE nid={$nid}")->fetch());
			
		}else{
			$isLink=false;
			$title=ucWords(str_replace(array('-',strstr($path,'?')), array(' ',''),$path));
			$exceptions[]=$title;
		}		
		if(isset($corrections[$title])) $title=$corrections[$title];	
		!$isLink ? $crumbs.="<li class='link'>$title</li>" : $crumbs.="<li class='link'><a href='".$base_url.'/'.$path."'>$title</a></li>";
		if($key+1 != count($fragments)) $crumbs.="<li class='separator'>&gt;</li>";		
	}	
	$crumbs .= '</ul>';
  return $crumbs;

}

function themex_pager($variables) {
  $tags = $variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];	
  $quantity = $variables['quantity'];
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first = '<span title="first">'.theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t('')), 'element' => $element, 'parameters' => $parameters)).'</span>';
  $li_previous = '<span title="previous">'.theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters)).'</span>';
  $li_next = '<span title="next">'.theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters)).'</span>';
  $li_last = '<span title="last">'.theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t('')), 'element' => $element, 'parameters' => $parameters)).'</span>';

  if ($pager_total[$element] > 1) {
    if ($li_first) {
      $items[] = array(
        'class' => array('pager-first'), 
        'data' => $li_first,
      );
    }
    if ($li_previous) {
      $items[] = array(
        'class' => array('pager-previous'), 
        'data' => $li_previous,
      );
    }

    // When there is more than one page, create the pager list.
    if ($i != $pager_max) {
      if ($i > 1) {
        $items[] = array(
          'class' => array('pager-ellipsis'), 
          'data' => '…',
        );
      }
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current) {
          $items[] = array(
            'class' => array('pager-item'), 
            'data' => theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('pager-current'), 
            'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array('pager-item'), 
            'data' => theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
				//dpm($items);
				
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => array('pager-ellipsis'), 
          'data' => '…',
        );
      }
    }
    // End generation.
    if ($li_next) {
      $items[] = array(
        'class' => array('pager-next'), 
        'data' => $li_next,
      );
    }
    if ($li_last) {
      $items[] = array(
        'class' => array('pager-last'), 
        'data' => $li_last,
      );
    }

    return '<h2 class="element-invisible">' . t('Pages') . '</h2>' . theme('item_list', array(
      'items' => $items, 
      'attributes' => array('class' => array('pager')),
    ));
  }
}

//end Navigation

//Forms =>

function themex_button($variables) {
	$element = $variables['element'];
	$element['#attributes']['type'] = 'submit';
	element_set_attributes($element, array('id', 'name', 'value'));
	$element['#attributes']['class'][] = 'form-' . $element['#button_type'];
	if (!empty($element['#attributes']['disabled'])) {
	$element['#attributes']['class'][] = 'form-button-disabled';
	}

	return '<div class="btn"><span></span><input' . drupal_attributes($element['#attributes']) . ' /></div>';
}

function themex_search_api_page_results(&$variables){
	
	$output='';
	
	if (!empty($variables['results']['results'])) {
	   $variables['items'] = $variables['index']->loadItems(array_keys($variables['results']['results']));
	}
	$url_paths=explode('/', $_SERVER['REQUEST_URI']);
	$searchTerms=$url_paths[count($url_paths)-1];
	$searchTerms=str_replace(array(strstr($searchTerms,'?'),'%20'),array('',' '),urldecode($searchTerms));
	
	global $base_url;
	if(strstr(strtolower($variables['keys']),'compendium')) $variables['results']['result count']++;
	$results=$variables['results']['result count'];
	
	
	
	if(count($variables['items'])>0) $output.='<ul class="search-results">';
	if(strstr(strtolower($variables['keys']),'compendium')){
		$output.='
		<li class="search-result">
			<h2>
				<a href="/my-compendium" >My <span class="searched-term">Compendium<span></a>
			</h2>
			<a href="/my-compendium" class="link">View Page</a>
		</li>';
	}
	foreach ($variables['items'] as $key => $node) {
		
		//processTableField($node);
		
		global $user;
		
		
		
		if($node->status==0 && $user->uid==0){$results--;continue;}
							
			$machine_name=strtolower(str_replace(' ','-', $node->title));			
			$link = $base_url.'/'.drupal_get_path_alias('node/'.$node->nid);
			$link_paths=explode('/', $link);	
			
			$body='';
			
			$countFields=0;
			
			foreach ($variables['index']->options['fields'] as $key => $field){
				if(strstr($key,':value')){
					$field=str_replace(':value','',$key);
					if(isset($node->$field)){
						$field=$node->$field;
						if(isset($field['und'])) if(!strstr($field['und'][0]['safe_value'],$searchTerms)) continue;
					}		
					$countFields++;
				} 			
			} 
			
			if($countFields==0) $countFields=1;
			
			
			
			foreach ($variables['index']->options['fields'] as $key => $field) {
				if(strstr($key,':value')){
					$field=str_replace(':value','',$key);					
					if(isset($node->$field)){
						$field=$node->$field;
						if(isset($field['und'])){			
							//if(!strstr($field['und'][0]['safe_value'],$searchTerms)) continue;			
							$_body='';
							$_body=$field['und'][0]['safe_value'];
							$_body=str_replace(array('<br>', '<br/>'), array(' ',' '),$_body);																	
							$_body=strip_tags($_body);
							$_body=preg_replace('/\[table\](.*)\[\/table\]/','',$_body);	
							$body.=extract_search_values($searchTerms,$_body,400);				
						}					
					}									
				}
			}				
			if($node->type=='highlights') $link='/highlights';
			$section='';
			$output.='<li class="search-result">';
			//if(empty($body)){continue;$results--;}						
			$output.=$section;	
			$output.="<h2><a href=\"$link\">".ucFirst(str_ireplace($searchTerms,'<span class="searched-term">'.$searchTerms.'</span>', $node->title)).'</a></h2>';
			$output.="<div class='body'>$body</div>";
			$output.="<a href=\"$link\" class='link'>View Page</a>";							
			$output.='</li>';			
			//break;
			
	}	
	
	
	if(count($variables['items'])>0) $output.='</ul>';
	
	$searchResults="
	<div class='searched'>Search results for \"$searchTerms\"</div>
	<div class='search-result-counter'>Your search returned $results results:</div>
	".$output;
	
	return $searchResults;
}

function extract_search_values($value,$field,$length){	
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
			if($prev> $value[1]){continue;}
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


function themex_preprocess_contact_site_form(&$vars){
	$vars['form']['name']['#prefix']='<div class="form-errors"></div>';
	$vars['form']['name']['#title']='Full Name:';
	$vars['form']['mail']['#title']='Email:';
	$vars['form']['message']['#title']='Your Message:';
	$vars['form']['message']['#suffix']='<div class="bottom-note">We will endeavour to reply to your query within 48 business hours.</div>';
	$vars['form']['subject']['#value'] = 'Contact Enquiry';
	$vars['form']['copy']['#access']= FALSE;
	$vars['form']['actions']['submit']['#value']='SUBMIT';	
	$vars['contact'] = drupal_render_children($vars['form']);		
	//dpm($vars);
	//honeypot_add_form_protection($vars['form'], $form_state, array('honeypot', 'time_restriction'));
}

function themex_textarea($variables) {
  $element = $variables['element'];
  $element['#attributes']['name'] = $element['#name'];
  $element['#attributes']['id'] = $element['#id'];
  $element['#attributes']['cols'] = $element['#cols'];
  $element['#attributes']['rows'] = $element['#rows'];
  _form_set_class($element, array('form-textarea'));
  $wrapper_attributes = array(
    'class' => array('form-textarea-wrapper')
  );
  // Add resizable behavior.
  // if (!empty($element['#resizable'])) {
  //   $wrapper_attributes['class'][] = 'resizable';
  // } 
  $output = '<div' . drupal_attributes($wrapper_attributes) . '>';
  $output .= '<textarea' . drupal_attributes($element['#attributes']) . '>' . check_plain($element['#value']) . '</textarea>';
  $output .= '</div>';
  return $output;
}

//end Forms

function bb2html($text) {
  $bbcode = array(
                  "[strong]", "[/strong]",
                  "[b]",  "[/b]",
                  "[u]",  "[/u]",
                  "[i]",  "[/i]",
                  "[em]", "[/em]",
									"[span]","[/span]"
                );
  $htmlcode = array(
                "<strong>", "</strong>",
                "<strong>", "</strong>",
                "<u>", "</u>",
                "<em>", "</em>",
                "<em>", "</em>",
								"<span>", "</span>"
              );
  return str_replace($bbcode, $htmlcode, $text);
}


function addProperties(){
	global $themePath;
	
	$csvPath=$themePath.'/files/properties_final.csv';
	
	$formattedArr = array();
	$filename = $csvPath;
	if (($handle = fopen($filename, "r")) !== FALSE) {
	    $key = 0;    // Set the array key.
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	        $count = count($data);  //get the total keys in row
	        //insert data to our array
	        for ($i=0; $i < $count; $i++) {
	            $formattedArr[$key][$i] = $data[$i];
	        }
	        $key++;
	    }
	    fclose($handle);    //close file handle
	}	
	$keys=array_shift($formattedArr);
	$properties=array();
	foreach ($formattedArr as $key => $values) {
		$city=$values[0];
		foreach ($values as $_key => $value) {		
			$properties[$values[0]][$keys[$_key]]=str_replace(array('%',','),array(''),$value);
		}	
	}


	
	require_once($themePath.'/framework/app/XML.php');
	$locations=XML::getArray($themePath.'/files/locations.xml');
	$_locations=array();
	XML::formatArray($locations,$_locations);
	
	foreach ($_locations as $key => $location) {
		$coordinates=explode(',',$location['point']['coordinates']);
		$properties[trim($location['name'])]['field_latitude']=$coordinates[1];
		$properties[trim($location['name'])]['field_longitude']=$coordinates[0];
	}
	
	foreach ($properties as $key => &$property) {
		unset($property['']);
		$city=explode(',',$property['field_city']);
		$property['field_city']=$city[0];
	}
	
	
	foreach ($properties as $key => $property) {
		$node = new stdClass();
	  $node->type = 'property';
	  node_object_prepare($node);
		$node->title    = $property['title_field'];
		$node->language = LANGUAGE_NONE;
		
		foreach ($property as $_key => $value) {
			//$node->$_key=array();
			if($_key=='field_city'){
				$term=taxonomy_get_term_by_name($value,'cities');
				
				if(!empty($term)){
					$term=current($term);
					$node->field_city[$node->language][0]['tid']=(string)$term->tid;
					$node->field_city[$node->language][0]['taxonomy_term']=$term;
				}

			}
			else if($_key=='field_retail_liquour_outlet'){
				$term=taxonomy_get_term_by_name($value,'retail_liquour_outlets');			
				if(!empty($term)){
					$term=current($term);
					$node->field_retail_liquour_outlet[$node->language][0]['tid']=(string)$term->tid;
					$node->field_retail_liquour_outlet[$node->language][0]['taxonomy_term']=$term;
				}
			}			
			else{
				eval('$node->'.$_key.'[$node->language][0]["value"]=$value;');
			}		
		}	

		$imagePath='/Volumes/Data/Jobs/ALE/www/sites/all/themes/themex/files/state_images/';
		$filename=$imagePath.strtolower(str_replace(array(' ','/'),array('-','_'),$property['title_field'])).'.jpg';		
		if(!is_file($filename)){
			$state_query="SELECT data.name FROM taxonomy_term_hierarchy as h INNER JOIN taxonomy_term_data as data on h.parent=data.tid WHERE h.tid={$node->field_city[$node->language][0]['tid']}";
			$state=current(db_query($state_query)->fetch());
			$stateString='-'.strtolower(str_replace(array(' '),array('-'),$state));
			$filename=$imagePath.strtolower(str_replace(array(' ','/'),array('-','_'),$property['title_field'])).$stateString.'.jpg';
		}
		if(is_file($filename)){
			$file_path = drupal_realpath($filename); // Create a File object
			$file = (object) array(
			  'uid' => 1,
			  'uri' => $file_path,
			  'filemime' => file_get_mimetype($file_path),
			  'status' => 1,
			); 		
			$file = file_copy($file, 'public://property images'); // Save the file to the root of the files directory. You can specify a 
			$node->field_images[LANGUAGE_NONE][0] = (array)$file;
		}
		
		node_save($node);
		compendium_node_update($node);
		//break;
	}
	//die();
	
	
}

function generate_pdfs(){
	// $query="SELECT nid FROM node WHERE type='property' ORDER BY nid";
	// $results=db_query($query)->fetchAll();
	// $nodes=array();
	// foreach ($results as $key => $result) {
	// 	$node=node_load($result->nid);
	// 	$nodes[]=$node;
	// 	//compendium_node_update($node);
	// }
	// 
	// $init=60;
	// $batch=30;
	// for ($i=$init; $i <$init+$batch ; $i++) { 
	// 	if(isset($nodes[$i])) compendium_node_update($nodes[$i]);
	// }
	
	$query="SELECT nid FROM node WHERE type='property_information' ORDER BY nid";
	$results=db_query($query)->fetchAll();
	$nodes=array();
	foreach ($results as $key => $result) {
		$node=node_load($result->nid);
		$nodes[]=$node;
	}
	
	$init=0;
	$batch=6;
	for ($i=$init; $i <$init+$batch ; $i++) { 
		if(isset($nodes[$i])) compendium_node_update($nodes[$i]);
	}
	
	
	
}

function f_parse_csv($file, $longest, $delimiter) {
  $mdarray = array();
  $file    = fopen($file, "r");
  while ($line = fgetcsv($file, $longest, $delimiter)) {
    array_push($mdarray, $line);
  }
  fclose($file);
  return $mdarray;
}
