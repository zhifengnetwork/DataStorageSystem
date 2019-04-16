<?php
namespace app\card\model;

class CardTemp extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_card_temp';

    /**
     * 存储临时
     */
    public function save_temp_log($admin_id,$post){
        $data = new self;
        $data->admin_id	= $admin_id;
        $data->brand_name = $post['brand_name'];
        $data->title = $post['title'];
        $data->sub_title = $post['sub_title'];
        $data->notice = $post['notice'];
        $data->service_phone = $post['service_phone'];
        $data->description = $post['description'];
        $data->quantity = $post['quantity'];
        $data->custom_url_name = $post['custom_url_name'];
        $data->custom_url = $post['custom_url'];
        $data->custom_url_sub_title = $post['custom_url_sub_title'];
        $data->promotion_url_name = $post['promotion_url_name'];
        $data->promotion_url = $post['promotion_url'];
        $data->deal_detail = $post['deal_detail'];
        $data->save();
    }


    /**
     * 获取临时数据
     */
    public function get_temp_log($admin_id){
        $data = self::where(['admin_id'=>$admin_id])->order('id desc')->find();
        
        //空数据返回
        if(!$data){
            $data['admin_id'] = "";
            $data['brand_name'] = "";
            $data['title'] = "";
            $data['sub_title'] = "";
            $data['notice'] = "";
            $data['service_phone'] = "";
            $data['description'] = "";
            $data['quantity'] = "";
            $data['custom_url_name'] = "";
            $data['custom_url'] = "";
            $data['custom_url_sub_title'] = "";
            $data['promotion_url_name'] = "";
            $data['promotion_url'] = "";
            $data['deal_detail'] = "";
            return $data;
        }

        $res = $data->data;
        
        if(!$res['notice']){
            $res['notice'] = "使用时向服务员出示此券";
        }
        if(!$res['custom_url_name']){
            $res['custom_url_name'] = "立即使用";
        }
        
        return $res;
    }

}