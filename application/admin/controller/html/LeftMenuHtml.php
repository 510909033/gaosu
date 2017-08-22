<?php

namespace app\admin\controller\html;

use think\Controller;
use app\common\tool\UserTool;
use app\admin\model\SysMenu;

class LeftMenuHtml 
{
    private static $privList;
    public static function getLeftMenu($user_id){
        
        
        $list = SysMenu::getList(0);
        
        self::$privList = UserTool::getAllPrivi($user_id);
        
        
        $html ='<div class="left_main">
    <div class="hide_bt"></div>';
        
        $html .=self::getHtml($list, 0);
        $html .='</div>';
        return $html;
        
    }
    
    
    private static function getHtml($list ,$level){
        $html = '<ul class="u1">';
        
        foreach ($list as $k=>$v){
            
            if ( false === array_search((string)$v['id'], self::$privList,true) ){
                continue;
            }
            
            if ( 1 == $v['type']){
                $url = url($v['module'].'/'.$v['controller'].'/'.$v['action']);
            }else if (2 == $v['type']){
                $url = 'javascript:;';
            }else{
                $url = 'javascript:;';
                //不应该出现这个结果
            }
            
            $html .= '<li class="l1"><p><span><a href="'.$url.'">'.$v['name'].'</a></span></p>';
            
            if ($v['children']){
                $html .='<ul class="u2">';
             
                $html .=self::getHtml($v['children'] , $level+1);
             
                $html .='</ul>';
            }
            
            $html .='</li>';
        }
        
        $html .='</ul>';
        return $html;
    }
    
    
    
    
    
}
/*

<div class="left_main">
    <div class="hide_bt"></div>
    
    <ul class="u1">
        <li class="l1"><p><span>用户管理</span></p>
        	<ul class="u2">
                        <li class="l2">用户管理
                            <!--<ul class="u2">-->
                                <!--<li class="l2">用户管理</li>-->
                                <!--<li class="l2">用户管理</li>-->
                                <!--<li class="l2">用户管理</li>-->
                                <!--<li class="l2">用户管理</li>-->
                            <!--</ul>-->
                        </li>
                        <li class="l2">用户管理</li>
                        <li class="l2">用户管理</li>
                        <li class="l2">用户管理</li>
                    </ul>
        </li>
        <li class="l1"><p><span>缴费管理</span></p></li>
        <li class="l1"><p><span>用户管理</span></p></li>
        <li class="l1"><p><span>用户管理</span></p></li>
        <li class="l1"><p><span>用户管理</span></p></li>
    </ul>
    
</div>

 */