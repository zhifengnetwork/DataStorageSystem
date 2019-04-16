<?php
namespace app\common\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
    /*
    * 初始化操作
    */
    function _initialize() 
    {
        $admin_id = session('admin_id');
      
        if(!$admin_id){
            $this->redirect('user/login/index');
        }
    }


    /**
     * 空
     */
    // public function _empty(){
    //     $this->error('页面不存在');
    // }
    
}

