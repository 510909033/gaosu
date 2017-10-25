<?php
use think\Route;

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//Route::resource('user' , 'way/User');
// Route::resource('role' , 'admin/Role');
// Route::resource('menu' , 'admin/Menu');
// Route::resource('rolemenu' , 'admin/RoleMenu');
// Route::resource('usermenu' , 'admin/UserMenu');
// Route::resource('userrole' , 'admin/UserRole');
// Route::resource('adminuser' , 'admin/User');

// Route::resource('adminuser' , 'admin/User' , ['var'=>['adminuser'=>'left_select']] );

// Route::resource('user1' , 'way/User');
return [
    '__pattern__' => [
        'name' => '\w+',
        'id'=>'\d+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],


];

/*

标识	请求类型	生成路由规则	对应操作方法（默认）
index	GET	blog	         index
create	GET	blog/create	     create
save	POST	blog	     save
read	GET	blog/:id	     read
edit	GET	blog/:id/edit	 edit
update	PUT	blog/:id	     update
delete	DELETE	blog/:id	 delete

*/
