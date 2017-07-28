<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace tests;

use app\common\tool\ConfigTool;
use think\Session;
use app\common\tool\UserTool;
use app\common\model\SysUser;
use think\Env;
use app\common\model\WayUserBindCar;

class UserTest extends TestCase
{
    private $data = array (
            'username' => '刘晓小',
            'identity_card' => '220702198805055555',
            'phone' => '13544665577',
            'car_number' => '吉bHH126',
            'car_type_id' => '1',
            'engine' => '112244',
            'car_color' => '111',
            'reg_time' => '2016-12-11',
            'chassis_number' => '车架号22222',
            'brand' => '路虎朗逸',
            '_agree' => 'on',
            'id' => '1',
            '_ajax' => '1',
            'user_id' => '',
            'openid' => '94232ff5_11114',
            'car_qrcode_path' => '',
            'status' => 0,
            'verify' => 0,
            'create_time' => 1501060532,
        );

    private $sysUser;
    public function setUp(){
        Session::boot();
        $this->sysUser = SysUser::get(Env::get('debug.user_id'));
        UserTool::init($this->sysUser);
        $this->data['user_id'] = UserTool::getUser_id();
    }
    
    public function testBindIndex(){
        $this->visit('way/user/bindindex')->assertViewHas('errcode' , ConfigTool::$ERRCODE__NO_ERROR);
        
        $this->assertResponseOk();
    }
    
    private function _makeRequest($parameters){
        $this->makeRequest('post', 'way/user/userbindcar',$parameters);
    }
    
    
    
    /**
     * 添加成功
     */
    public function testAddSuccess(){
    
        WayUserBindCar::destroy(['user_id'=>$this->sysUser->id]);
        $parameters= $this->data;
        unset($parameters['id']);
        $parameters['car_number'] = strtoupper($parameters['car_number']);
    
        $this->_makeRequest($parameters);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__NO_ERROR,
    
        ]);
    }
    
    /**
     * 添加 && 未同意条款
     */
    public function testAddNo__argee(){
        //添加
        WayUserBindCar::destroy(['user_id'=>$this->sysUser->id]);
        $parameters= $this->data;
        unset($parameters['id']);
        unset($parameters['_agree']);
        
        $this->_makeRequest($parameters);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__COMMON,
        
        ]);
    }
    
    /**
     * 添加 && 未填写车牌
     */
    public function testAddNo_car_number(){
        WayUserBindCar::destroy(['user_id'=>$this->sysUser->id]);
        $parameters= $this->data;
        unset($parameters['id']);
        unset($parameters['car_number']);
    
        $this->_makeRequest($parameters);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__COMMON,
    
        ]);
    }

    
    /**
     * 修改 成功
     */
    public function testModifySuccess(){
    
        //标准修改
        $parameters= $this->data;
        
        $hasBind = WayUserBindCar::getOne($this->sysUser->id);
        if (!$hasBind){
            $this->testAddSuccess();
            $hasBind = WayUserBindCar::getOne($this->sysUser->id);
        }
        
        $username = date('Y-m-d H:i:s');
        $parameters['id'] = $hasBind->id;
        $parameters['username'] = $username;
        
        $this->_makeRequest($parameters);
    
        $data = [
            'errcode'=>ConfigTool::$ERRCODE__NO_ERROR,
        ];
        $this->seeJsonContains($data);
        
        $hasBind = WayUserBindCar::getOne($this->sysUser->id);
        $this->assertEquals($username, $hasBind->username);
    }
    
    /**
     * 修改 && 未填写username
     */
    public function testModifyNo_username(){
    
        //标准修改
        $parameters= $this->data;
    
        $hasBind = WayUserBindCar::getOne($this->sysUser->id);
        if (!$hasBind){
            $this->testAddSuccess();
            $hasBind = WayUserBindCar::getOne($this->sysUser->id);
        }
    
        $username = date('Y-m-d H:i:s');
        $parameters['id'] = $hasBind->id;
        unset($parameters['username'] );
    
        $this->_makeRequest($parameters);
    
        $data = [
            'errcode'=>ConfigTool::$ERRCODE__COMMON,
        ];
        $this->seeJsonContains($data);
    
    }
    
    
}