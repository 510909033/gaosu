<?php

namespace app\admin\controller;

use think\Controller;
use app\common\controller\NeedLoginController;
use app\common\interf\IPriviCheckInterf;

//表示在后台模块
defined('ADMIN_MODULE') ?'':define('ADMIN_MODULE', TRUE);
/**
 * 后台模块 父类
 * @author Administrator
 *
 */
class PublicController extends NeedLoginController implements IPriviCheckInterf   
{
 
}
