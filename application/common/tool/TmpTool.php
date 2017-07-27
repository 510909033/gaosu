<?php
namespace app\common\tool;

class TmpTool {
    
    public static function arrayToArrayFile($array,$filename_subfix=''){
        if (is_array($array)){
            $save_text = '<?php '.PHP_EOL.'$arr='.var_export($array,true) .';';
        }else if (is_string($array)){
            $save_text = $array;
        }else if (is_object($array)){
            $save_text = print_r($array,true);
        }
        
        
        $dir = ROOT_PATH.'public/tmp_tool/arrayToArrayFile/'.date('Ymd/');
        if (!is_dir($dir)){
            mkdir($dir,755,true);
        }
        
        if ($filename_subfix){
            $filename_subfix = str_replace(array(':','/','\\'), '--', $filename_subfix);
        }
        $filename =$dir.date('YmdHis').'_'.rand(1,1000000).'.tmp.'.$filename_subfix.'.txt';
        
        
        @file_put_contents($filename, $save_text);
    }
    
    
}