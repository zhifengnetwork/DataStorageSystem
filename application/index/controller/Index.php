<?php
namespace app\index\controller;

use app\common\controller\Base;
use think\Controller;
use app\picture\model\PictureList;
use app\card\model\CardList;
use app\activity\model\ActivityList;
use app\member\model\MemberList;
use app\admin\model\AdminLog;

class Index extends Base
{
    /**
     * 首页
     */
    public function index()
    {
       
        $username = session('username');
        $this->assign('username',$username);
        
        
        return $this->fetch();
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

        $CardList = new CardList();
        $count['card'] = $CardList->where(['admin_id'=>$admin_id])->count();

        $ActivityList = new ActivityList();
        $count['activity'] = $ActivityList->where(['admin_id'=>$admin_id])->count();

        $ActivityList = new ActivityList();
        $count['activity'] = $ActivityList->where(['admin_id'=>$admin_id])->count();

        $MemberList = new MemberList();
        $count['member'] = $MemberList->where(['admin_id'=>$admin_id])->count();

        $this->assign('count',$count);

        $this->assign('username',session('username'));

        $AdminLog = new AdminLog();
        $log = $AdminLog->get_last_log($admin_id);
        $this->assign('log',$log);
        
        $login_time = $AdminLog->get_login_time($admin_id);
        $this->assign('login_time',$login_time);

        return $this->fetch();
    }
}
