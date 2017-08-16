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
use think\Route;
use app\common\model\SysConfig;
use think\File;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Tests\Field\FileFormFieldTest;
use app\admin\model\SysRole;

class RoleTest extends TestCase
{
    private $data = array (
            'name' => '角色unit',
            'fid' => '0',
            'status' => '1',
            'is_nav' => '1',
            'desc' => 'desc',
        );

    private $sysUser;
    
 
    
    public function setUp(){
        Session::boot();
        print __CLASS__.PHP_EOL;
        
    }
    
    
    
    private function _makeRequest($parameters,$is_add,$is_update,$is_delete=false){
        $files=[];
        $id = isset($parameters['id'])?$parameters['id']:0;
        $parameters['unit'] = 1;
        foreach ($parameters as $k=>$v){
            if ( '_method' == $k){
                continue;
            }
//             openssl_public_encrypt($v, $crypted, ConfigTool::$RSA_PUBLIC_KEY);
//             $parameters[$k] = base64_encode($crypted);
            
        }
        
        
        
        if ($is_add && !$is_update){
            
            $this->makeRequest('POST', 'role',$parameters , [] , $files);
            
            
        }else if ($is_update && !$is_add){
            $parameters['_method'] = 'PUT';
 
            $this->makeRequest('PUT', 'role/'.$id,$parameters , [],$files);
            
        }else if ($is_delete ){
            $parameters['_method'] = 'DELETE';
            $this->makeRequest('DELETE', 'role/'.$id,$parameters , [],$files);
        }else{
            $this->fail('error args ,line='.__LINE__.',file='.__FILE__);
        }
    }
    
    
    
    /**
     * 添加成功
     */
    public function testAddSuccess(){
    
        $parameters= $this->data;
        $parameters['name'] = 'role-unit-'.uniqid();
    
        $this->_makeRequest($parameters,true,false);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__NO_ERROR,]);
        $arr = json_decode($this->response->getContent(),true);
        $model = SysRole::get($arr['id']);
        $this->assertEquals($parameters['name'], $model->name);
        
    }
    
    public function testAdd_field_empty(){
        $copy = $this->data;
        
        foreach ($copy as $field=>$value){
            if ('desc' == $field){
                continue;
            }
            $parameters= $this->data;
            $parameters[$field] = '';
            $this->_makeRequest($parameters,true,false);
            
            $arr = json_decode($this->response->getContent(),true);
        
            if ($arr && $arr['errcode'] != ConfigTool::$ERRCODE__COMMON){
                $this->fail( '-------please unset this field ,field = '.$field.',errcode='.$arr['errcode'] );
            }
            
            $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__COMMON,]);
            
        }
    }
    
    /**
     * 添加 && 未填写车牌
     */
    public function testAddNo_name(){
        $parameters= $this->data;
        unset($parameters['name']);
    
        $this->_makeRequest($parameters,true,false);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__COMMON,
    
        ]);
    }
    
    
    /**
     * 修改 成功
     */
    public function testModifySuccess(){
    
        $this->testAddSuccess();
        $arr = json_decode($this->response->getContent(),true);
        
        $parameters = $this->data;
        $parameters['id'] = $arr['id'];
        $parameters['name'] = 'modify-role-'.uniqid();
        
        
        $this->_makeRequest($parameters,false,true);
    
        $data = [
            'errcode'=>ConfigTool::$ERRCODE__NO_ERROR,
        ];
        $this->seeJsonContains($data);
        
        $role = SysRole::get($arr['id']);
        
        $this->assertEquals($parameters['name'], $role->name);
        
    }
    
    
    public function testDelete(){
        $this->testAddSuccess();
        $arr = json_decode($this->response->getContent(),true);
        $parameters = [
            'id'=>$arr['id'],
        ];
        $this->_makeRequest($parameters, false, false,true);
        $arr = json_decode($this->response->getContent(),true);
        
        
        $model = SysRole::get($arr['id']);
        $this->assertEquals(null, $model);
        
        
    }
    
    
//     /**
//      * 修改 && 未填写username
//      */
//     public function testModifyNo_username(){
    
//         //标准修改
//         $parameters= $this->data;
    
//         $hasBind = WayUserBindCar::getOne($this->sysUser->id);
//         if (!$hasBind){
//             $this->testAddSuccess();
//             $hasBind = WayUserBindCar::getOne($this->sysUser->id);
//         }
    
//         $username = 'unit_testModifyNo_username';
//         $parameters['id'] = $hasBind->id;
//         unset($parameters['username'] );
    
//         $this->_makeRequest($parameters,false,true);
    
//         $data = [
//             'errcode'=>ConfigTool::$ERRCODE__COMMON,
//         ];
//         $this->seeJsonContains($data);
        
    
//     }
    
    
    
}