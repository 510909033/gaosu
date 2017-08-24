<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\SysUser;
use app\common\validate\RegValidate;
use app\admin\model\SysRole;
use app\data\controller\v3\LoginController;
use think\Validate;
use app\common\tool\ConfigTool;
use app\common\model\SysConfig;

class UserController extends PublicController
{
    use \app\common\trait_common\RestTrait;
    protected $page = 1;
    protected $pagesize = null;
    protected $route_prefix = 'admin/user/';
    
    protected function _before_save(){
        return [
            'modelname'=>'\\'.get_class(new SysUser()),
            'allowField'=>['uni_account' , 'password' ,'sort' , 'regtime' , 'type'],
            'validate'=>new RegValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.get_class(new SysUser()),
            'allowField'=>['uni_account' , 'password' ,'sort' , 'regtime' , 'type'],
            'validate'=>new RegValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.get_class(new SysUser()),
        ];
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function indexAction()
    {
        $vars=[];
        $user = new SysUser();
        $where=[];
        $paginate = $user->getQuery()->where($where)->order('id','desc')->paginate(20,false);
        
        $arr = $paginate->toArray();
        $vars['html']['list']['data'] = $arr['data'];
        $vars['html']['list']['page'] = $paginate->render();
        
        return \view('index',$vars);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function createAction()
    {
                $form = [];
        $form['form']['submit']['url'] = url($this->route_prefix.'save');
        $form['form']['method'] = 'post';
        
        
        return $this->createAndEdit(0,$form);
    }
    
    private function createAndEdit($id=0,$form){
        $modelname = $this->getModelname();
    
        if ($id){
            $model = $modelname::get($id);
        }else{
            $model = new $modelname();
            $model->id = 0;
            $model->uni_account = '';
        }
    
    
        $vars['model'] = $model;
    
        $vars=array_merge_recursive($vars,$form);
        return \view('createAndEdit',$vars);
    }
    

    private function validateSaveAndUpdate(){
        $data = [];
        $data['uni_account'] = input('uni_account');
        $data['type'] = SysConfig::REG_TYPE_ADMIN;
        $data['solt'] = '12345';
        $data['regtime'] = time();
        $data['subscribe'] = 0;
        $data['create_time'] = time();
        $data['user_type'] = SysConfig::REG_USER_TYPE__PERSONAL;
        
        $password = input('password');
        $repeat_password = input('repeat_password');
        
        if (!Validate::is($data['uni_account'],'require')){
            exception('用户名必填');
        }
        
        if (!Validate::eq($password,$repeat_password)){
            exception('两次密码不同');
        }
        if (!$password){
            $password = $data['uni_account'];
            //                 exception('密码必填');
        }
        
        
        
        $data['password'] = sha1($password.$data['solt']);
        return $data;
    }
    
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function saveAction(Request $request)
    {
        try {
            $json = [];
            
            $data = $this->validateSaveAndUpdate();
            
            $regResult=SysUser::regApi($data);
            if ( 0 === $regResult['err'] ){
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '添加成功';
                $json['url']['next_page'] = url('admin/UserRole/edit?id='.$regResult['user_model']->id);
            }else{
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = implode('<br />'.PHP_EOL, (array)$regResult['reason']);
                $json['debug']['err'] = $regResult['reason'];
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
            $json['html'] = $e->getMessage();
        }
        
        
        return json($json);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function editAction($id)
    {
        
        
        $form = [];
        $form['form']['submit']['url'] = url($this->route_prefix.'update',['id'=>$id]);
        $form['form']['method'] = 'put';
        
        return $this->createAndEdit($id,$form);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function updateAction(Request $request, $id)
    {
        try {
            $json = [];
        
            $user = SysUser::get($id);
            if (!$user){
                exception('要编辑的用户不存在');
            }
            $data = [];
            $data['uni_account'] = input('uni_account');
            
            $password = input('password');
            $repeat_password = input('repeat_password');
            
            $isExistUser = SysUser::get(['uni_account'=>$data['uni_account'],'type'=>SysConfig::REG_TYPE_ADMIN ]);
            if ($isExistUser && $isExistUser->id != $user->id){
                exception('账号已经存在');
            }

            
            if (!Validate::eq($password,$repeat_password)){
                exception('两次密码不同');
            }
            if ($password){
                $data['password'] = sha1($password.$user->solt);
            }
            
            $res = $user->save($data);
            
        
            if ( false !== $res ){
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '修改成功';
                $json['url']['next_page'] = url('admin/userrole/edit?id='.$id);
            }else{
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = '修改失败';
            }
        } catch (\Exception $e) {
            $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
            $json['html'] = $e->getMessage();
        }
        
        
        return json($json);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
