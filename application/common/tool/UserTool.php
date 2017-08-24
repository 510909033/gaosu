<?php
namespace app\common\tool;
use app\common\model\SysUser;
use think\Session;
use app\admin\model\SysUserMenu;
use think\db\Query;
use app\admin\model\SysUserRole;
use app\admin\model\SysRoleMenu;
use app\admin\model\SysMenu;
use app\admin\model\SysRole;
use think\Db;
use app\admin\validate\MenuValidate;
use app\admin\controller\MenuController;

class UserTool {
    private static $is_login = null;
    private static $user_id=null;
    private static $uni_account = null;
    
    /**
     * @return the $is_login
     */
    public static function getIs_login()
    {
        if (is_null(self::$is_login)){
            self::$is_login = session('is_login');
        }
        return UserTool::$is_login;
    }

    /**
     * @return the $user_id
     */
    public static function getUser_id()
    {
        if (is_null(self::$user_id)){
            self::$user_id = session('user_id');
        }
        return UserTool::$user_id?UserTool::$user_id:0;
    }

    /**
     * @return the $uni_account
     */
    public static function getUni_account()
    {
        if (is_null(self::$uni_account)){
            self::$uni_account = session('uni_account');
        }
        return UserTool::$uni_account?UserTool::$uni_account:'';
    }

    public static function init(SysUser $sysUser){
        Session::boot();
        
        \session('user_id',$sysUser->id);
        \session('uni_account' , $sysUser->uni_account);
        \session('is_login',true);
        
        self::$user_id = $sysUser->id;
        self::$uni_account = $sysUser->uni_account;
        self::$is_login = true;
    }
    
