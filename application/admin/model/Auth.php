<?php
namespace app\admin\model;


class Auth extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_auth';

    // 获取权限
	public function getAuth($where = "auth_pid=0" ){
		// auth_pid为0的才是顶级权限
		return $this->where( $where )->order('sort','DESC')->select();
	}
}