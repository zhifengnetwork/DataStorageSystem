<?php
namespace app\mobile\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
    /*
    * 初始化操作
    */
    function _initialize() 
    {
       
    }
       
    public function _empty(){
        $this->error('页面不存在');
    }
    
}

