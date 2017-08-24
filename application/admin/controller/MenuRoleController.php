<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysRole;
use app\admin\model\SysRoleMenu;
use app\admin\model\SysMenu;
use think\Db;
use app\common\tool\ConfigTool;
use app\admin\validate\RoleMenuValidate;

class MenuRoleController extends PublicController
{
    use  \app\common\trait_common\RestTrait;
   protected function _before_save(){
        return [
            'allowField'=>['role_id','menu_id','allow'],
        ];
    }
    
    protected function _before_update(){

    }
    
    protected function _before_delete(){

    }
    
    
    public function editAction($id){
        $vars=[];
        
        $list = SysRole::getList(0);
        $menu = SysMenu::get($id);
        $vars['html']['list']['title'] = '菜单所属角色设置【'.$menu->name.'】';
        $vars['html']['list']['html'] = $this->getIndexHtml($id,$list, 0);
        $vars['form']['submit']['url'] = url('admin/MenuRole/set?id='.$id);
       
        return \view('edit',$vars);
    }
    
    
    private function getIndexHtml($id,$list , $currentLevel){
        $html = '';
        static $level = 0;
        $subfix='';
    
        foreach ($list as $k=>$v){
            $role_id_checkbox = '';
            $allow_checkbox='';
            $roleMenu = SysRoleMenu::get( ['menu_id'=>$id , 'role_id'=>$v['id']] );
            if ($roleMenu){
                $role_id_checkbox = ' checked ';
            
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
    <td>{$v['is_nav']}</td>
    <td>{$v['sort']}</td>
    <td>{$v['desc']}</td>
    <td>
        选中：<input type="checkbox" name="role_id[]"  {$role_id_checkbox} value="{$v['id']}" /> |  
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
    
    
    public function setAction($id){
        $menuId = $id;
        $roleIdArr = $this->request->post('role_id/a');
        $config = $this->getAddConfig();
        $allowField = $config['allowField'];
        $json = [];
        if ($menuId ){
            try {
    
    
                Db::startTrans();
                $existRoleId = [];
                if ($roleIdArr){
                    foreach ($roleIdArr as $role_id){
                        $existRoleId[] = $role_id;
                        
                        if ($this->request->post('allow_'.$role_id)){
                            $allow = 1;
                        }else{
                            $allow = 2;
                        }
                        
                        $data = [
                            'menu_id'=>$menuId,
                            'role_id'=>$role_id,
                            'allow'=>$allow,
                        ];
        
                        //如果存在，则更新
                        $roleMenu = SysRoleMenu::get(['menu_id'=>$menuId,'role_id'=>$role_id,]);
                        
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
                }
    
                $where = '';
                $sysRoleMenu = new SysRoleMenu();

                $sysRoleMenu->destroy(function(\think\db\Query $query) use($menuId,$existRoleId){
                    $query->where('menu_id',$menuId);
                    if ($existRoleId){
                        $query->whereNotIn('role_id', $existRoleId);
                    }
                });
    
                Db::commit();
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '编辑菜单所属角色成功';
                $json['url']['next_page'] = url('admin/Menu/index');
            } catch (\Exception $e) {
                Db::rollback();
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $e->getMessage();
            }
        }else{
            $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
            $json['html'] = '未有数据进行提交';
            $json['debug']['roleId'] = $menuId;
            $json['debug']['menuIdArr'] = $roleIdArr;
    
        }
    
        return json($json);
    
    
    }
}
