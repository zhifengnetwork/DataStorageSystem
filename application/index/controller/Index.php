<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Controller;
use app\picture\model\PictureList;
use app\card\model\CardList;
use app\activity\model\ActivityList;
use app\member\model\MemberList;
use app\admin\model\AdminLog;
use think\Session;
use app\admin\model\Role;
use app\admin\model\Auth;

class Index extends Base
{
    /**
     * 首页
     */
    public function index()
    {
       
        return $this->fetch('',[
            'left'  =>  $this->left(),
        ]);
    }

    public function left(){
        //获取当前登录管理员的角色id
        $role_id = Session::get('user_role_id');
        $role = new Role;
        $auth = new Auth;
        $RoleInfo = $role->find( $role_id );
        $where = "auth_pid=0 AND auth_id IN({$RoleInfo['role_auth_ids']})";
        $TopAuth = $auth->getAuth($where);
        
        $where = "auth_pid!=0 AND auth_id IN({$RoleInfo['role_auth_ids']}) AND is_menu=1";
        $sonAuth = $auth->getAuth($where);
    	
    	return $this->fetch('public/left',[
            'TopAuth'   =>  $TopAuth,
            'sonAuth'   =>  $sonAuth,
        ]);
    }

    /**
     * 欢迎页面
     */
    public function welcome()
    {
       
        $admin_id = session('admin_id');

        $PictureList = new PictureList();
        $count['pic'] = $PictureList->where(['admin_id'=>$admin_id])->count();
        

        $CardList = new CardList();
        $count['card'] = $CardList->where(['admin_id'=>$admin_id])->count();

        $ActivityList = new ActivityList();
        $count['activity'] = $ActivityList->where(['admin_id'=>$admin_id])->count();


        $this->assign('count',$count);

        
        

        return $this->fetch();
    }
}
