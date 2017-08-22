<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysMenu;
use app\admin\validate\MenuValidate;
use app\admin\validate\RoleValidate;
use app\admin\controller\html\LeftMenuController;
use app\admin\controller\html\LeftMenuHtml;
use app\common\tool\UserTool;
use think\Loader;

class MenuController extends Controller
{
    public function demoAction(){
        
        
        
    }
    
    use \app\common\trait_common\RestTrait;
    
    
    protected $page = 1;
    protected $pagesize = null;
    protected $route_prefix = 'admin/menu/';
    
    
    
    protected function _before_save(){
        return [
            'modelname'=>'\\'.get_class(new SysMenu()),
            'allowField'=>['name','fid','status','module','controller','action','left_menu','sort','type'],
            'validate'=>new MenuValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.get_class(new SysMenu()),
            'allowField'=>['name','fid','status','module','controller','action','left_menu','sort','type'],
            'validate'=>new MenuValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.get_class(new SysMenu()),
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
        
        $list = $modelname::getList(0,null);
        
        $html = $this->getIndexHtml($list, 0);

        $vars['html']['list']['html'] = $html;
        
        $vars['html']['add']['url'] = url($this->route_prefix.'create');
        
        return \view('',$vars);
    }
    
    private function getIndexHtml($list , $currentLevel){
        $html = '';
        static $level = 0;
        $subfix='';
    
        foreach ($list as $k=>$v){
    
            $prefix = str_repeat('&nbsp;', $currentLevel*8);
            $prefix .= $currentLevel==0?'': '└';
    
            
            $name = $prefix.$v['name'].$subfix;

            
            $modify_url = url($this->route_prefix.'edit?id='.$v['id']);
        $html .=<<<EEE
<tr>
    <td>{$name}</td>
    <td>{$currentLevel}</td>
    <td>{$v['status']}</td>
    <td>{$v['left_menu']}</td>
    <td>{$v['sort']}</td>
    <td>{$v['module']}</td>
    <td>{$v['controller']}</td>
    <td>{$v['action']}</td>
    <td>
        <button type="button" class="btn btn-warning xx" onclick="location.href='{$modify_url}';" >修改</button>
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
            $model->left_menu = 1;
            $model->status = 1;
            $model->fid=0;
            $model->sort = 0;
            $model->type = 1;
            $model->module='';
            $model->controller='';
            $model->action='';
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
        $html .= '<option value="0" '.$selected.'>顶级菜单</option>';
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


    public static function getLeftMenu(){
        return LeftMenuHtml::getLeftMenu(UserTool::getUser_id()    );
    }
    
    
    
}
