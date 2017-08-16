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

class UserController extends NeedLoginController
{
    
    public function testAction(){
        $config=[
            
        ];
       
//         Session::init($config);
      
        return 'test';
    }
 
    public function indexAction(){
        
        
        $wayUserBindCar = WayUserBindCar::get( array('user_id'=>UserTool::getUser_id()));
        
        
        if ($wayUserBindCar){
            $this->redirect('way/user/read',['id'=>$wayUserBindCar->id] );
        }else{
            $this->redirect('way/user/create');
        }
    }
    
    public function createAction(){
        if (WayUserBindCar::getOne(UserTool::getUser_id())){
            exception('您已绑定了车辆');
        }
        
        View::share('form_url' , url('way/user/save'));
        View::share('form_method','post');
        
        
        return $this->read(0);
    }
    
    public function readAction($id){

        if (!WayUserBindCar::getOne(UserTool::getUser_id())){
            exception('您尚未绑定车辆');
        }
        
     
        $vars = [
            'id'=>$id,
        ];
        View::share('form_url' , url('way/user/update' , $vars));
        View::share('form_method','put');
        
        return $this->read($id);
    }
    
    
    public function saveAction(){
        return $this->userBindCar(true,false);
    }
    
    public function updateAction(){
        return $this->userBindCar(false,true);
    }
    
