<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\SysRole;
use app\admin\validate\RoleValidate;
use app\common\tool\ConfigTool;
use app\common\model\SysLogTmp;
use think\db\Query;
use think\Route;

class RoleController extends   PublicController
{
    use \app\common\trait_common\RestTrait;
    protected $page = 1;
    protected $pagesize = null;
    protected $route_prefix = 'admin/role/';
    
    
    
    
    
    protected function _before_save(){
        return [
            'modelname'=>'\\'.get_class(new SysRole()),
            'allowField'=>['name','fid','status','is_nav','desc','sort'],
            'validate'=>new RoleValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.get_class(new SysRole()),
            'allowField'=>['name','fid','status','is_nav','desc','sort'],
            'validate'=>new RoleValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.get_class(new SysRole()),
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
        
        
//         \app\admin\controller\MenuController::getLeftMenu();
        
//         exit;
        
        
        $modelname = $this->getModelname();
        
        $list = $modelname::getList(0,null);
        
        $html = $this->getIndexHtml($list, 0);

        $vars['html']['list']['html'] = $html;
        $vars['html']['add']['url'] = url($this->route_prefix.'create');
        $vars['html']['list']['title'] = '角色列表';
        return \view('',$vars);
    }
    
    private function getIndexHtml($list , $currentLevel){
        $html = '';
        static $level = 0;
        $subfix='';
    
        foreach ($list as $k=>$v){
    
            $prefix = str_repeat('&nbsp;', $currentLevel*8);
            $prefix .= $currentLevel==0?'': '└';
    
//             $html .= '<option value="'.$v['id'].'"  >'.$prefix.$v['name'].$subfix.'</option>';
            
            $name = $prefix.$v['name'].$subfix;

            
            $modify_url = url($this->route_prefix.'edit?id='.$v['id']);
            $edit_url = url('admin/RoleMenu/edit?id='.$v['id']);
            $delete_url = url($this->route_prefix.'delete?id='.$v['id']);
            
    
            
        $html .=<<<EEE
<tr>
    <td>{$name}</td>
    <td>{$currentLevel}</td>
    <td>{$v['status']}</td>
    <td>{$v['is_nav']}</td>
    <td>{$v['sort']}</td>
    <td>{$v['desc']}</td>
    <td>
        <button type="button" class="btn btn-warning xx" onclick="location.href='{$modify_url}';" >修改</button>
        <button type="button" class="btn btn-warning xx" onclick="location.href='{$edit_url}';" >编辑权限</button>  
        <button type="button" class="btn btn-warning xx" onclick="location.href='{$delete_url}';" >删除</button>    
    </td>
</tr>        
EEE;
    
        if ($v['children']){
            $level++;
            $eq = false;
            $html .=$this->getIndexHtml($v['children'],$currentLevel+1);
        }
        }
        return $html;
    }
    
    private function createAndEdit($id=0,$form){
        $modelname = $this->getModelname();
        
        if ($id){
            $model = $modelname::get($id);
        }else{
            $model = new $modelname();
            $model->name = '';
            $model->id=0;
            $model->is_nav = 0;
            $model->status = 1;
            $model->desc='';
            $model->fid=0;
            $model->sort = 0;
        }
        
        $list = $modelname::getList(0);
        $vars['form']['pid']['select']['html'] = $this->getHtmlPid($list,$model);
        
        $vars['model'] = $model;
        
        $vars=array_merge_recursive($vars,$form);
        return \view('createAndEdit',$vars);
    }
    
    private function _getHtmlPid($list , $currentLevel,$model){
        $html = '';
        static $level = 0;
        $subfix='';

        foreach ($list as $k=>$v){
          
            $prefix = str_repeat('&nbsp;', $currentLevel*4);
            
//             $subfix = '--'.$v['id'].'|'.$v['fid'];
            $selected =  $v['id'] === $model->fid?"selected":"";
            $html .= '<option value="'.$v['id'].'" '.$selected.' >'.$prefix.$v['name'].$subfix.'</option>';
            
            if ($v['children']){
                $level++;
                $eq = false;
                $html .=$this->_getHtmlPid($v['children'],$currentLevel+1,$model);
            }
        }
        return $html;
    }
    private function getHtmlPid($list,$model){
        $html ='<select name="fid" class="form-control">';
        $selected =  0 === $model->id?"selected":"";
        $html .= '<option value="0" '.$selected.'>顶级角色</option>';
        $html .=$this->_getHtmlPid($list,0,$model);
        $html .='</select>';
        return $html;
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

    public function deleteAction($id){
        $msg='';
        $modelname = $this->getModelname();
        try {
            $where=[
                'fid'=>$id,
            ];
            if ($modelname::get($where)){
                exception($msg='角色包含子角色，不能删除');
            }
            $res = $modelname::deleteOne($id);
            if (true !== $res){
                exception($msg=$res);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }
        if ('' === $msg){
            $this->success('success',null,'',1);
        }
        $this->error($msg);
    }

}
