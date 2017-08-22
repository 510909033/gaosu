<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysRoleMenu;
use app\admin\validate\RoleMenuValidate;
use app\admin\model\SysRole;
use app\admin\model\SysMenu;
use think\Db;
use app\common\tool\ConfigTool;
use function think\query;

class RoleMenuController extends Controller
{
    use  \app\common\trait_common\RestTrait;
   protected function _before_save(){
        return [
            'modelname'=>'\\'.SysRoleMenu::class,
            'allowField'=>['role_id','menu_id','allow'],
            'validate'=>new RoleMenuValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.SysRoleMenu::class,
            'allowField'=>['role_id','menu_id','allow'],
            'validate'=>new RoleMenuValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.SysRoleMenu::class,
        ];
    }
    
    
    public function editAction($id){
        $vars=[];
        
        $list = SysMenu::getList(0);
        
        $vars['html']['list']['html'] = $this->getIndexHtml($id,$list, 0);
        $vars['form']['submit']['url'] = url('admin/role_menu/addmenu?id='.$id);
       
        return \view('edit',$vars);
    }
    
    
    private function getIndexHtml($id,$list , $currentLevel){
        $html = '';
        static $level = 0;
        $subfix='';
    
        //权限列表
        foreach ($list as $k=>$v){
            $role_id_checkbox = '';
            $allow_checkbox = '';
            $roleMenu = SysRoleMenu::get(['role_id'=>$id,'menu_id'=>$v['id']]);
            if ($roleMenu){
                $role_id_checkbox = ' checked' ;
                $allow_checkbox = ' checked ';
                if (2 == $roleMenu->allow){
                    $allow_checkbox = '  '; 
                }else if (0 == $roleMenu->allow){
                    $role_id_checkbox = '';
                    $allow_checkbox = '';
                }else if (1 == $roleMenu->allow){
                    
                }else{
                    exception('系统程序错误',ConfigTool::$ERRCODE__SHOULD_NOT_BE_DONE_HERE);
                }
            }
    
            $prefix = str_repeat('&nbsp;', $currentLevel*8);
            $prefix .= $currentLevel==0?'': '└';
    
    
            $name = $prefix.$v['name'].$subfix;
    
    
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
            选中：<input type="checkbox" name="menu_id[]"  {$role_id_checkbox} value="{$v['id']}" /> | 
    </td>
    <td>
                启用/禁用<input type="checkbox" name="allow_{$v['id']}" {$allow_checkbox} value="1" />  
                </td>
</tr>
EEE;
    if ($v['children']){
        $level++;
        $eq = false;
        $html .=$this->getIndexHtml($id,$v['children'],$currentLevel+1);
    }
    }
    return $html;
    }
    
    
    public function addmenuAction($id){
        $roleId = $id;
//         $roleId = $this->request->get('id');
        $menuIdArr = $this->request->post('menu_id/a');
        $config = $this->getAddConfig();
        $allowField = $config['allowField'];
        $json = [];
        if ($roleId && $menuIdArr){
            try {
                
            
            Db::startTrans();
            $existMenuId = [];
            foreach ($menuIdArr as $menu_id){
                $existMenuId[] = $menu_id;
                if ($this->request->post('allow_'.$menu_id)){
                    $allow = 1;
                }else{
                    $allow = 2;
                }
                $data = [
                    'role_id'=>$roleId,
                    'menu_id'=>$menu_id,
                    'allow'=>$allow,
                ];
                
                //如果存在，则更新
                $roleMenu = SysRoleMenu::get(['role_id'=>$roleId,'menu_id'=>$menu_id,]);
                if ($roleMenu){
                    if ($roleMenu->allow != $allow){
                        $return = SysRoleMenu::updateData($roleMenu->id  , $data, new RoleMenuValidate(), $allowField);
                        if (!is_object($return)){
                            exception($return,ConfigTool::$ERRCODE__COMMON);
                        }
                    }
                }else{
                    //不存在，则增加
                    $return = SysRoleMenu::addData($data, new RoleMenuValidate(), $allowField);
                    if (!is_object($return)){
                        exception($return,ConfigTool::$ERRCODE__COMMON);
                    }
                }
            }//end foreach
            
            if ($existMenuId){
                $where = '';
                $roleMenuModel = new SysRoleMenu();
                $updateData = ['allow'=>0];
                
                $roleMenuModel->save($updateData , function(\think\db\Query $query) use($roleId,$existMenuId){
                    $query->where('role_id',$roleId)->whereNotIn('menu_id', $existMenuId);
                });
            }
            
            Db::commit();
            $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
            $json['html'] = '编辑角色权限成功';
            } catch (\Exception $e) {
                Db::rollback();
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $e->getMessage();
            }
        }else{
            $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
            $json['html'] = '未有数据进行提交';
            $json['debug']['roleId'] = $roleId;
            $json['debug']['menuIdArr'] = $menuIdArr;
            
        }
        
        return json($json);
    }
    
}
