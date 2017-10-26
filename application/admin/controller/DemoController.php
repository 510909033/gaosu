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

    //转化高速收费站站点
    public function testAction(){
        header('content-type;text/html;charset=utf-8');
        $res= Db::query("SELECT * FROM `kw_station`");
        foreach ($res as $key => $value) {
            $Station_Name = iconv("gb2312","utf-8",$value['Station_Name']);
            echo $Station_Name;
            Db::query("SET NAMES utf8");
            $sql = "INSERT INTO station VALUES('".$value['Station_ID']."','".$Station_Name."','','".$value['pinyin']."')";
            $res = Db::execute($sql);
        }
        echo "done";

/*        
        var_dump($data);*/
    }
}
