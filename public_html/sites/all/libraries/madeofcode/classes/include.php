<?php
//CONSTANTS =>
define('DS', DIRECTORY_SEPARATOR);

//end
Class __File{
	private static $fileArray;	
	private static $frontQueue;
	private static $backQueue;
	public static function getFiles($pattern='',$inclusions='',$exclusions='',$recursive=true,$frontQueue=array(),$backQueue=array(),$exceptions=array(),$init=true,$tokenExceptions=array()){		
		$files=array();		
		if(!is_array($backQueue)) $backQueue=array();
		if(!is_array($frontQueue)) $frontQueue=array();
		if(!is_array($exceptions)) $exceptions=array();	
		
		$extension=str_replace('*','',strstr($pattern,'*'));
		$dir=str_replace(strstr($pattern,'*'),'',$pattern);		
		if(empty($extension)) return false; 
		if($init) foreach ($frontQueue as $key => $frontQueued) $files[]=$frontQueued;
		
		if ($handle = opendir($dir)){
			while (false !== ($file = readdir($handle)) ) {	
				$continue=true;
				foreach ($tokenExceptions as $key => $token) {
					//echo $file;
					if(strstr($file,$token)){
						$continue=false;
					}
				}	
				if(substr($file,0,1)!='.' && $continue ){
					if(is_file($dir.DS.$file)){				
						if($exclusions!='' && substr($file,0,strlen($exclusions))==$exclusions){
							continue;
						}				
						$filePath=$dir.$file;		
						if(in_array($filePath,$frontQueue)||in_array($filePath,$backQueue)||in_array($filePath,$exceptions)){
							continue;
						}
						$extension= array_pop(explode('*.',$pattern)) ;
						$file_extension= array_pop(explode('.',$filePath)) ;
						if($extension==$file_extension){

							$files[]=$filePath;					
						} 					
					}
					elseif(is_dir($dir.DS.$file) && $recursive){
						if(!strstr($extension,'.')) $extension='.'.$extension;			
						$_pattern=$dir.$file.DS.'*'.$extension;			
						$_files=self::getFiles($_pattern,$inclusions,$exclusions,$recursive,$frontQueue,$backQueue,$exceptions,false,$tokenExceptions);
						foreach ($_files as $key => $file) {
							$files[]=$file;
						}
					}			
				}
			}
		}
		if($init) foreach ($backQueue as $key => $backQueued) $files[]=$backQueued;
		return $files;
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
