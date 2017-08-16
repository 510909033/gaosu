<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\SysRole;
use app\admin\validate\RoleValidate;
use app\common\tool\ConfigTool;
use app\common\model\SysLogTmp;
use think\db\Query;

class RoleController extends Controller
{
    use \app\common\trait_common\RestTrait;
    protected $page = 1;
    protected $pagesize = null;
    
    protected function _before_save(){
        return [
            'modelname'=>'\\'.SysRole::class,
            'allowField'=>['name','fid','status','is_nav','desc'],
            'validate'=>new RoleValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.SysRole::class,
            'allowField'=>['name','fid','status','is_nav','desc'],
            'validate'=>new RoleValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.SysRole::class,
        ];
    }
    
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function indexAction()
    {
        $page=1;
        $listRows=10;
        $modelname='';
        $vars = [];
        $query=null;
        $list = [];
        
        
        $modelname = $this->getModelname();
//         $modelname
        
        $query = new \think\db\Query();
        $query->setTable($modelname::getTable()); 
        $list = $query->order('id','desc')->page($this->page,$this->pagesize)->select();
        
        $list = SysRole::getList(0);
        
        dump($list);
        
        return \view('',$vars);
        
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function createAction()
    {
        $vars = [];
        $modelname = $this->getModelname();
        
        
        
        return \view('',$vars);
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
    public function edit($id)
    {
        //
    }




}
