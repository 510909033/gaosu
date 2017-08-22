<?php
namespace app\admin\validate;
use think\Validate;

class RoleMenuValidate extends Validate{
    
    protected $rule = [
        'role_id'=>'require|integer',
        'menu_id'=>'require|integer',
        'allow'=>'require|in:0,1,2',
    ];
    
    
    
}