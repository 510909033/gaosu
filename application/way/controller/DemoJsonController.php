<?php

namespace app\way\controller;

use think\Controller;
use app\order\model\WayUserBindCar;
use think\db\Query;

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
        $cars = model('WayUserBindCar')->getAll();



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
      (ID INT PRIMARY KEY   NOT NULL,
      CAR_NUMBER   TEXT     NOT NULL,
      VERSION      TEXT     NOT NULL,
      STATUS       TEXT     NOT NULL,
      VERIFY       TEXT     NOT NULL,
      );
EOF;
        set_time_limit(0);
        $list = $db->execute($sql);

        foreach($cars as $car){
            $data[] =[
            'ID'=>$car['id'],
            'CAR_NUMBER'    =>$car['car_number'],
            'VERSION'       =>$car['qrcode_version'],
            'STATUS'        =>$car['status'],
            'VERIFY'        =>$car['verify'],
            ];
        }
            $db->insert($data)  ;
        
       

        $list = $db->count();
        
        
        dump($list);
        
    }
    
}
