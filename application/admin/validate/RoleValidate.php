<?php
namespace app\admin\validate;
use think\Validate;

class RoleValidate extends Validate{
    
    protected $rule = [
        'id'=>'require',
        'name'=>'require',
        'fid'=>'require',
        'status'=>'require',
        'is_nav'=>'require',
        'sort'=>'require'
        
    ];
    
    protected $scene = [
        'save'  =>  [
            'name'=>'require',
            'fid'=>'require|integer',
            'status'=>'require|in:0,1',
            'is_nav'=>'require|in:0,1',
            'sort'=>'require',
            
            'id'=>'require|integer|gt:0'
        ],
        'add'=>[
            'name'=>'require|min:1',
            'fid'=>'require|integer',
            'status'=>'require|in:0,1',
            'is_nav'=>'require|in:0,1',
            'sort'=>'require',
        ]
    ];
    
    
    
}