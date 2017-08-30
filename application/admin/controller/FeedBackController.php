<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use app\common\tool\AjaxTool;
use app\common\tool\Verifier;
use app\admin\controller\PublicController;

class FeedBackController extends PublicController
{
    public function indexAction(){
    	$num = 20 ;

    	$data = model('SysFeedBack')->paginate($num);
    	$page = $data->render();
		$this->assign('page', $page);
    	$this->assign('data',$data);
      	return $this->fetch('feedback');
    }
    

    public function addAction(){
    	$verify = Verifier::validation($_POST, array(
			array('name:tel' => '联系方式', 'name:msg' => '反馈信息'),
			array('tel, msg','noempty'),
			array('tel, msg','required'),
			array('tel', 'mobile'),
		));
		if (true !== $verify)
			AjaxTool::outputError($verify['msg']);
			
		$data['tel'] = $_POST['tel'];
		$data['msg'] = $_POST['msg'];
		$data['aid'] = 1;
		$data['add_time'] = date('Y-m-d H:i:s',time());


		$res = model('SysFeedBack')->insertGetId($data);
		if (is_numeric($res)) 
			AjaxTool::outputDone($res);
		else
			AjaxTool::outputError('失败');
    }
    
    public function updatestateAction(){
        $verify = Verifier::validation($_POST, array(
			array('name:id' => '数据id'),
			array('id','noempty'),
			array('id','required'),
			array('id', 'int'),
		));	
		
		if (true !== $verify)
			AjaxTool::outputError($verify['msg']);

		$data['status'] = 1;

		$res = model('SysFeedBack')->updateData($data,$_POST['id']);

		if ($res) 
			AjaxTool::outputDone($res);
		else
			AjaxTool::outputError('失败');

    }
}
