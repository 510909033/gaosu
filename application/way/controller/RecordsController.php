<?php

namespace app\Way\controller;

use think\Controller;
use think\Request;
use app\way\controller\func\UserBindCarFuncController;
use app\common\model\WayUserBindCar;
use app\way\controller\NeedLoginController;
use app\common\tool\UserTool;
use think\Exception;
use app\common\model\SysUser;
use app\common\tool\ConfigTool;
use app\common\model\SysLogTmp;
use app\common\tool\TmpTool;
use think\helper\Time;
use app\common\model\WayCarType;
use app\common\model\SysConfig;
use think\Session;
use think\View;
use youwen\exwechat\ErrorCode;
use think\File;
use think\Validate;
use vendor\SMS\SmsSingleSender;
use app\way\validate\WayUserBindCarValidate;

use think\Paginator;
use think\Db;
use app\admin\model\User;//引入模型层

use app\common\tool\Verifier;
use app\common\tool\AjaxTool;
use app\way\model\Records;

class RecordsController extends \app\common\controller\NeedLoginController
{
    public function indexAction()
    {	
    	$res 		= array();
    	$car_num 	= ""; 
    	//后期读取缓存
        $res_station = Db::name('kw_payrate')->select();
        var_dump($res_station);
        die;
        $station     = array('311'=>'长春东','31A'=>'莲花山');
        $is_pay      = array('0'=>'未支付','1'=>'已支付');

    	$user_id = UserTool::getUser_id();

    	if ($user_id) {
	       	$res_1 = Db::name('way_user_bind_car')->where("'$user_id' = user_id")->select();

	       	if ($res_1) {
       			$car_num = $res_1[0]['car_number'];
        		$res = Db::name('way_log')->order('is_pay asc')->where("'$user_id' = user_id")->select();
        		if ($res) {
        			foreach ($res as $key => $value) {
        				$res[$key]['in_pos_id'] = isset($station[$value['in_pos_id']]) ? $station[$value['in_pos_id']] : "";	
        				$res[$key]['out_pos_id'] = isset($station[$value['out_pos_id']]) ? $station[$value['out_pos_id']] : "";
        				$res[$key]['is_pay'] = isset($is_pay[$value['is_pay']]) ? $is_pay[$value['is_pay']] : "";	
        				$res[$key]['create_time'] = date('Y-m-d',strtotime($value['create_time']));	
        			}
        		}
	       	}
    	}

       	$this->assign('car_num',$car_num);
        $this->assign('res', $res);

         return $this->fetch('records');
    }
}
