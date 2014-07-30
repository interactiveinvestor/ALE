<?php
Class File{
	private static $fileArray;	
	private static $frontQueue;
	private static $backQueue;
	public static function getFiles($pattern='',$inclusions='',$exclusions='',$recursive=true,$frontQueue=array(),$backQueue=array(),$exceptions=array(),$init=true){
		if(!is_array($backQueue)) $backQueue=array();
		if(!is_array($frontQueue)) $frontQueue=array();
		if(!is_array($exceptions)) $exceptions=array();
		$extension=str_replace('*','',strstr($pattern,'*'));
		$dir=str_replace(strstr($pattern,'*'),'',$pattern);
		//echo $extension;
		if(empty($extension)){return false;} 		
		if($init==true){
			self::$fileArray=array();			
			if(is_array($frontQueue)){
				foreach ($frontQueue as $key => $frontQueued) {
					self::$fileArray[]=str_replace($_SERVER['DOCUMENT_ROOT'],'',$frontQueued);
				}
			}
		}		
		
		if ($handle = opendir($dir)){
			while (false !== ($file = readdir($handle)) ) {
				if($file != "." && $file != ".." && substr($file,0,1)!='.'){
					
					if(is_file($dir."/".$file) == true){
						//echo $file.'<br>';
						$include=true;				
						if($exclusions!=''){
							if(substr($file,0,strlen($exclusions))==$exclusions){
								$include=false;
							}
						}					
						if($exclusions!=''){
							if(substr($file,0,strlen($inclusions))!=$inclusions){
								$include=false;
							}
						}
						$filePath=str_replace($_SERVER['DOCUMENT_ROOT'],'',$dir).$file;
						if(in_array($filePath,$frontQueue)||in_array($filePath,$backQueue)){
							$include=false;
						}							
						if($include!==false){
							self::$fileArray[]=$filePath;
						}						
					}elseif($recursive==true){					
						self::getFiles($dir.$file.'/*'.$extension,$inclusions,$exclusions,$recursive,$frontQueue,$backQueue,$exceptions,false);
					}			
				}										
			}
			closedir($handle);
		}			
		if($init==true){
			foreach ($backQueue as $key => $backQueued) {
				self::$fileArray[]=$backQueued;
			}
			return self::$fileArray;
		}
	}	
	
	public static function copyToPublic($file){
		
		
	}
	public static function reorderFiles($dir,$file_name,$extension,$old,$new,$max_files=null){	
		$a=$old;
		if($max_files==100){
			if(strlen($a)==1){$a='0'.$a;}
		}

		 rename($dir.$file_name.$a.$extension,$dir."temp_name".$extension);

			for($b=$old+1;$b<=$new;$b++){
				$c=$b-1;
				$d=$b;
				if($max_files==100){
					if(strlen($d)==1){$d='0'.$d;}
					if(strlen($c)==1){$c='0'.$c;}
				}
				rename($dir.$file_name.$d.$extension,$dir.$file_name.$c.$extension);
			}
			$count=0;

			for($b=$new;$b<$old;$b++){
				$count++;
				$c=$old-$count +1;
				$d=$old-$count;
				if($max_files==100){
					if(strlen($d)==1){$d='0'.$d;}
					if(strlen($c)==1){$c='0'.$c;}
				}

				rename($dir.$file_name.$d.$extension,$dir.$file_name.$c.$extension);

			}
			if($max_files==100){
				if(strlen($old)==1){$old='0'.$old;}
				if(strlen($new)==1){$new='0'.$new;}
			}
			rename($dir."temp_name.jpg",$dir.$file_name.$new.$extension);

	}
	public static function enhancedCopy($source,$dest){	
		$pathArray=explode('/',$dest);
		unset($pathArray[count($pathArray)-1]);		
		$path=join('/',$pathArray);		
		mkdir($path,0777,true);		
		copy($source,$dest);		
	}
	
	public static function formatSizeUnits($bytes)
	    {
	        if ($bytes >= 1073741824)
	        {
	            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
	        }
	        elseif ($bytes >= 1048576)
	        {
	            $bytes = number_format($bytes / 1048576, 2) . ' MB';
	        }
	        elseif ($bytes >= 1024)
	        {
	            $bytes = number_format($bytes / 1024, 2) . ' KB';
	        }
	        elseif ($bytes > 1)
	        {
	            $bytes = $bytes . ' bytes';
	        }
	        elseif ($bytes == 1)
	        {
	            $bytes = $bytes . ' byte';
	        }
	        else
	        {
	            $bytes = '0 bytes';
	        }

	        return $bytes;
	}
}

