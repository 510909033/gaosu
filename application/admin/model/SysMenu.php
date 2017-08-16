<?php

namespace app\admin\model;

use think\Model;
use think\db\Query;

class SysMenu extends Model
{
    use \app\common\trait_common\ModelTrait;
    /**
     * 
     * @param unknown $data
     * @return boolean
     */
    protected static function before_create($data){
        if ($data['fid'] > 0){
            if ( !self::get($data['fid']) ){
                return lang('上级菜单不存在，上级菜单id='.$data['fid']); 
            }
        }
        return true;
    }
    
    protected Static function before_delete(Model $model){
        $data = function (Query $query) use($model ){
            $query->where('fid',$model->id);
        };
        if (self::get($data)){
            return '不能删除数据，还有下级数据';
        }
        return true;
    }
}
