<?php
namespace app\admin\controller;

use think\Controller;//引入控制器
use think\Request;//引入request
use think\Paginator;
use think\Db;
use app\admin\model\User;//引入模型层
header("content-type:text/html;charset=UTF-8");//防乱码

class UserController extends Controller
{
     //表单页面
    public function indexAction()
    {
        $user = new User;//实例化model
        $res = $user->paginate(5);
        
        if(empty($_POST['contents']))
        {
           $_POST['contents'] = "username";

           $contents = $_POST['contents'];

           $search_name = input('search_name');

           $this->assign('res', $res);


            $select = [];
            $select['username'] = "";
            $select['phone'] = "";
            $select['car_number'] = "";
            $select['brand'] = "";

         
      



            $this->assign('select' , $select)  ;

           return $this->fetch('show');

        }
        
    }

   

    public function index1Action()
    {



        $search_name = input('search_name');
        $search = ['query'=>[]];
        $search['query']['search_name'] = $search_name;
        if( input('contents') )
        {
            $contents = input('contents');

             /*dump($contents);
             die;*/
            $res = Db::name('way_user_bind_car')->where("$contents",'like',"%{$search_name}%")->paginate(5,false,[
            'query' => request($contents)->param(),]);
            
            $this -> assign('res',$res);

            $this -> assign('search_name',$search_name);

            $var = [];
            $select = [];
            $select['username'] = "";
            $select['phone'] = "";
            $select['car_number'] = "";
            $select['brand'] = "";

            $select[$contents] ='selected';

            $this->assign('select' , $select)  ;

            return $this->fetch('show');


        }
        
        /*$search_name = input('search_name');
        $search = ['query'=>[]];
        $search['query']['search_name'] = $search_name;
        $res = Db::name('sys_user')->where('phone','like',"%{$search_name}%") ->paginate(5,false,$search);
        $this -> assign('res',$res);
        $this->assign('search_name',$search_name);
        return $this->fetch();*/
    }

    //添加
    public function insert()
    {
        
        $Request=Request::instance();//调用Request中instance方法
        $data=$Request->post();//接值
        //print_r($data);
        $user=new User;//实例化model
        $result=$user->insertData($data);//执行添加语句
        if($result)
        {   
            return Redirect('Index/show');
            //echo "<script>alert('新增成功');localtion.href='{:url('Index/show')}'</script>";
            //$this->success('新增成功','Index/show');
        }else{
            $this->error('新增失败');
        }

    }

   //展示
   /*public function showAction()
   {
     $user=new User;//实例化model
     $arr=$user->show();//执行查询
     return view('show',['arr'=>$arr]);
   }*/

   //删除
   public function delete()
   {
   $Request=Request::instance();
   $id=$Request->get('id');
   $user=new User;
   $result=$user->deleteData($id);
   if($result)
   {
    return Redirect('Index/show');
   }else{
    $this->error('删除失败');
   } 
   }

   //修改页面
   public function update()
   {
    $Request=Request::instance();
    $id=$Request->get('id');
    $user=new User;
    $res=$user->findData($id);//查询单条
    //print_r($res);die;
    return view('update',['res'=>$res]);
   }

   //执行修改
   public function save()
   {
    $Request=Request::instance();
    $id=$Request->post('uid');
    $Request=Request::instance();
    $data=$Request->post();//接修改之后的数据
    $user=new User;
    $result=$user->updateData($data,$id);//修该语句
    if($result)
    {
    return Redirect('Index/show');
    }else{
        $this->error('修改失败');
    }


   }

   public function getDetailAction(){

      $id = $_GET['id'];
      
      if($id == NULL){

        alert('数据异常');

      }else{

         $data = Db::name('way_user_bind_car')->find(array('id'=>$id));

      if ($data['create_time']&&!empty($data['create_time']))
          

            $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);  

            $status = config('status');

            $data['verify'] =  $status[$data['status']];

          echo !empty($data) ? json_encode($data,JSON_UNESCAPED_UNICODE) : json_encode(array());

          //var_dump($data);die;
          
        
      }
     
   }
}

