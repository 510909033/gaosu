<?php
namespace app\admin\validate;
use think\Validate;

class MenuValidate extends Validate{
    
    protected $rule = [
        'name'=>'require',
        'fid'=>'require|integer',
        'status'=>'require|in:0,1',
        'module'=>'requireIf:type,1',
        'controller'=>'requireIf:type,1',
        'action'=>'requireIf:type,1',
        'left_menu'=>'require|in:0,1',
        'sort'=>'require',
        'type'=>'require',
    ];
    
    protected $message = [
    ];
    
    
}