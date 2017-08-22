<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\SysUserRole;
use app\admin\validate\UserRoleValidate;
use app\admin\model\SysRole;
use think\Db;
use app\common\tool\ConfigTool;
use app\common\model\SysUser;

class UserRoleController extends Controller
{
    use  \app\common\trait_common\RestTrait;
   protected function _before_save(){
        return [
            'modelname'=>'\\'.SysUserRole::class,
            'allowField'=>['user_id','role_id'],
            'validate'=>new UserRoleValidate(),
        ];
    }
    
    protected function _before_update(){
        return [
            'modelname'=>'\\'.SysUserRole::class,
            'allowField'=>['user_id','role_id'],
            'validate'=>new UserRoleValidate(),
        ];
    }
    
    protected function _before_delete(){
        return [
            'modelname'=>'\\'.SysUserRole::class,
        ];
    }
    
    
    
    public function editAction($id){
        $vars=[];
        
        $list = SysRole::getList(0);
        
        $vars['html']['list']['html'] = $this->getIndexHtml($id,$list, 0);
        $vars['form']['submit']['url'] = url('admin/user_role/addrole?id='.$id);
       
        return \view('edit',$vars);
    }
    
    private function getIndexHtml($id,$list , $currentLevel){
        $html = '';
        static $level = 0;
        $subfix='';
    
        foreach ($list as $k=>$v){
            $role_id_checkbox = '';
            $userRole = SysUserRole::get( ['user_id'=>$id , 'role_id'=>$v['id']] );
            if ($userRole){
                $role_id_checkbox = ' checked ';
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
    
    public function addroleAction($id){
        $userId = $id;
        //         $userId = $this->request->get('id');
        $roleIdArr = $this->request->post('role_id/a');
        $config = $this->getAddConfig();
        $allowField = $config['allowField'];
        $json = [];
        if ($userId && $roleIdArr){
            try {
        
        
                Db::startTrans();
                $existRoleId = [];
                foreach ($roleIdArr as $role_id){
                    $existRoleId[] = $role_id;
                    $data = [
                        'user_id'=>$userId,
                        'role_id'=>$role_id,
                    ];
        
                    //如果存在，则更新
                    $userMenu = SysUserRole::get(['user_id'=>$userId,'role_id'=>$role_id,]);
                    if (!$userMenu){
                        //不存在，则增加
                        $return = SysUserRole::addData($data, new UserRoleValidate(), $allowField);
                        if (!is_object($return)){
                            exception($return,ConfigTool::$ERRCODE__COMMON);
                        }
                    }
                }//end foreach
        
                if ($existRoleId){
                    $where = '';
                    $userMenuModel = new SysUserRole();
                    $updateData = ['allow'=>0];
        
                    $userMenuModel->destroy(function(\think\db\Query $query) use($userId,$existRoleId){
                        $query->where('user_id',$userId)->whereNotIn('role_id', $existRoleId);
                    });
                }
        
                Db::commit();
                $json['errcode'] = ConfigTool::$ERRCODE__NO_ERROR;
                $json['html'] = '编辑用户所属角色成功';
            } catch (\Exception $e) {
                Db::rollback();
                $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
                $json['html'] = $e->getMessage();
            }
        }else{
            $json['errcode'] = ConfigTool::$ERRCODE__COMMON;
            $json['html'] = '未有数据进行提交';
            $json['debug']['roleId'] = $userId;
            $json['debug']['menuIdArr'] = $roleIdArr;
        
        }
        
        return json($json);
        
        
    }
    
    
}
