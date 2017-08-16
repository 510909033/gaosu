<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysRoleMenu;
use app\admin\validate\RoleMenuValidate;

class RoleMenuController extends Controller
{
    use  \app\common\trait_common\RestTrait;
   protected function _before_save(){
        return [
            'modelname'=>'\\'.SysRoleMenu::class,
            'allowField'=>['role_id','menu_id','allow'],
            'validate'=>new RoleMenuValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.SysRoleMenu::class,
            'allowField'=>['role_id','menu_id','allow'],
            'validate'=>new RoleMenuValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.SysRoleMenu::class,
        ];
    }
}
