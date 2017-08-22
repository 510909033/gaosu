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
use app\admin\model\SysMenu;

class MenuTest extends TestCase
{
    private $data = array (
            'name' => '菜单unit',
            'fid' => '0',
            'status' => '1',
            'module' => 'admin',
            'controller' => 'menu',
            'action'=>'save',
            'left_menu'=>1,
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
        }
        
        
        if ($is_add && !$is_update){
            
            $this->makeRequest('POST', 'menu',$parameters , [] , $files);
            
            
        }else if ($is_update && !$is_add){
            $parameters['_method'] = 'PUT';
 
            $this->makeRequest('PUT', 'menu/'.$id,$parameters , [],$files);
            
        }else if ($is_delete ){
            $parameters['_method'] = 'DELETE';
            $this->makeRequest('DELETE', 'menu/'.$id,$parameters , [],$files);
        }else{
            $this->fail('error args ,line='.__LINE__.',file='.__FILE__);
        }
    }
    
    
    
    /**
     * 添加成功
     */
    public function testAddSuccess($data=[]){
        if ($data){
            $this->data= $data;
        }else{
            $this->data['name'] = 'unit_name-'.uniqid();
            $this->data['action'] = 'unit-action-'.uniqid();
        }
        
        
        $parameters= $this->data;
    
        $this->_makeRequest($parameters,true,false);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__NO_ERROR,]);
        
    }
    
    public function testAddSubMenu(){
        $parameters= $this->data;
        $this->data['fid'] = 0;
        $this->testAddSuccess();
        
        $arr = json_decode($this->response->getContent(),true);

        $parent_id = $arr['id'];
        
        $menu = SysMenu::get($arr['id']);
        $this->data['name'] = 'sub-'.$menu->id;
        $this->data['fid'] = $menu->id;
        $this->data['action'] = 'unit-action-'.uniqid();
        $this->testAddSuccess($this->data);
        
        $arr = json_decode($this->response->getContent(),true);
        $menu = SysMenu::get($arr['id']);
        $this->data['name'] = 'sub-'.$menu->id;
        $this->data['fid'] = $menu->id;
        $this->data['action'] = 'unit-action-'.uniqid();
        $this->testAddSuccess($this->data);
        
        $parameters = [
            'id'=>$parent_id,
        ];
        $this->_makeRequest($parameters, false, false,true);
        $arr = json_decode($this->response->getContent(),true);
   
        $this->assertEquals(ConfigTool::$ERRCODE__COMMON, $arr['errcode']);
        
   
        
        
    }
    
    
    
    public function testAdd_field_empty(){
        $copy = $this->data;
        
        foreach ($copy as $field=>$value){
        
            $parameters= $this->data;
            $parameters[$field] = '';
            $this->_makeRequest($parameters,true,false);
            
            $arr = json_decode($this->response->getContent(),true);
        
            if ($arr && $arr['errcode'] != ConfigTool::$ERRCODE__COMMON){
                $this->fail( '-------please unset this field ,field = '.$field );
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
    
    public function testDelete(){
        $this->testAddSuccess();
        $arr = json_decode($this->response->getContent(),true);
        $parameters = [
            'id'=>$arr['id'],
        ];
        $this->_makeRequest($parameters, false, false,true);
        $arr = json_decode($this->response->getContent(),true);
    
        $model = SysMenu::get($arr['id']);
        $this->assertEquals(null, $model);
    }
    
    
//     /**
//      * 修改 成功
//      */
//     public function testModifySuccess(){
    
//         //标准修改
//         $parameters= $this->data;
        
//         $hasBind = WayUserBindCar::getOne($this->sysUser->id);
//         if (!$hasBind){
//             $this->testAddSuccess();
//             $hasBind = WayUserBindCar::getOne($this->sysUser->id);
//         }
        
//         $username = '名山胜水';
//         $parameters['id'] = $hasBind->id;
//         $parameters['username'] = $username;
        
//         $this->_makeRequest($parameters,false,true);
    
//         $data = [
//             'errcode'=>ConfigTool::$ERRCODE__NO_ERROR,
//         ];
//         $this->seeJsonContains($data);
        
//         $hasBind = WayUserBindCar::getOne($this->sysUser->id);
//         $this->assertEquals($username, $hasBind->username);
//     }
    
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