    /**
     * 车辆绑定显示页
     * @param number $id
     * @return \think\response\View
     */
    private function read($id){
        
      
        
        
        $vars = [];
        $form = [];
        try {
            if ($id){
//                 $wayUserBindCar = WayUserBindCar::get( array('id'=>$id,'user_id'=>UserTool::getUser_id()));
                $wayUserBindCar = WayUserBindCar::get( array('user_id'=>UserTool::getUser_id()));
                if ($wayUserBindCar){
                   $form = $wayUserBindCar; 
                }
            }else{
                $where = [
                    'user_id'=>UserTool::getUser_id()
                ];
               
                
                $wayUserBindCar = WayUserBindCar::where($where)->find();
                
            
                if ($wayUserBindCar){
                    $form = $wayUserBindCar;
                }
            }
            $vars['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
        } catch (\Exception $e) {
            $vars['errcode'] = ConfigTool::$ERRCODE__EXCEPTION ;
            exception('系统错误');
        }
        
        if ($form){
            $form->reg_time = date('Y-m-d' ,$form->reg_time);
            $vars['form'] = $form->toJson();
        }else{
            $vars['form'] = '[]';
        }
        
 
        $vars['rsa_public_key'] = ConfigTool::$RSA_PUBLIC_KEY;
        
        return \view('bindindex',$vars);
    }


    private function debugLogUserBindCarAction($array){
        TmpTool::arrayToArrayFile($array);
    }
    
    private function uploadImage($name){
        if (!ConfigTool::$IS_UPLOAD_IDENTITY_IMAGE){
            return '';
        }
        $file=  $this->request->file($name);
        if (null === $file){
            return '';
        }
        $path = INDEX_PATH.'upload'.DS;
        $rule = ['size'=>2024000,'ext'=>'jpg,png,gif'];
        $info  = $file->validate($rule)->move($path);
        if ( $info ){
            return $info->getSaveName();
        }
        $msg = is_array($file->getError())?implode('<br />', $file->getError()):$file->getError();
        exception($msg , ConfigTool::$ERRCODE__COMMON);
    }
    
    /**
     * @param unknown $data
     * @return true|array   true表示通过验证， array为数据验证失败
     */
    private function imageValidate_add($data){
        if (input('unit')){
            return true;
        }
        
        $validate = new Validate();
        $validate->rule('identity_image0' , 'require');
        $validate->rule('identity_image1' , 'require');
        $validate->rule('driving_license_image' , 'require');
        
        $validate->message('identity_image0' , '身份证正面图片必传');
        $validate->message('identity_image1' , '身份证反面图片必传');
        $validate->message('driving_license_image' , '行驶证图片必传');
        
        if ( ! $validate->check($data)){
            return $validate->getError();
        }
        return true;
    }
    

    /**
     * 用户绑定车辆
     * errcode
     * 
     */
    private function userBindCar($is_add,$is_update){
        //SELECT id,status,verify,qrcode_version,car_number FROM `way_user_bind_car` order by id desc limit 100
        //return response('',500);
        
        $json =[];
        try {
            usleep(2000);
            $wayUserBindCar = new WayUserBindCar();
            
            //获取post数据
            if ($this->request->isPut()){
                $data = $this->request->put();
            }else{
                $data = $this->request->post();
            }
            
            foreach ($data as $k=>$v){
                if ($k == '_method'){
                    continue;
                }
                $decrypted='';
                $de_res=openssl_private_decrypt(base64_decode($v), $decrypted, ConfigTool::$RSA_PRIVATE_KEY);
                if (!$de_res){
                    exception('系统错误,field='.$k.',值为：'.$v.',line='.__LINE__.',method='.__METHOD__,ConfigTool::$ERRCODE__SHOULD_NOT_BE_DONE_HERE);
                }
                $data[$k] = $decrypted;
                
            }
            
            
            $data['user_id'] = UserTool::getUser_id();
            $data['openid'] = UserTool::getUni_account();
            $data['car_qrcode_path'] = '';
            $data['status'] = 0;
            $data['verify'] = 0;
            $data['create_time'] = time();
            $data['reg_time'] = strtotime($data['reg_time']);
            
            $data['identity_image1'] = $this->uploadImage('identity_image1');
            $data['identity_image0'] = $this->uploadImage('identity_image0');
            $data['driving_license_image'] = $this->uploadImage('driving_license_image');
            
            
            if ($is_add && !$is_update){
//                 unset($data['id']);
                $data['id'] = 0;
                $res=null;
                $res_validate = $this->imageValidate_add($data);
                if (true === $res_validate){
                    $res = $wayUserBindCar->addOne($data);
                }else{
                    \exception( '图片上传验证失败：'.var_export($res,true) ,ConfigTool::$ERRCODE__COMMON);
                }
            }else if (!$is_add && $is_update){
                
                $hasBind = WayUserBindCar::getOne(UserTool::getUser_id());
       
                $res = $wayUserBindCar->saveOne($data,$hasBind);
            }else{
                \exception('错误的条件,file='.__FILE__.',line='.__LINE__ , ConfigTool::$ERRCODE__SHOULD_NOT_BE_DONE_HERE);
            }
            
            if (!$res){
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = implode('<br />', (array)$wayUserBindCar->getError());
                $json['unit']['error'] = $wayUserBindCar->getError();
          
            }else{
                if(!$res->car_qrcode_path){
                    WayUserBindCar::save_car_qrcode_path($res);
                }
                
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '绑定车辆成功';
                $json['view_url'] = url('way/user/read',['id'=>$res->id]);
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__EXCEPTION;
            $json['debug']['e'] = $e;
            $json['html'] = '系统错误';
            if (ConfigTool::IS_LOG_TMP){
                $log_content = print_r($json,true) ;
                SysLogTmp::log('绑定车辆出现异常,json变量内容', $log_content , 0 ,__METHOD__);
                SysLogTmp::log('绑定车辆出现异常,e=', var_export($e,true), 0 ,__METHOD__);
            }
        }
   
        
        if (ConfigTool::IS_LOG_TMP){
            $log_content = print_r($json,true) ;
            SysLogTmp::log('绑定车辆结果,method='.$this->request->method().',id='.input('id'), print_r(array('json'=>$json,'data'=>$data ,
                'file'=>$this->request->file(),
            ),true) , 0 ,__METHOD__);
        }
        
        return json($json);
        
    }
    
    
    public function getCarTypeJsonAction(){
        $all = WayCarType::all();
        foreach ($all as $k=>$v){
            $all[$k] = $v->toArray();
            $all[$k]['name'] = $v->name.'('.$v->title.')';
        }
        
        return json(array('records'=>$all));
    }


    public function getCarColorJsonAction(){
        
        $all = SysConfig::getListByType(SysConfig::TYPE_GS_COLOR_CONFIG);
        
        return json(array('records'=>$all));
    }
    
}
