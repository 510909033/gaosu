<?php

namespace app\way\controller\func;

use think\Controller;
use app\common\model\SysConfig;
use app\common\model\SysArea;
use think\Db;

class InitFunc 
{
    public function initConfig(){
        $info = [];
        try {
            $config = new SysConfig();
            
//             $auth = new AuthController();
//             $auth->getAccessToken(false);
            
            $info['初始化配置表'] = $config->init_table_data();
            
            $res = Db::query("show tables");
            foreach ($res as $arr ){
                $info['表名'][] = current($arr);
//                 $info['表行数大小'][key($arr)] = Db::table(current($arr))->count('id');
            }
            
            if (SysArea::count('id') < 1){
                $info['初始化地区表'] = SysArea::execute(file_get_contents(EXTEND_PATH.'sys_area.sql'));
            }
        } catch (\Exception $e) {
            $info['异常'] = $e->getMessage();
        }
        

        
        dump($info);
    }
    
}
