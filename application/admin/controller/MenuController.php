<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysMenu;
use app\admin\validate\MenuValidate;
use app\admin\validate\RoleValidate;

class MenuController extends Controller
{
    use \app\common\trait_common\RestTrait;
    
    protected function _before_save(){
        return [
            'modelname'=>'\\'.SysMenu::class,
            'allowField'=>['name','fid','status','module','controller','action','left_menu'],
            'validate'=>new MenuValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.SysMenu::class,
            'allowField'=>['name','fid','status','module','controller','action','left_menu'],
            'validate'=>new MenuValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.SysMenu::class,
        ];
    }
    
 
    
    public function aAction(){
        
        $v = new RoleValidate();
        $data=[
            'name'=>'ddd',
        ];
        $v->scene('add')->check($data);
        
        dump($v->getError());
        
//              D O N A L D
//             +G E R A L D
//         　 　R O B E R T
/*
                5 2 6 4 8 5
                1 9 7 4 8 5
                7 2 3 9 7 0
106481
597481
703972

526485
197485
723970
                
 */
/*
//              5ONAL5
//             +GERAL5
//         　 　ROBER0
 * 
 * 
 */


        $d = 5;
        $o=$n=$a=$l=$g=$e=$r=$b=$t=0;
        $a = [];
        for ($d=0;$d<=9;$d++){
            for ($t=0;$t<=9;$t++){
                if ( $d*2%10 == $t ){
        for ($l=0;$l<=9;$l++){//l start
            for ($r=0;$r<=9;$r++){// r start
                if( ($l + $l + 1 )%10  == $r){
                    //l + l = r
                    for ($a=0;$a<=9;$a++){//a start
                        for ($e=0;$e<=9;$e++){//e start
                            if (  ( (($l + $l + 1 )) > 10 &&  ($a + $a + 1 )%10 == $e ) || ( (($l + $l + 1 )) <= 10 &&  ($a + $a  )%10 == $e)   ){
                                for ($n=0;$n<=9;$n++){//n start
                                    for ($b=0;$b<=9;$b++){// b start
                                        if (  ($a*2 > 10 && ($n + $r + 1)%10  == $b) || $a*2 <= 10 && ($n + $r )%10  == $b   ){
                                            for ($o=0;$o<=9;$o++){//o start
                                                if (  ( ($n + $r) > 10 &&  ($o + $e + 1 )%10 == $o) || ( ($n + $r) <= 10 &&  ($o + $e  )%10 == $o) ){
                                                    for ($g=0;$g<=9;$g++){//g start
                                                       if ( ( ( $n + $r) > 10 &&  ($d + $g + 1 )%10 == $r ) ||  ( ( $n + $r) <= 10 &&  ($d + $g  )%10 == $r ) ){
                                                            if  ( ($d + $g) < 9 ){ 
                                                              
                                                              if ( $d != $o && $d != $n && $d != $a && $d != $l
                                                                    && $o != $n && $o != $a && $o != $l 
                                                                    && $n != $a && $n != $l && $n != $g && $n !=$e 
                                                                    && $g != $e && $g != $o && $g != $r && $g!= $t
                                                                  && $a !=$e && $n != $r  && $o != $t && $o!=$b && $o!= $e && $r!=$o
                                                                  && $d != $t 
                                                                  ){
                                                                
                                                                   
                                                                   
                                                                   $aa = 'd,o,a,n,l,g,e,r,b,t';
                                                                   $arr=[];
                                                                   $arr = explode(',', $aa);
                                                                   $new = [];
                                                                   foreach ($arr as $v){
                                                                       $new[$v] = $$v;
                                                                   }
                                                                   
                                                                   if (count(array_unique(array_values($new))) != 10 ){
                                                                       continue;
                                                                   }
                                                                   asort($new);
                                                                   dump($new);
                                                                   
                                                                   echo $d.$o.$n.$a.$l.$d;
                                                                   echo '<br />';
                                                                   echo $g.$e.$r.$a.$l.$d;
                                                                   echo '<br />';
                                                                   echo $r.$o.$b.$e.$r.$t;
                                                                   echo '<br />';
                                                                   
                                                                   foreach ($new as $k=>$v){
                                                                       echo $k.'='.$v.'<br />';
                                                                   }
                                                                   echo '<hr />';
                                                                   
                                                              }
                                                            }
                                                       }
                                                    }//g end
                                                }
                                            }//o end
                                        }
                                    }//b end
                                }//n end
                            }
                        }//e end
                    }//a end
                }
            }//r end
            $l++;
        }//l end
                }
            }//t end
        }//d end
        
        
        
    }
    
}
