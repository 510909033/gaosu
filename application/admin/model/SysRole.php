<?php

namespace app\admin\model;

use think\Model;
use app\admin\validate\RoleValidate;
use app\common\model\SysLogTmp;
use think\Validate;
use think\db\Query;

class SysRole extends Model
{
    use  \app\common\trait_common\ModelTrait;

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
    
    private static function _getList($fid,$status=null){
        $where = [
            'fid'=>$fid,
            'status'=>$status,
        ];
        if (is_null($status)){
            unset($where['status']);
        }
        $list = self::where($where)->order('sort asc,id asc')->select();
        $new = [];
        foreach ($list as $k=>$v){
            $new [] = $v->toArray();
        }
        if ($new){
            foreach ($new as $k=>$v){
                $new[$k]['children'] = self::_getList($v['id']);
            }
            
        }
        return $new;
    }
    /**
     * @param unknown $fid
     * @return array
     */
    public static function getList($fid,$status=null){
        return self::_getList($fid,$status);
    }
    
}
