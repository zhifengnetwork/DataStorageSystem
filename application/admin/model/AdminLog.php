<?php
namespace app\admin\model;


class AdminLog extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_admin_log';

    public function get_last_log($admin_id){
        $data = self::where(['admin_id'=>$admin_id])->find();
        return $data->data;
    }

    public function get_login_time($admin_id){
        $data = self::where(['admin_id'=>$admin_id])->count();
        return $data;
    }
}