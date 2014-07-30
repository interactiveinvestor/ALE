<?php

function shuffle_assoc(&$array) {
    $keys = array_keys($array);
    shuffle($keys);
    foreach($keys as $key) {
        $new[$key] = $array[$key];
    }
    $array = $new;
    return true;
}

Class Blog{
	
	public $posts=array();
	public $contentType;
	public $displayedRecords;
	public $displayedIndexes;
	public $totalRecords;
	public $archive;
	public $tagTable='field_data_field_tags';
	public $tagName=null;
	public $tagFilterHtml='';
	public $rawUrl='';
	public $secondaryContent='';
	public $content='';
	public $nodeIds=array();
	public $startIndex=0;
	public $displayType;
	public $offset;
	public $options=array();
	
	public function __construct($userOptions){
		
		$this->getOptions($userOptions);
		
		if(!is_null($this->options['vocabId'])){
			$this->categories=$this->getCategories();
		}
		
		// $contentType,$url=null, $displayedRecords=null,$displayedIndexes=null,$get=false,$displayType='teaser',$offset=0
		// 
		// if($url!=null){
		// 	$this->rawUrl='/'.$url;
		// }else{
		// 	$this->rawUrl=str_replace('?'.$_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']);
		// }
		// $this->offset=$offset;
		// $this->displayType=$displayType;
		// $this->contentType=$contentType;
		// $this->displayedIndexes=$displayedIndexes;
		// $this->displayedRecords=$displayedRecords;
		// if($get){
		// 	$this->getPosts();
		// }
		
	}
	
	private function getOptions($userOptions){
		
		$defaultOptions=array(
			'contentType'=>null,
			'baseUrl'=>null,
			'displayedRecords'=>null,
			'displayedIndexes'=>null,
			'getPosts'=>false,
			'displayType'=>'teaser',
			'offset'=>0,
			'vocabId'=>null
		);
		
		foreach ($defaultOptions as $key => $option) {
			if(isset($userOptions[$key])){
				$this->options[$key]=$userOptions[$key];
			}else{
				$this->options[$key]=$defaultOptions[$key];
			}		
		}
		
	}
	
	private function getCategories(){
		$categories=taxonomy_get_tree($this->options['vocabId']);
		
		$categoryMenu='<ul class="blog-categories">';
		foreach ($categories as $key => $cat) {
			$categoryMenu.="<li><a href='/".$this->options['baseUrl']."/".machineName($cat->name)."'>$cat->name</a></li>";
		}
		$categoryMenu.='</ul>';
		$this->categoryMenu=$categoryMenu;
	}
	
	public function getPosts(){
		
		
		$startRecord=get_start_record($this->displayedRecords);
		$this->startIndex=$startRecord-1 + $this->offset;
		
		
		if(isset($_GET['search_value'])){
			$search_value=$_GET['search_value'];
			
			if(strlen($search_value)<3 || empty($search_value)){
				$this->secondaryContent="<div class='body-note alert'>Searches smaller than 2 letters are not allowed. Please try searching for a longer word.</div>";
				$this->getDefaultPosts();
			}else{
				$hasSearched=true;
				$query=	"SELECT COUNT(*), {node}.nid FROM {node} ";
				$query.=" LEFT JOIN {field_data_body} ON ( {node}.nid= {field_data_body}.entity_id) ";
				$query.=" WHERE {field_data_body}.bundle='{$this->contentType}' AND {node}.status=1 ";		
				$query.="AND ({node}.title LIKE '%{$search_value}%' OR {field_data_body}.body_value LIKE '%{$search_value} %')";
				$searchCount=db_query($query)->fetchAll();
				$this->totalRecords=current($searchCount[0]);
				
				$query=	"SELECT  {node}.nid FROM {node} ";
				$query.=" LEFT JOIN {field_data_body} ON ( {node}.nid= {field_data_body}.entity_id) ";
				$query.=" WHERE {field_data_body}.bundle='{$this->contentType}' AND {node}.status=1 ";		
				$query.="AND ({node}.title LIKE '%{$search_value}%' OR {field_data_body}.body_value LIKE '%{$search_value}%') ";
				$query.=" LIMIT {$this->displayedRecords} OFFSET {$this->startIndex}";
				$results=db_query($query)->fetchAll();
				
				foreach ($results as $key => $result) $this->nodeIds[]=$result->nid;	
				
				if(empty($this->nodeIds)){
					$this->secondaryContent="<div class='body-note alert'>We apologise. Your search did not yield any results.</div>";
					$this->getDefaultPosts();
				}else{
					$this->secondaryContent="<div class='body-note success'>Your search returned $this->totalRecords results. <a href='$blog->rawUrl'>Reset results.</a></div>";
				}
				
			}
			
		}	
		elseif(isset($_GET['tag']) && is_numeric($_GET['tag'])){
			
			$this->tagName=taxonomy_term_load($_GET['tag'])->name;
			
			$query=	"SELECT COUNT(*),  {$this->tagTable}.entity_id, {node}.status FROM {$this->tagTable} ";
			$query.=" LEFT JOIN {node} ON ( {$this->tagTable}.entity_id= {node}.nid) ";
			$query.=" WHERE {$this->tagTable}.field_tags_tid='{$_GET['tag']}' AND {node}.status=1 ";		
			$tagCount=db_query($query)->fetchAll();	
			$this->totalRecords=current($tagCount[0]);
			
			$query=	"SELECT  {$this->tagTable}.entity_id, {node}.status FROM {$this->tagTable} ";
			$query.=" LEFT JOIN {node} ON ( {$this->tagTable}.entity_id= {node}.nid) ";
			$query.=" WHERE {$this->tagTable}.field_tags_tid='{$_GET['tag']}' AND {node}.status=1 ";
			$query.=" LIMIT {$this->displayedRecords} OFFSET {$this->startIndex}";	
				
			$tagNodes=db_query($query)->fetchAll();	
			foreach ($tagNodes as $key => $tagNode) $this->nodeIds[]=$tagNode->entity_id;
		}
		else{ $this->getDefaultPosts();}

		$posts=array();
		foreach ($this->nodeIds as $key => $nodeId) {	
			$teaser = node_view(node_load($nodeId), $this->displayType);	
			$teaser['links']['node']['#links']['node-readmore']['title']='';
			$post=render($teaser);
			
			if(isset($hasSearched)){
				//$post=extract_values($search_value,$post,2000);
				//$post=str_ireplace($search_value,'<span class="searched">'.$search_value.'</span>',$post);
				$post = preg_replace("!($search_value)!i","<span class='searched'>$1</span>",$post);
				$post = preg_replace("!(<[^>]+?)(<span class='searched'>($search_value)</span>)([^<]+?>)!i","$1$3$4",$post);	
			}		
			$posts[]=$post;	 
		}
		
		$this->posts=$posts;
		
		if(!empty($this->secondaryContent)){
			$this->content.=$this->secondaryContent;
		}
		
		foreach ($this->posts as $key => $post){
			$this->content.=$post;
		}	
		
	}
	
	public function getDefaultPosts(){
		$this->totalRecords=db_query("SELECT COUNT(*) FROM {node} WHERE type = '{$this->contentType}' AND status = 1")->fetchField();
		$query = new EntityFieldQuery();
		$query
		  ->entityCondition('entity_type', 'node', '=')
		  ->propertyCondition('status', 1, '=')
		  ->propertyCondition('type', array($this->contentType))
		  ->propertyOrderBy('created', 'DESC')
		  ->range($this->startIndex,$this->displayedRecords);
		;			
		$result = $query->execute();
		$postNodes=$result['node'];			
		foreach ($postNodes as $key => $postNode) $this->nodeIds[]=$postNode->nid;
		
	}
	
	public function getArchive(){		
		$output='';		
		$posts=db_query("SELECT nid, title, created FROM {node} WHERE type='{$this->contentType}' AND status=1 ORDER BY created DESC")->fetchAll();
		$pastYear=0;
		$pastMonth='';		
		$output.= '<ul class="years">';		
		foreach ($posts as $key => $post) {	
			$currentYear=date('Y',$post->created);
			if(isset($posts[$key+1]->created)){
				$nextYear=date('Y',$posts[$key+1]->created);
				$nextMonth=date('F',$posts[$key+1]->created);
			}	else{
				unset($nextYear);
				unset($nextMonth);
			}	
			$currentMonth=date('F',$post->created);
			
			if($currentYear!=$pastYear){
				$output.= '<li class="year"><a href="JavaScript:void(0)"><span class="icon">&#9658;</span><span class="link-text">'.$currentYear.'</span></a>';
				$output.= '<ul class="months">';
			}		
			
			$classes='';
			if($pastMonth!=$currentMonth){
				$classes=' first';
				$output.= '<li class="month"><a href="JavaScript:void(0)"><span class="icon">&#9658;</span><span class="link-text">'.$currentMonth.'</span></a>';				
				$output.= '<ul class="archive-posts">';
			}	
			
			if((isset($nextMonth)&&$nextMonth!=$currentMonth) || !isset($nextMonth)){
				$classes=' last';
			}	
			
			$output.= '<li class="post'.$classes.'"><a href="/	'.drupal_get_path_alias('node/'.$post->nid).'">'.$post->title.'</a></li>';
			
			if((isset($nextMonth)&&$nextMonth!=$currentMonth) || !isset($nextMonth)){
					$output.= '</ul>';
					$output.= '</li>';
			}
			
			if((isset($nextYear)&&$nextYear!=$currentYear) || !isset($nextYear)){
					$output.= '</ul>';
					$output.= '</li>';
			}
			
			$pastMonth=$currentMonth;		
			$pastYear=$currentYear;		
		}		
		$output.= '</ul>';
		return $output;		
	}
	
	public function getTags($tagTable=null,$limit=30,$maxFont=35, $minFont=12){
		
		if($tagTable==null){
			$tagTable=$this->tagTable;
		}

		$tagNodes=db_query("SELECT field_tags_tid  FROM {$tagTable} WHERE bundle='{$this->contentType}'")->fetchAll();		
		$tags=array();		
		foreach ($tagNodes as $key => $tagNode) $tags[]=$tagNode->field_tags_tid;
		$tagCount=array_count_values($tags);	
		shuffle_assoc($tagCount);		
		
		$count=0;
		foreach ($tagCount as $key => $tag) {
			if($count >= $limit) unset($tagCount[$key]);	
			$count++;
		}
		
		$maxCount=max($tagCount);
		$minCount=min($tagCount);
		$output='';
		
		foreach ($tagCount as $key => $count) {
			$term=taxonomy_term_load($key);
			$fontSize=round(($count*($maxFont-$minFont))/($maxCount)) + $minFont;
			
			$this_page = basename($_SERVER['REQUEST_URI']);
			if (strpos($this_page, "?") !== false) $this_page = reset(explode("?", $this_page));
			$class="";
			if($term->name==$this->tagName){
				$class=" class='selected' ";
			}
			
			$url=$this->rawUrl.'?tag='.$term->tid;		
			$output.="<span class='tag'><a $class style='font-size:{$fontSize}px' href='$url'>{$term->name} <span class='count'>($count)</span></a></span>";
		}
		
		return $output;
		
		
	}
	
	public function getLatest(){
		$query = new EntityFieldQuery();
		$query
		  ->entityCondition('entity_type', 'node', '=')
		  ->propertyCondition('status', 1, '=')
		  ->propertyCondition('type', array($this->contentType))
		  ->propertyOrderBy('created', 'DESC')
		  ->range(1,1);
		;			
		$result = current(current($query->execute()));
		
		$featured = node_view(node_load($result->nid), 'search_result');	
		$featured['links']['node']['#links']['node-readmore']['title']='ff';
		$post=render($featured);
		
		return $post;
	}
	
}

function machineName($human_name) {
  return strtolower(preg_replace(array(
    '/[^a-zA-Z0-9]+/',
    '/-+/',
    '/^-+/',
    '/-+$/',
  ), array('-', '-', '', ''), $human_name));
}

