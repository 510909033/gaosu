<?php
namespace app\test\controller;

class TestController
{
    //
   public function aaAction(){
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

    file_put_contents('demo.txt','$postStr');
	}
  
}
