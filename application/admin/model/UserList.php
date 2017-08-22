<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class UserList extends Model
{
	protected $table='way_user_bind_car';//表名


	//增加
	function insertData($data)
	{
		return Db::table($this->table)->insertGetId($data);
	}

	//查询
	function show()
	{
		return Db::table($this->table)->select();

	//分页
	function paginate()
	{
		return Db::table($this->table)->paginate();
	} 

	}
	//删除
  	function deleteData($id)
	{
		return Db::table($this->table)->where('uid','=',$id)->delete();    
	}

	//查询单条
	function findData($id)
	{
		return Db::table($this->table)->where('uid','=',$id)->find();    
	}

	//修改
	function updateData($data,$id)
	{
		return Db::table($this->table)->where('uid','=',$id)->update($data); 
	}

	
}