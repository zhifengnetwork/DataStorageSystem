<?php
namespace app\member\model;


class MemberList extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_member';
    
   
    /**
     * 获取客户列表
     */
    public static function get_member_list($admin_id){
        $data = self::where(['admin_id'=>$admin_id])
        ->order('user_id desc')
        ->select();
        if(!$data){
            return [];
        }
        $count = count($data);
        if($count == 0){
            return [];
        }
        foreach($data as $key => $val){
            $result[] = $data[$key]->data;
        }

        return $result;
    }

    /**
     * 单个人的信息
     */
    public static function get_member($user_id){
        $data = self::where(['user_id'=>$user_id])->find();
        return $data->data;
    }

    /**
     * 
     */
    public function del_member($user_id){
        self::where(['user_id'=>$user_id])->delete();
    }
}