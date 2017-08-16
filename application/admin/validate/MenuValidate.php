<?php
namespace app\admin\validate;
use think\Validate;

class MenuValidate extends Validate{
    
    protected $rule = [
        'name'=>'require',
        'fid'=>'require|integer',
        'status'=>'require|in:0,1',
        'module'=>'require|unique:sys_menu,module^controller^action',
        'controller'=>'require',
        'action'=>'require',
        'left_menu'=>'require|in:0,1',
    ];
    
    protected $message = [
        'module.unique'=>'module^controller^action唯一',  
    ];
    
    
}