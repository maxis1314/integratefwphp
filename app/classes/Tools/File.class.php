<?php

class Tools_File
{

    /*
    * author : www.phpernote.com
    * param string $dir 目录名称
    * return array $dirList 查询结果数组
    */

    static function listDir($dir){
        if(!file_exists($dir)||!is_dir($dir)){
            return '';
        }
        $dirList=array('dirNum'=>0,'fileNum'=>0,'lists'=>'');
        $dir=opendir($dir);
        $i=0;
        while($file=readdir($dir)){
            if($file!=='.'&&$file!=='..'){
                $dirList['lists'][$i]['name']=$file;
                if(is_dir($file)){
                    $dirList['lists'][$i]['isDir']=true;
                    $dirList['dirNum']++;
                }else{
                    $dirList['lists'][$i]['isDir']=false;
                    $dirList['fileNum']++;
                }
                $i++;
            };
        };
        closedir($dir);
        return $dirList;
    }


}
 