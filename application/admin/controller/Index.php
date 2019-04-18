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
use app\admin\model\Role;

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
        $role_id = Session::get('role_id');
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

        $MemberList = new MemberList();
        $count['member'] = $MemberList->count();

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
            if( !$data['admin_name'] || !$data['password'] || !$data['role_id'] ) $this->error('请填写完整信息！');

            $data['salt'] = mt_rand(0,999999);
            $data['password'] = password($data['password'],$data['salt']);
            if( Db::name('admin')->insert($data) ) {
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }

        return $this->fetch('',[
            'role'  =>  Role::select(),
        ]);
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

            if( !$data['role_id'] ) $this->error('请填写完整信息！');

            if($data['password']){
                $data['password'] = password($data['password'],$info['salt']);
            }else{
                unset($data['password']);
            }
            
            if( Db::name('admin')->update($data,$data['admin_id']) !== false ) {
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }

        return $this->fetch('',[
            'info'  =>  $info,
            'role'  =>  Role::select(),
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
            if( !$admin['status'] ) $this->error('该账号已禁用！');

            $data['password'] = password($data['password'],$admin['salt']);	//加密
            
            if( $data['password'] == $admin['password'] ){
                
                // $role = new RoleModel;
                $role = new Role;
                $role = $role->field('role_name')->find($admin['role_id']);
                //保存管理员登录信息
                Session::set('admin_id' ,$admin['admin_id']);	//管理员ID
                Session::set('admin_name' ,$admin['admin_name']);	//管理员账号
                Session::set('role_id' ,$admin['role_id']);	//当前管理员角色ID
                Session::set('role_name' ,$role['role_name']);	//当前管理员角色名称
                Session::set('is_login',1);

                $this->success('登录成功！',url('admin/index/index'));
            }
            $this->error('账号或密码错误！',url('admin/index/index'));
        }

        return $this->fetch();
    }

    /**
	 * 退出登录
	 */
	public function logout(){
        session('admin_id',null);
        session('role_id',null);
        session('admin_name',null);
        session('role_name',null);
        session('is_login',null);
        $this->success( lang('退出成功!') ,'Admin/Index/login',1);
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
     * 权限批量删除
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


    /**
	 * 角色列表
	 */
    public function role_list(){
        $role = new Role;
    	$list = $role->alias('r')
        // ->join('r LEFT JOIN ts_auth a on FIND_IN_SET(a.auth_id,r.role_auth_ids)')
        ->join('auth a','FIND_IN_SET(a.auth_id,r.role_auth_ids)','LEFT')
        ->group('r.role_id')
        ->field('r.*,GROUP_CONCAT(a.auth_name) as name')
        ->paginate(10); 


        return $this->fetch('',[
            'list'  =>  $list,
            'count'  =>  $role->count(),
        ]);
    }
    /**
     * 添加
     */
    public function role_add(){
    	if( request()->isPost() ){
    		$data = input('post.');
    		//判断值是否为空
    		if( empty($data['role_name']) ){
    			$this->error( lang('角色名称必须填写!') );
    		}

    		$res = Role::insert($data);
    		if( $res ){
    			$this->success( lang('添加角色成功!') );
    		}else{
    			$this->error( lang('添加角色失败!') );
    		}
    	}

        return $this->fetch('',[
        ]);
    }

    /**
     * 修改
     */
    public function role_edit($role_id){
        if( request()->isPost() ){
            $data = input('post.');

            if( Role::update($data) !== false ){
                $this->success( lang('修改成功!') );
            }else{
                $this->error( lang('修改失败!') );
            }
        }

        $info = Role::find($role_id);
        return $this->fetch('',[
            'info'  =>  $info,
        ]);
    }

    /**
     * ajax删除
     */
    public function role_del(){
        if( request()->isAjax() ){
            $data['role_id'] = input('role_id');
            $role = new Role;
            $res = $role->where( 'role_id' ,'=' ,$data['role_id'] )->delete();
            return $res;
        }
    }
    /**
     * 批量删除
     */
    public function role_delAll(){
        if( request()->isPost() ){
            if( input('post.') == null ){
                $this->error( lang('请勾选要批量删除的ID') );
            }
            $role_id = input('post.')['role_id'];
            $res = Role::destroy($role_id);
            if( $res ){
                $this->success( lang('批量删除成功!') );
            }else{
                $this->error( lang('批量删除失败!') );
            }
        }
    }

    /**
     * 分派权限
     */
    public function setauth($role_id){
        $role = new Role;
        $auth = new Auth;
        if( request()->isPost() ){
            $auth_id = isset(input('post.')['auth_id']) ? input('post.')['auth_id'] : '';
            if( $auth_id != '' ){
                $_POST['role_auth_ids'] = implode(',' ,input('post.')['auth_id']);
                
                $current_auth = $auth->getAuth("auth_id IN ({$_POST['role_auth_ids']})");
                $urls = '';//声明一个变量用于存储权限的控制器-方法名
                foreach ($current_auth as $val) {
                    $urls .= $val['url'] . ',';
                }
                $_POST['urls'] = trim($urls,',');
                unset($_POST['auth_id']);
                
            }else{
                $_POST['role_auth_ids'] = '';
                $_POST['urls'] = '';
            }

            $res = $role->update($_POST);
            $this->success( lang('分派权限成功!') );

        }

        //判断,如果角色id为0,则返回上一页
        if( $role_id < 1 ){
            $this->error( lang('非法参数!') );die;
        }

        //根据对应的角色id获取对应的角色信息,并赋值模板中
        $info = $role->find($role_id);

        //获取所有顶级权限
        $topAuth = $auth->getAuth();

        //auth_pid不等于 0 的,都是子权限
        $where = "auth_pid!=0 ";
        $sonAuth = $auth->getAuth($where);
        return $this->fetch('',[
            'info'  =>  $info,
            'topAuth'  =>  $topAuth,
            'sonAuth'  =>  $sonAuth,
        ]);
    }


}
