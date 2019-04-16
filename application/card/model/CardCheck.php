<?php
namespace app\card\model;
use app\card\model\CardList;

class CardCheck extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_card_check';

    /**
     * 获取核销列表
     */
    public static function get_check_list(){
        $admin_id  = session('admin_id');
        $data = self::where(['admin_id'=>$admin_id])
        ->order('id desc')
        ->select();
        if(!$data){
            return [];
        }
        $count = count($data);
        foreach($data as $key => $val){
            $result[] = $data[$key]->data;
        }

        $CardList = new CardList();
        foreach($result as $key => $val){
            $result[$key]['title'] = $CardList->where(['card_id'=>$val['card_id']])->value('title');
            $result[$key]['sub_title'] = $CardList->where(['card_id'=>$val['card_id']])->value('sub_title');

        }

        return $result;
    }
   
}