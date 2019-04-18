<?php
namespace app\common\controller;
use think\Controller;
use think\Session;
use think\Config;

class Base extends Controller
{
    /*
    * 初始化操作
    */
    function _initialize() 
    {
		$this->checkLogin();
		
		
	}
	
	//登录检测
	public function checkLogin(){
		$m = request()->module();
		$c = request()->controller();
		$a = request()->action();
		$action = array('admin/index/login','user/login/index');
		$res = !in_array( strtolower( "$m/$c/$a" ), $action);
		// 验证用户登录状态
		if( $res && Session::has('is_login') != 1  ){
			if( strtolower( "$m" ) == 'admin' ){
				$this->redirect( url('Admin/Index/login') );
			}

			if( $res && Session::has('user_is_login') != 1  ){
				$this->redirect( url('user/login/index') );
			}

		}
	}

    //文档处理
	public function public_doc($doc_path='',$file_name='',$filen='',$name=''){


		$file_name = $file_name == '' ? $doc_path : $file_name;
		
		$filen = $filen == '' ? $file_name : $filen;
	    $file = request()->file($file_name);
	    $data=$_POST;
		
		$name = date('YmdHis').'_'.$file_name;

	    if(isset($file)){
	        // 移动到框架应用根目录/public/uploads/ 目录下
	        if(!empty($doc_path)){
	        	if($doc_path == $filen){
	        		$info = $file->move(ROOT_PATH . Config('c_pub.doc') .$filen,$name);
	        	}else{
	            	$info = $file->move(ROOT_PATH . Config('c_pub.doc') . $doc_path .DS .$filen,$name);
	        	}
	        }else{
	            $info = $file->move(ROOT_PATH . Config('c_pub.doc').$file_name,$name);
	        }
	        
	        if($info){
	        	// 成功上传后 获取上传信息
	            $data['url'] = $info->getSaveName();
	            if($doc_path == $filen){
	        		return $doc_path .'/' . $data['url'];
	        	}else{
	            	return $file_name . '/' . $data['url'];
	        	}
	        }else{
	            // 上传失败获取错误信息
	        	echo $file->getError();
	        }
	    }
	}

    /**
     * 空
     */
    // public function _empty(){
    //     $this->error('页面不存在');
    // }
    
}

