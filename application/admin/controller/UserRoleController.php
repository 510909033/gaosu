<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysUserRole;
use app\admin\validate\UserRoleValidate;

class UserRoleController extends Controller
{
    use  \app\common\trait_common\RestTrait;
   protected function _before_save(){
        return [
            'modelname'=>'\\'.SysUserRole::class,
            'allowField'=>['user_id','role_id'],
            'validate'=>new UserRoleValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.SysUserRole::class,
            'allowField'=>['user_id','role_id'],
            'validate'=>new UserRoleValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.SysUserRole::class,
        ];
    }
}
