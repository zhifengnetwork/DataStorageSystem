<?php
namespace app\user\controller;
use think\Controller;
use think\Session;

use app\user\model\User;
use app\admin\model\Auth;
use app\admin\model\Role;

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

            $user = new User();
            $res = $user->login($username,$password);

            if($res === '-2') $this->error('账号或密码不能为空！');
            if($res === '-1') $this->error('账号不存在！');
            if($res === 2) $this->error('账号或密码错误！');
            if($res === 0) $this->error('该账号已停用！');
            
            if($res){
                $role = new Role;
                $role = $role->field('role_name')->find($res['role_id']);
                session('user_id',$res['user_id']);
                session('username',$res['name']);
                session('user_role_id',$res['role_id']);
                session('user_role_name',$role['role_name']);
                session('user_is_login',1);
                
                //添加登录记录
                // admin_log(session('admin_id'));
                $this->success("登录成功",'index/index/index');
                
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
        session('user_id',null);
        session('user_role_id',null);
        session('username',null);
        session('user_is_login',null);
        session('user_role_name',null);
        $this->success( lang('退出成功!') ,'user/login/index',1);
    } 
}