    public static function getAllPrivileges($user_id,&$debug=[]){
        $debug['user_id'] = $user_id;

        $listUserMenu=[];
        $listRoleMenu=[];
        $closure = null;
        
        $closure = function(Query $query) use($user_id){
            $where = [
                'user_id'=>$user_id,
            ];
            $query->where($where);//->whereIn('allow', '1,2');
        };
        $listUserMenu = SysUserMenu::all($closure);
   
        
        //所有用户角色
        // —————— start
        $closure = function(Query $query) use($user_id){
            $where = [
                'user_id'=>$user_id,
            ];
            $query->where($where);//->whereIn('allow', '1,2');
        };
        $listUserRole = SysUserRole::all($closure);
        $debug[] = '1———— 开始循环用户角色列表 -- ';
        if ($listUserRole){
            foreach ($listUserRole as $lineUserRole){
                $debug[] = '——————init--用户包含角色，角色id='.$lineUserRole->role_id;
                $lineRole = SysRole::get($lineUserRole->role_id);
                if ($lineRole){
                    if (1 != $lineRole->status){
                        $debug[]='角色id因状态不是1，跳过，role_id='.$lineUserRole->role_id.',line='.__LINE__;
                        continue;
                    }
                }else{
                    $debug[]='角色id因表中无数据，跳过，role_id='.$lineUserRole->role_id.',line='.__LINE__;
                    continue;
                }
                $closure = function(Query $query) use($lineUserRole){
                    $where = [
                        'role_id'=>$lineUserRole->role_id,
                    ];
                    $query->where($where);
                };
                /**
                 * 角色包含的所有菜单
                 * @var Ambiguous $tmp_list_role_menu
                 */
                $tmp_list_role_menu = SysRoleMenu::all($closure);
                //过滤，将allow=1保留 
                if ($tmp_list_role_menu){
                    foreach ($tmp_list_role_menu as $line_tmp_role_menu){
                        if ( 1 == $line_tmp_role_menu->allow){
                            if (!isset($listRoleMenu[$line_tmp_role_menu->menu_id])){
                                $listRoleMenu[$line_tmp_role_menu->menu_id] = true;
                            }
                        }else {
                            $debug[]='因角色菜单不是允许状态，跳过，role_menu表id='.$line_tmp_role_menu->id.'，allow='.$line_tmp_role_menu->allow.'，file='.__FILE__;
                        }
                    }
                }else{
                    $debug[] = '用户角色不包含菜单，role_id='.$lineUserRole->role_id.',file='.__LINE__;
                }
            }
        }else{
            $debug[]='用户无任何角色,line='.__LINE__;
        }
        $debug[] = '1————循环用户角色列表 --结束  ';
        //—————— end
        
        
        $debug[] = '2———— 开始循环 过滤 角色菜单列表 -- ';
        if ($listRoleMenu){
            foreach ($listRoleMenu as $menu_id=>$_true){
                $closure = function(Query $query) use($user_id,$menu_id){
                    $where = [
                        'user_id'=>$user_id,
                        'menu_id'=>$menu_id
                    ];
                    $query->where($where);
                };
                $lineUserMenu = SysUserMenu::get($closure);
                if ($lineUserMenu && 2 == $lineUserMenu->allow){
                    $debug[]='因用户菜单禁止了此权限，删除此权限，menu表主键id='.$menu_id.',line='.__LINE__;
                    unset($listRoleMenu[$menu_id]);
                }else{
                    //判断菜单是否启用
                    $lineMenu = SysMenu::get($menu_id);
                    if (!$lineMenu){
                        $debug[]='因为菜单表没有此菜单，删除此权限,menu表主键id='.$menu_id.',line='.__LINE__;
                        unset($listRoleMenu[$menu_id]);
                    }else if ( 1 != $lineMenu->status){
                        $debug[]='因为菜单表禁用了此权限，删除此权限,menu表主键id='.$menu_id.',line='.__LINE__;
                        unset($listRoleMenu[$menu_id]);
                    }
                }
            }
        }
        $debug[] = '2————循环 过滤 角色菜单列表 --结束  ';
        
        
        $debug[] = '3———— 开始循环用户菜单列表 -- ';
        if ($listUserMenu){
            foreach ($listUserMenu as $k=>$lineUserMenu){
                if (isset($listRoleMenu[$lineUserMenu->menu_id])  ){
                    if (2 == $lineUserMenu->allow){
                        $debug[]='因用户菜单禁止了此权限，删除此权限，menu表主键id='.$menu_id.',line='.__LINE__;
                        unset($listRoleMenu[$menu_id]);
                    }
                }else{
                    if (1 == $lineUserMenu->allow){
                        $listRoleMenu[$lineUserMenu->menu_id] = true;
                    }else{
                        $debug[]='因用户菜单allow !=1，跳过，user_menu表主键id='.$lineUserMenu->id.'，allow='.$lineUserMenu->allow.',line='.__LINE__;
                    }
                }
            }
        }
        $debug[] = '3————循环用户菜单列表 --结束  ';
        
        $menu=[];
        if ($listRoleMenu){
            foreach ($listRoleMenu as $menu_id=>$_true){
                $menu[] = SysMenu::get($menu_id);
            }
        }
    
        return $listRoleMenu;
    }
    
    
    /**
     * 获取用户所有权限
     * @param unknown $user_id
     * @return false|array
     */
    public static function getAllPrivi($user_id){
        /*
         * 角色权限不会继承
         */
        $sql=<<<EEE
select group_concat(menu_id ) menu_ids from (SELECT * FROM 
(
select sm.id menu_id 
	FROM 
		sys_user_role ur 
	LEFT JOIN  
		sys_role sr 
	ON 
		ur.role_id = sr.id 
	LEFT JOIN 
		sys_role_menu  srm
	ON 
		sr.id=srm.role_id 
	LEFT JOIN 
		sys_menu sm 
	ON 
		srm.menu_id=sm.id 
	LEFT JOIN 
		sys_user_menu sum 
	ON 
		sm.id=sum.menu_id
	
	WHERE 
		ur.user_id={$user_id}
		AND 
		sr.status=1 
		AND 
		srm.allow=1
		AND 
		sm.status=1
		AND 
		( sum.id is null OR sum.allow=1 ) 
		
	UNION 
		select menu_id FROM sys_user_menu 
		where 
			user_id={$user_id} 
			AND 
			allow=1
) as a	 ) as b       
EEE;
		//管理员
        if ($user_id == ConfigTool::ADMIN_ID){
            $sql="select group_concat(id ) menu_ids from sys_menu ";
        }
        
		$list = Db::query($sql );
        
        return isset($list[0]) && isset($list[0]['menu_ids']) ?explode(',', $list[0]['menu_ids']):false ;
    }
    
    public static function isPrivi($user_id='',$module='',$controller='',$action=''){
        $user_id = $user_id?$user_id:self::getUser_id();
//         //管理员
        if ($user_id == ConfigTool::ADMIN_ID){
            return true;
        }
        
        $module = $module?$module:\request()->module();
        $controller = $controller?$controller:\request()->controller();
        $action = $action?$action:\request()->action();
        
        $where=[];
        
        $where = [
            'module'=>$module,
            'controller'=>$controller,
            'action'=>$action,
            'status'=>1,
        ];
        $line = SysMenu::get($where);
        
        if($line ){
            return false === array_search($line->id, self::getAllPrivi($user_id))?false:true;
        }else{
            $data=$where;
            $data['status'] = 1;
            $data['fid']=0;
            $data['name'] = implode('-', $where);
            $data['left_menu'] = 0;
            $validate = new MenuValidate();
//             $contro = new MenuController();
//             $config = $contro->getAddConfig();
//             $allowField = $config['allowField'];
            
            $allowField =['name','fid','status','module','controller','action','left_menu','sort','type'];
            
            SysMenu::addData($data, $validate, $allowField);
        }
        return false;
    }

    
}