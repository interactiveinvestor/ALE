<?php

Class _DM{
	
	private $recursiveMenuData;
	private static $menuData=null;
	
	public function __construct(){
		
	}
	
	public static function urlEncode($val){
		$_val = strtolower(ltrim($val));
		$_val=str_replace(array(' ','.','º','--','with'),array('-','','','-',''),$_val);				
		$_val=str_replace(array('--'),array('-'),$_val);				
		return trim($_val);
	}
	
	public static function machine_name($human_name) {
	  return strtolower(preg_replace(array(
	    '/[^a-zA-Z0-9]+/',
	    '/-+/',
	    '/^-+/',
	    '/-+$/',
	  ), array('-', '-', '', ''), $human_name));
	}
	
	public static function recursiveMenu($data,&$output){
		
		$output.='<ul>';
		$count=0;
		global $base_url;
		global $base_path;		
		$dataCount=count($data);
		foreach ($data as $key => $value) {		
			$icon=false;
			if(isset($value['link']['options']['menu_icon'])){			
				$imageData=$value['link']['options']['menu_icon'];
				$imageUrl=image_style_url($imageData['image_style'], $imageData['path']);	
				$file=$_SERVER['DOCUMENT_ROOT'].str_replace(array($base_url),array(''),$imageUrl);
				$file=substr($file,0,strpos($file,'?itok='));
				if(file_exists($file)) $icon=true;
			}
			
			
			$machine_name=self::machine_name($value['link']['link_title']);		
				
			if($value['link']['hidden']==1){
				$dataCount--;
				continue;
			}
			$count++;
			
			$href=drupal_get_path_alias($value['link']['link_path']);
			if(!strstr($href,'http')){
				$href='/'.$href;
			}
					
			if($value['link']['link_path']=='#'){
				$href='JavaScript:void(0)';
			}	
			
			$output.='<li';
			$classes='menu-'.$machine_name.' ';		
			if(strstr(urlDecode($_SERVER['REQUEST_URI']),$href)){
				$classes.='nav-selected arrow ';
			} 
			if($count==1){$classes.=' first';}
			if($count==$dataCount){$classes.=' last';}
			if(!empty($classes)){$output.=" class='".trim($classes)."' ";}
			$output.='>';
			$href=str_replace(array('/<front>',$base_url.'/<nolink>',$base_url),array($base_url,'JavaScript:void(0)',''),$href);
			
			
			$_attributes='';
			if(isset($value['link']['localized_options']) && isset($value['link']['localized_options']['attributes'])){
				
				$attributes=$value['link']['localized_options']['attributes'];
				if(isset($attributes['target'])){
					$_attributes.=' target="'.$attributes['target'].'" ';
				}
			}
			
			if(!strstr($href,'http://')){
				$href=$base_url.$href;
			}
			
			if(strstr($href,'<nolink>')) $href='JavaScript:void(0)';
			
			$output.="<a {$_attributes} href='".$href."'>";
			if($icon){
				
				$output.="<div class='icon-holder'><img src='$imageUrl' alt='menu-icon' /></div>";
			}
			if($icon) $output.='<p>';
			$output.=$value['link']['link_title'];
			
			if($icon) $output.='</p>';
			$output.='</a>';
			if(isset($value['link']['after'])){
				$output.=$value['link']['after'];
			}
			if(!empty($value['below'])){
				self::recursiveMenu($value['below'],$output);
			}
			$output.='</li>';		
		}
		$output.='</ul>';		
	}
	
	public static function getProjects(){
		global $_language;
		$projects=array();
		$query = new EntityFieldQuery();
		$__projects =$query
		->entityCondition('entity_type', 'node', '=')
		->propertyCondition('status', 1, '=')
		->propertyCondition('type', array('proyectos'))
		->fieldOrderBy('field_date', 'value', 'DESC')
		->execute();
		$_projects=$__projects['node'];
		
		foreach ($_projects as $key => $project) {
			$project=node_load($project->nid);
			$cat=$project->field_categoria;
			$path=$project->field_thumbnail['und'][0]['uri'];
			$path=str_replace('public://','',$path);
			$thumbnail=image_style_url('project-gallery-thumbs', $path);
			if(isset($cat[$_language])){
				$catId=$cat[$_language][0]['tid'];
			}else if(isset($cat['und'])){
				$catId=$cat['und'][0]['tid'];
			}else{
				foreach ($cat as $key => $value) {
					$catId=$value[0]['tid'];
					break;
				}
			}
			$project->thumbnailUrl=$thumbnail;	
			$projects[$catId][]=$project;
		}		
		return $projects;
	}
	
	public static function getProjectCategories(){		
		$categorias=array();
		$query = new EntityFieldQuery;
		$__categorias = $query->entityCondition('entity_type', 'taxonomy_term')->propertyCondition('vid', taxonomy_vocabulary_machine_name_load('projects')->vid)->execute();
		$_categorias=$__categorias['taxonomy_term'];
		foreach ($_categorias as $key => $cat) {
			$term=entity_load('taxonomy_term', array($cat->tid));
			//if($term[$cat->tid]->language==$_language){
				$categorias[]=$term[$cat->tid];
			//}
		}
		return $categorias;
	}
	
	public static function getFirstProject(){
		$query = new EntityFieldQuery();
		$firstProject =$query
		->entityCondition('entity_type', 'node', '=')
		->propertyCondition('status', 1, '=')
		->propertyCondition('type', array('proyectos'))
		->fieldOrderBy('field_date', 'value', 'DESC')
		->range(0,1)
		->execute();
		$firstProject=array_keys($firstProject['node']);
		!isset($_SESSION['language']) ? $language='es':$language=$_SESSION['language'];		
		$firstProjectUrl=drupal_get_path_alias('node/'.$firstProject[0],$language);
	}
	
	public static function vaMenu(){
		global $_language;
		$menuTree=menu_tree_all_data('main-menu');
		$projects=_DM::getProjects();
		$categories=_DM::getProjectCategories();
		
		foreach ($menuTree as $key => $menu) {
			
			if($menu['link']['mlid']==910 || $menu['link']['mlid']==912){
				foreach ($categories as $_key => $cat) {
					$catName='';
					if (isset($cat->name_field[$_language])){
						$catName=$cat->name_field[$_language][0]['value'];
					}			
					$menuTree[$key]['below'][$_key]['link']['link_title']=$catName;
					$menuTree[$key]['below'][$_key]['link']['link_path']='#';
					if(isset($projects[$cat->tid])){
						foreach ($projects[$cat->tid] as $__key => $project) {
							$title_field=$project->title;
							if(isset($project->title_field[$_language])){						
								$title_field=$project->title_field[$_language][0]['value'];
							}			
							$classes='';			
							if(isset($urlPaths[3])){
								$path=str_replace(array('º'),array(''),urldecode($urlPaths[3]));
								if($path==_DM::urlEncode($title_field)) $classes.=" selected arrow ";
							}			
							$menuTree[$key]['below'][$_key]['below'][$__key]['link']['link_title']=$title_field;
							$menuTree[$key]['below'][$_key]['below'][$__key]['link']['link_path']=drupal_get_path_alias('node/'.$project->nid);
							$menuTree[$key]['below'][$_key]['below'][$__key]['link']['after']="<div class='thumb'><img src='$project->thumbnailUrl' alt=''/></div>";
							
							
						}
					}			
				}		
			}	
		}
		$menu='';		
		self::recursiveMenu($menuTree,$menu);
		
		return $menu;	
	}
}
