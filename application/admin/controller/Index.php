<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\Db;
use think\Session;
use app\picture\model\PictureList;
use app\card\model\CardList;
use app\activity\model\ActivityList;
use app\member\model\MemberList;
use app\admin\model\Auth;

class Index extends Base
{

    /**
     * 首页
     */
    public function index()
    {
       
        $admin_name = session('admin_name');
        $this->assign('admin_name',$admin_name);
        
        
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

        $ActivityList = new ActivityList();
        $count['activity'] = $ActivityList->where(['admin_id'=>$admin_id])->count();

        $MemberList = new MemberList();
        $count['member'] = $MemberList->where(['admin_id'=>$admin_id])->count();

        $this->assign('count',$count);

        $this->assign('admin_name',session('admin_name'));


        return $this->fetch();
    }


    public function admin_list()
    {
        if( request()->isAjax() ){
            //接收所有传过来的post数据
            $datatables = request()->post();
            //得到排序的方式
            $order = $datatables['order'][0]['dir'];
            //得到limit参数
            $limit_start = $datatables['start'];
            $limit_length = $datatables['length'];
            //得到搜索的关键词
            $search = $datatables['search']['value'];
            $where = [];
            if($search){
                $where['admin_name'] = ['like', "%{$search}%"];
            }
            
            $data = Db::name('admin')
                    ->where($where)
                    ->order("admin_id $order")
                    ->limit( $limit_start,$limit_length )
                    ->select();
            $cnt = Db::name('admin')->where($where)->count();
            
            $list = [
                'draw'=> request()->post('draw'), // ajax请求次数，作为标识符
                'recordsTotal'=>count($data),  // 获取到的结果数(每页显示数量)
                'recordsFiltered'=>$cnt,       // 符合条件的总数据量
                'data'=>$data,
            ];
            return json( $list );
        }

        return $this->fetch();
    }

    /*
     * 添加管理员
     */
    public function admin_add()
    {   
        if( request()->isPost() ){
            $data = input('post.');
            if( !$data['admin_name'] || !$data['password'] ) $this->error('请填写完整信息！');

            $data['salt'] = mt_rand(0,999999);
            $data['password'] = password($data['password'],$data['salt']);
            if( Db::name('admin')->insert($data) ) {
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }

        return $this->fetch();
    }

    /*
     * 修改管理员
     */
    public function admin_edit()
    {   
        $admin_id = input('admin_id');
        if(!$admin_id) $this->error('参数错误！');
        $info = Db::name('admin')->find($admin_id);

        if( request()->isPost() ){
            $data = input('post.');

            if($data['password']){
                $data['password'] = password($data['password'],$info['salt']);
            }
            
            if( Db::name('admin')->update($data,$data['admin_id']) !== false ) {
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }

        return $this->fetch('',[
            'info'  =>  $info,
        ]);
    }

    /*
     * 删除管理员
     */
    public function del(){
        $admin_id = input('id');
        if(!$admin_id){
            returnJson(100,'参数错误');
        }
        $info = Db::name('admin')->find($admin_id);
        if(!$info){
            returnJson(100,'参数错误');
        }
        // if( Db::name('document')->where('cat_id',$admin_id)->find() ){
        //     returnJson(100,'该类别含有文档，不能删除');
        // }

        if( Db::name('admin')->where('cat_id',$admin_id)->delete() ){
            returnJson([],'删除管理员成功！');
        }

    }

    /**
     * ajax显示隐藏
     */
    public function admin_status(){
        if( request()->isAjax() ){
            $data['admin_id'] = input('id');
            $data['status'] = input('status');

            if( !$data['admin_id'] ){
                return json(['status'=>0,'info'=>lang('参数错误!')]);
            }

            if( $data['status'] ){
                $status = ['status'=>1,'info'=>lang('显示成功!')];
            }else{
                $status = ['status'=>1,'info'=>lang('隐藏成功!')];
            }

            $info = Db::name('admin')->find($data['admin_id']);
            if( !$info ){
                return json(['status'=>0,'info'=>lang('显示或隐藏失败!')]);
            }
            
            $res = Db::name('admin')->update($data,$data['admin_id']);
            return json($status);;
        }
    }

    
    public function login(){

        if( request()->isPost() ){

            $data = input('post.');
            if( !$data['admin_name'] || !$data['password'] ) $this->error('账户或密码不能为空！');

            $admin = Db::name('admin')->where('admin_name','=',$data['admin_name'])->find();

            if( !$admin ) $this->error('用户不存在！');

            $data['password'] = password($data['password'],$admin['salt']);	//加密
            if( $data['password'] == $admin['password'] ){
                
                // $role = new RoleModel;
                // $role = $role->field('role_name')->find($user['role_id']);
                //保存管理员登录信息
                Session::set('admin_id' ,$admin['admin_id']);	//管理员ID
                Session::set('admin_name' ,$admin['admin_name']);	//管理员账号
                // Session::set('role_id' ,$admin['role_id']);	//当前管理员角色ID
                // Session::set('role_name' ,$role['role_name']);	//当前管理员角色名称
                // Session::set('login_ip' ,$admin['login_ip']);	//上次登录IP
                // Session::set('login_time' ,$admin['login_time']);	//上次登录时间
                Session::set('is_login',1);

                $this->success('登录成功！',url('admin/index/index'));
            }
            $this->error('账号或密码错误！',url('admin/index/index'));
        }

        return $this->fetch();
    }
    
    /**
     * 权限列表
     */
    public function auth_list(){
        $list = Db::name('auth')->field('auth_id id ,auth_name ,auth_pid pid ,url,sort')->order('sort','DESC')->select();
        $list = getTree( $list );
        return $this->fetch('',[
            'list'  =>  $list,
        ]);
    }

    /**
     * 权限添加
     */
    public function auth_add(){ 
        $auth = new Auth;
        if( request()->isPost() ){
            $data = input('post.');
            

            $res = $auth->save($data);
            if( $res ){
                $this->success( lang('添加权限成功!') );
            }else{
                $this->error( lang('添加权限失败!') );
            }
        }

        // 获得所有的顶级权限,并赋值到模板中
        
        $topAuth = $auth->getAuth();
        return $this->fetch('',[
            'topAuth'  =>  $topAuth,
        ]);
    }

    /**
     * 修改权限
     */
    public function auth_edit(){
        $auth = new Auth;
        if( request()->isPost() ){
            $data = input('post.');
            
            $res = $auth->update($data);
            if( $res ){
                $this->success( lang('修改权限成功!') );
            }else{
                $this->error( lang('修改权限失败!') );
            }
        }
        
        $auth_id = input('id');
        $info = $auth->get($auth_id);

        $topAuth = $auth->getAuth();

        return $this->fetch('',[
            'topAuth'  =>  $topAuth,
            'info'  =>  $info,
        ]);
    }

    /**
     * ajax删除
     */
    public function auth_del(){
        if( request()->isAjax() ){
            $data['auth_id'] = input('id');
            $auth = new Auth;
            return $auth->where( 'auth_id' ,'=' ,$data['auth_id'] )->delete();
        }
    }

    /**
     * 批量删除
     */
    public function auth_delAll(){
        if( request()->isPost() ){
            if( input('post.') == null ){
                $this->error( lang('请勾选要批量删除的ID') );
            }
            $auth_id = input('post.')['id'];
            $auth = new Auth;
            $res = $auth->destroy($auth_id);
            if( $res ){
                $this->success( lang('批量删除成功!') );
            }else{
                $this->error( lang('批量删除失败!') );
            }
        }
    }

}
