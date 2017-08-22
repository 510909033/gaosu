<?php
namespace app\admin\validate;
use think\Validate;

class UserRoleValidate extends Validate{
    
    protected $rule = [
        'user_id'=>'require|integer',
        'role_id'=>'require|integer',
    ];
    
    
    
}