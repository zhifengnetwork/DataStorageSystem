<?php
namespace app\picture\model;


class PictureList extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_picture';
    
    /**
     * 获取客户列表
     */
    public static function get_picture_list(){
        $admin_id  = session('admin_id');
        $data = self::where(['admin_id'=>$admin_id])
        ->select();
        if(!$data){
            return [];
        }
        $count = count($data);
        foreach($data as $key => $val){
            $result[] = $data[$key]->data;
        }
        return $result;
    }

    /**
     * 单个人的信息
     */
    public static function get_member($user_id){
        return self::where(['user_id'=>$user_id])->find();
    }
}