<?php

namespace app\way\controller;

use think\Controller;
//use app\common\model\WayUserBindCar;
use think\db\Query;
use app\way\model\WayUserBindCar;

class DemoJsonController
{

    public function getListAction(){
        
        $p = input('p',1);
        $pagesize = input('pagesize',20);
        
        $closure = function(Query $query )use ($p,$pagesize){
            $order = [
                'id'=>'desc',  
            ];
            $where = [
                
            ];
            $query->page($p,$pagesize)->order($order)->where($where);
        };
        
        $list = WayUserBindCar::all($closure);
        
        $arr = [];
        foreach ($list as $k=>$v){
            $arr[] = $v->toArray();
        }

        
        return json($arr);
        
    }
    
    public function sqliteAction(){
        //绑定信息数据
        $data = model('WayUserBindCar')->getAll();
        var_dump($data);die();



        $config = [
    // 数据库类型
    'type'           => 'sqlite',
    // 服务器地址
    'hostname'       => '127.0.0.1',
    // 数据库名
    'database'       => '/a.db',
    // 用户名
    'username'       => 'root',
    // 密码
    'password'       => 'root',
    // 端口
    'hostport'       => '3306',
    // 连接dsn
    'dsn'            => '',
    // 数据库连接参数
    'params'         => [],
    // 数据库编码默认采用utf8
    'charset'        => 'utf8',
    // 数据库表前缀
    'prefix'         => '',
    // 数据库调试模式
    'debug'          => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'         => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'    => false,
    // 读写分离后 主服务器数量
    'master_num'     => 1,
    // 指定从服务器序号
    'slave_no'       => '',
    // 是否严格检查字段是否存在
    'fields_strict'  => true,
    // 数据集返回类型 array 数组 collection Collection对象
    'resultset_type' => 'array',
    // 是否自动写入时间戳字段
    'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'    => false,
];
        $db = \db('company' , $config );
        $db = \db('test_insert');
        $sql =<<<EOF
      CREATE TABLE COMPANY
      (ID INT PRIMARY KEY     NOT NULL,
      NAME           TEXT    NOT NULL,
      AGE            INT     NOT NULL,
      ADDRESS        CHAR(50),
      SALARY         REAL);
EOF;
        set_time_limit(1200);
         $list = $db->execute($sql);
        for ($i=5000;$i<15000;$i++){
            $data = [
                'ID'=>$i,
                'NAME'=>'ASDF',
                'AGE'=>12,
                'ADDRESS'=>'SDFSDF',
                'SALARY'=>22.22
                
            ];
            $db->insert($data)  ;
        }
        
       

        $list = $db->count();
        
        
        dump($list);
        
    }
    
}
