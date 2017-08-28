<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class SysFeedBack extends Model
{
	protected $table 	= 'sys_feedback';//表名
	protected $pk 		= "id";


	//增加
	function insertData($data)
	{
		return Db::table($this->table)->insertGetId($data);
	}

	//查询
	public function getAll()
	{
		return Db::table($this->table)->order('create_time', 'desc')->select();

	//分页
	function paginate($pnum)
	{
		return Db::table($this->table)->order('create_time', 'desc')->paginate($pnum);
	} 

	}
	//删除
  	function deleteData($id)
	{
		return Db::table($this->table)->where('id','=',$id)->delete();    
	}

	//查询单条
	function findData($id)
	{
		return Db::table($this->table)->where('id','=',$id)->find();    
	}

	//修改
	function updateData($data,$id)
	{
		return Db::table($this->table)->where('id','=',$id)->update($data); 
	}

	
}