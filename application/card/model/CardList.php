<?php
namespace app\card\model;

class CardList extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_card_list';

    public function get_detail_by_id($id){
        if(!$id){
            return [];
        };
        $data = self::where(['id'=>$id])->find();
        return $data->data;
    }


    public function get_detail($card_id){
        if(!$card_id){
            return [];
        };
        $data = self::where(['card_id'=>$card_id])->find();
        return $data->data;
    }

    /**
     * get_admin_id_by_card_id
     */
    public function get_admin_id_by_card_id($card_id){
        $data = self::where(['card_id'=>$card_id])->value('admin_id');
        return $data;
    }

    /**
     * 删除
     */
    public function delete_by_id($card_id){
        self::where(['card_id'=>$card_id])->delete();
    }
    
}