<?php
//require_once APPPATH.'third_party\\ZipArchiver.class.php';
class Zipp{
	public function create($baserootpath,$zipPath){
		//$zipper = new ZipArchiver;
		$dirPath = $baserootpath;
		$zipPath = $zipPath;
		$zip = $this->zipDir($dirPath, $zipPath);
		$currdate = date("Y-m-d H:i:s");
		if($zip){
		    return  array("status" => "success","msg" =>'ZIP archive created successfully.',"currdate" => $currdate);
		}else{
		    return  array("status" => "success","msg" =>'Failed to create ZIP.',"currdate" => $currdate);
		}
	}
	public static function zipDir($sourcePath, $outZipPath){
        $pathInfo = pathinfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];
    
        $z = new ZipArchive();
        $z->open($outZipPath, ZipArchive::CREATE);
        $z->addEmptyDir($dirName);
        if($sourcePath == $dirName){
            self::dirToZip($sourcePath, $z, 0);
        }else{
            self::dirToZip($sourcePath, $z, strlen("$parentPath/"));
        }
        $z->close();
        
        return true;
    }
     private static function dirToZip($folder, &$zipFile, $exclusiveLength){
        $handle = opendir($folder);
        while(FALSE !== $f = readdir($handle)){
            // Check for local/parent path or zipping file itself and skip
            if($f != '.' && $f != '..' && $f != basename(__FILE__)){
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip
                $localPath = substr($filePath, $exclusiveLength);
                if(is_file($filePath)){
                    $zipFile->addFile($filePath, $localPath);
                }elseif(is_dir($filePath)){
                    // Add sub-directory
                    $zipFile->addEmptyDir($localPath);
                    self::dirToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }
}