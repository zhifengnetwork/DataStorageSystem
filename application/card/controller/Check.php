<?php
namespace app\card\controller;
use app\common\controller\Base;
use think\Db;

use app\picture\model\PictureList;
use app\card\model\CardList;
use app\card\model\CardCheck;

class Check extends Base
{
    
    public function index()
    {

        return $this->fetch();
    }

    public function post()
    {
        $code = input('code');
        $deal_detail = input('deal_detail');

        $data = array(
            "code" => $code
        );
        $admin_id = session('admin_id');
        $access_token = access_token($admin_id);
        if(!$access_token){
            $this->error("access_token未配置，或配置错误，请联系管理员",'system/index/base');
            exit;
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        //$url = "https://api.weixin.qq.com/card/code/get?access_token=".$access_token;
        $url = "https://api.weixin.qq.com/card/code/consume?access_token=".$access_token;
        $response = PostRequest($url,$json);
        $res = json_decode($response,true);
     
      

        if($res['errcode'] == 0){
            //核销成功
            $CardCheck = new CardCheck();
            $CardCheck->admin_id = $admin_id;
            $CardCheck->openid = $res['openid'];
            $CardCheck->errmsg = $res['errmsg'];
            $CardCheck->card_id = $res['card']['card_id'];
            $CardCheck->deal_detail = $deal_detail;
            $CardCheck->save();

            $this->assign('status',1);
            $this->assign('msg','核销成功');

        }else{

            $CardCheck = new CardCheck();
            $CardCheck->admin_id = $admin_id;
            $CardCheck->errmsg = $res['errmsg'];
            $CardCheck->deal_detail = $deal_detail;
            $CardCheck->save();

            $this->assign('status',0);
            if($res['errcode'] == 40099){
                $this->assign('msg',"核销失败，此优惠券已经使用");
            }else{
                $this->assign('msg',$res['errmsg']);
            }
        }

        return $this->fetch();
    }


    /**
     * 已经核销列表
     */
    public function has_check(){
      
        $CardCheck = new CardCheck();
        $data = $CardCheck->get_check_list();
        $this->assign('data',$data);

        return $this->fetch();
    }


    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        if(!$id){
            return '';
        }
        $CardCheck = new CardCheck();
        $res = $CardCheck->where(['id'=>$id])->delete();
        $return_arr = array(['status'=>1,'msg'=>'删除成功']);
        ajaxReturn($return_arr);
    }

}
