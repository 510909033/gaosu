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
use app\admin\model\SysRole;
use app\admin\model\SysUserMenu;

class UserMenuTest extends TestCase
{
    private $data = array (
            'user_id' => 1,
            'menu_id' => 1,
            'allow' => 'require|in:0,1,2',
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
            
            $this->makeRequest('POST', 'usermenu',$parameters , [] , $files);
            
            
        }else if ($is_update && !$is_add){
            $parameters['_method'] = 'PUT';
 
            $this->makeRequest('PUT', 'usermenu/'.$id,$parameters , [],$files);
            
        }else if ($is_delete ){
            $parameters['_method'] = 'DELETE';
            $this->makeRequest('DELETE', 'usermenu/'.$id,$parameters , [],$files);
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
            
            $this->data['user_id'] = SysUser::max('id');
            $this->data['menu_id'] = SysMenu::max('id');
            $this->data['allow'] = 1 ;
            $where = $this->data;
            unset($where['allow']);
            $model = SysUserMenu::get($where);
            if ($model){
                SysUserMenu::destroy($where);
            }
        }
        
        
        $parameters= $this->data;
    
        $this->_makeRequest($parameters,true,false);
        $this->seeJsonContains(['errcode'=>ConfigTool::$ERRCODE__NO_ERROR,]);
        
        
        
        
        
        
        
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

    public function testDelete(){
        
        $this->testAddSuccess();
        $arr = json_decode($this->response->getContent(),true);
        $parameters = [
            'id'=>$arr['id'],
        ];
        $this->_makeRequest($parameters, false, false,true);
        $arr = json_decode($this->response->getContent(),true);
    
        $model = SysUserMenu::get($arr['id']);
        $this->assertEquals(null, $model);
        $this->testAddSuccess();
    }
    
 
    
    
}