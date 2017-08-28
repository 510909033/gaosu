<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;

class InstallController extends Controller
{
    
    public function init_menu(){
        $sql="
            INSERT INTO `sys_menu` (`id`, `fid`, `sort`, `name`, `status`, `type`, `module`, `controller`, `action`, `left_menu`) VALUES
            (4413, 0, -10, '权限-管理', 1, 1, 'admin', 'menu', 'index', 1),
            (4414, 4413, 0, '新建用户', 1, 1, 'admin', 'user', 'create', 1),
            (4415, 4413, 0, '菜单管理', 1, 1, 'admin', 'menu', 'index', 1),
            (4416, 4413, 0, '角色管理', 1, 1, 'admin', 'role', 'index', 1),
            (4445, 4413, 0, '用户列表', 1, 1, 'admin', 'user', 'index', 1),
            (4447, 0, 0, '嘉华', 1, 1, 'admin', 'UserList', 'index', 1);";
        
      Db::execute($sql);  
        
    }
    
}
