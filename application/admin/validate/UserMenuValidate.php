<?php
namespace app\admin\validate;
use think\Validate;

class UserMenuValidate extends Validate{
    
    protected $rule = [
        'user_id'=>'require|integer',
        'menu_id'=>'require|integer',
        'allow'=>'require|in:0,1,2',
    ];
    
    
}