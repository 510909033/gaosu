<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class demoController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
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

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }


    public function testAction(){
        $res= Db::query("SELECT * FROM `kw_station` WHERE Station_ID!=311 or Station_ID!='31A'");
        foreach ($res as $key => $value) {
            $sql='UPDATE kw_station SET Station_Name="'.iconv("gb2312", "utf-8", $value['Station_Name']).'"WHERE Station_ID="'.$value['Station_ID'].'"';           
            $res = Db::execute($sql);
        }
        echo "done";

/*        $data = iconv("gb2312","utf-8",$res[0]['Station_Name']);
        var_dump($data);*/
    }
}
