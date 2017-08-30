<?php
namespace app\admin\controller;

use think\Controller;//引入控制器
use think\Request;//引入request
use think\Paginator;
use think\Db;
use app\admin\model\User;//引入模型层

use app\common\tool\Verifier;
use app\common\tool\AjaxTool;

header("content-type:text/html;charset=UTF-8");//防乱码

class UserListController extends Controller
{
     //表单页面
    public function indexAction()
    {
        $user = new User;//实例化model

        $res = $user->order('verify asc')->paginate(5);
        
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

            $res = Db::name('way_user_bind_car')->order('verify asc')->where("$contents",'like',"%{$search_name}%")->paginate(5,false,['query' => request($contents)->param(),]);
            
            $this -> assign('res',$res);

            $this -> assign('search_name',$search_name);

            $var = [];
            $select = [];
            $select['username'] = "";
            $select['phone'] = "";
            $select['car_number'] = "";
            $select['brand'] = "";

            $select[$contents] ='selected';

            $this->assign('select' , $select);

            return $this->fetch('show');

        }

    }

    //添加
    public function insertAction()
    {
        
        $Request = Request::instance();//调用Request中instance方法
        $data = $Request->post();//接值
     
        $user = new User;//实例化model
        $result = $user->insertData($data);//执行添加语句
       
        if($result)
        {   
            //return Redirect('UserList/index');
            //echo "<script>alert('新增成功');localtion.href='{:url('Index/show')}'</script>";
            $this->success('新增成功','UserList/show');
        }else{
            $this->error('新增失败');
        }

    }

   //删除
   public function deleteAction()
   {
       $Request=Request::instance();
       $id=$Request->get('id');
       $user=new User;
       $result=$user->deleteData($id);
       if ($result) 
          AjaxTool::outputDone($result);
       else
          AjaxTool::outputError('失败');
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
   public function saveAction()
   {
        $Request = Request::instance();

        $id = $Request->post('id');

        
    
        $Request = Request::instance();

        $data = $Request->post();//接修改之后的数据

        $user = new User;

        

        $result = $user->updateData($data,$id);//修该语句


        if($result)
        {
          echo "<script>
          alert('新增成功');
          window.history.back(-1);
          </script>";
        //$this->success('提交成功','UserList/index');

        }else{

           echo "<script>
          alert('新增失败');
          return false;
          </script>";
        }
   }

   //AJAX传值
   public function getDetailAction(){

      $id = $_GET['id'];
      
      if($id == NULL){

        alert('数据异常');

      }else{

         $data = Db::name('way_user_bind_car')->find(array('id'=>$id));

      if ($data['create_time']&&!empty($data['create_time']))     

            $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);  

            /*$verify = config('verify');

            $status = config('status');

            $data['verify'] =  $verify[$data['verify']];

            $data['status'] =  $status[$data['status']];*/

            echo !empty($data) ? json_encode($data,JSON_UNESCAPED_UNICODE) : json_encode(array());

          //var_dump($data);die;

      }

    }

//更新状态

    public function updatestateAction(){

        $verify = Verifier::validation($_POST, array(
          array('name:id' => '数据id','name:status'=>'状态'),
          array('id','noempty'),
          array('id,status','required'),
          array('id,status', 'int'),
        )); 
        if (true !== $verify)
          AjaxTool::outputError($verify['msg']);

        $data['status'] = ($_POST['status']==1) ? 0 : 1; 

        $res = model('User')->updateData($data,$_POST['id']);

        if ($res) 
          AjaxTool::outputDone($res);
        else
          AjaxTool::outputError('失败');

    }
  

}

