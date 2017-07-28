<?php

namespace app\common\model;

use think\Model;

class SysLogTmp extends Model
{
    public static function log($title,$content,$user_id,$pos){
        $data = [
            'title'=>$title,
            'content'=>$content,
            'user_id'=>bcadd($user_id, 0,0),
            'create_time'=>time(),
            'pos'=>$pos
            
        ];
        return self::create($data);
    }
}
