<?php
namespace app\user\controller;
use think\Controller;
use think\Session;

use app\user\model\Admin;

/**
 * 登录
 */
class Login extends Controller
{
    public function index()
    {

        if($this->request->method() == "POST"){
            $username = input('username');
            $password = input('password');

            $admin = new Admin();
            $res = $admin->login($username,$password);

            if($res){
                
                if(isset($res['admin_id'])){
                    session('admin_id',$res['admin_id']);
                    session('username',$res['username']);
                    //添加登录记录
                    admin_log(session('admin_id'));
                    $this->success("登录成功",'index/index/index');
                }else{
                    $this->error("登录失败");
                    exit;
                }
                
            }else{
                $this->error("登录失败");
                exit;
            }
           
        }

        return $this->fetch();
    }


    /**
     * 自动登录
     */
    public function auto_login(){
        $username = input('name');
        $token = input('token');
        $date = date('Ymd');
        
        $correct_token = md5($date.$username);

        if($correct_token == $token){
            $admin = new Admin();
            $res = $admin->where(['username'=>$username])->find();
            if(!$res){
               $this->error("登录失败");
            }
            if(isset($res['admin_id'])){
                session('admin_id',$res['admin_id']);
                session('username',$res['username']);
                //添加登录记录
                admin_log(session('admin_id'));
                $this->redirect('index/index/index');
            }else{
                $this->error("登录失败");
            }

        }else{
            $this->error("登录失败");
        }


    }


    /**
     * 退出登录
     */
    public function logout(){
        session(null);
        $this->success("退出成功",'index');
    } 
}
