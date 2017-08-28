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
        
        if (!self::$privList){
            return '';
        }
        
        
        $html ='<div class="left_main">
    <div class="hide_bt"></div>';
        
        $html .=self::getHtml($list, 0);
        $html .='</div>';
        return $html;
        
    }
  
    
  

    private static function getHtml($list ,$level){
        static $left_select=null;
        if (is_null($left_select)){
            $left_select = input('left_select');
        }
        
        $html = '<ul class="u1">';
        
        
        $num = $level+1;
        if ($num > 1){
            $p_style='border-bottom:none;';
            $span_style='background:none';
            $li_style="padding-left:0;";
            $level_2_style='';
        }else{
            $p_style='';
            $span_style='';
            $li_style="";
            
        }
  
        
        foreach ($list as $k=>$v){
            
            if ( false === array_search((string)$v['id'], self::$privList,true) ){
                continue;
            }
            
            if ( 1 == $v['type']){
                $url = url($v['module'].'/'.$v['controller'].'/'.$v['action'],['left_select'=>$v['id']]);
            }else if (2 == $v['type']){
                $url = 'javascript:;';
            }else{
                $url = 'javascript:;';
                //不应该出现这个结果
            }
            
            $html .= '<li class="l'.$num.'" style="'.$li_style.'" ><p style="'.$p_style.'"><span style="'.$span_style.'"><a href="'.$url.'">'.$v['name'].'</a></span></p>';
            
            if ($v['children']){
                $level_2_style='';
                foreach ($v['children'] as $tmp_v){
                    if ( !is_null($left_select) && $tmp_v['id'] == $left_select){
                        $level_2_style = 'display:block;adsfasdf;';
                    }
                }
                
                $html .='<ul class="u2" style="'.$level_2_style.'">';
             
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