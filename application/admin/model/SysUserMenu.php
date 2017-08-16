<?php

namespace app\admin\model;

use think\Model;

class SysUserMenu extends Model
{
    use \app\common\trait_common\ModelTrait;
    /**
     *
     * @param unknown $data
     * @return boolean
     */
    protected static function before_create($data){

        return true;
    }
    protected Static function before_delete(Model $model){
        return true;
    }
}
