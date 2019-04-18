<?php
namespace app\member\controller;
use app\common\controller\Base;
use app\member\model\MemberList;
use think\Db;
use app\admin\model\Role;

class Index extends Base
{
    /*
    * 初始化操作
    */
    function _initialize() 
    {
        parent::_initialize();
    }

    /**
     * 列表
    */
    public function index()
    {
        $Member = new MemberList();
        $data = $Member->get_member_list();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function del()
    {

        return $this->fetch();
    }

    public function add()
    {
        $MemberList = new MemberList();
        if($this->request->method() == "POST"){
            $name = input('name');
            $sex = input('sex');
            $mobile = input('mobile');
            $beizhu = input('beizhu');

            $admin_id = session("admin_id");
            
            $MemberList->admin_id = $admin_id;
            $MemberList->name = $name;
            $MemberList->sex = $sex;
            $MemberList->mobile = $mobile;
            $MemberList->status = 1;
            $MemberList->beizhu = $beizhu;
            $MemberList->save();

            $this->success('添加成功','add');
            exit;
        }

        return $this->fetch('',[
            'role'  =>  Role::select(),
        ]);
    }


    /**
     * 编辑
     */
    public function edit()
    {
        $MemberList = new MemberList();
        $user_id = input('user_id');
        $this->assign('user_id',$user_id);
        if(!$user_id){
            $this->error('user_id不存在');
        }
        $data = $MemberList->get_member($user_id);
        $this->assign('data',$data);
            
        if($this->request->method() == "POST"){
            $name = input('name');
            $sex = input('sex');
            $mobile = input('mobile');
            $beizhu = input('beizhu');

            $admin_id = session("admin_id");
            $MemberList->where(['user_id'=>$user_id,'admin_id'=>$admin_id])->update(['name'=>$name,'sex'=>$sex,'mobile'=>$mobile,'beizhu'=>$beizhu]);

            $this->success('修改成功','edit?user_id='.$user_id);
            exit;
        }
        return $this->fetch('',[
            'role'  =>  Role::select(),
        ]);
    }

    /**
     * 修改密码
     */
    public function edit_pwd(){
        if( request()->isPost() ){
            $data = input('post.');
            if(!$data['user_id'] || !$data['pwd'] ) $this->error('参数错误！');
            $info = Db::name('member')->find($data['user_id']);
            if(!$info) $this->error('参数错误！');
            $data['pwd'] = pwd($data['pwd'],$info['salt']);
            Db::name('member')->where(['user_id'=>$data['user_id']])->update(['pwd'=>$data['pwd']]);
            $this->success('修改成功');
        }
        return $this->fetch();
    }

    /**
     * 删除用户
     */
    public function del_member(){
        // /index.php/member/index/del_member?user_id=
        $user_id = input('user_id');

        $MemberList = new MemberList();
        $MemberList->del_member($user_id);

        echo "删除成功";

    }

    public function level()
    {

        return $this->fetch();
    }

    public function scoreoperation()
    {

        return $this->fetch();
    }

    public function record_browse()
    {

        return $this->fetch();
    }

    public function record_download()
    {

        return $this->fetch();
    }

    public function record_share()
    {

        return $this->fetch();
    }

    /**
     * 单个人的信息
     */
    public function show()
    {
        $user_id = input('user_id');
        $Member = new MemberList();
        $data = $Member->get_member($user_id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 处理状态
     */
    public function handle(){
        $user_id = input('user_id');
        $status = input('status');
        $Member = new MemberList();
        $Member::where(['user_id'=>$user_id])->update(['status'=>$status]);
    }

}
