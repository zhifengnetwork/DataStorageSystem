<?php
namespace app\card\controller;
use app\common\controller\Base;
use think\Db;

use app\picture\model\PictureList;
use app\card\model\CardTemp;
use app\card\model\CardList;


class Index extends Base
{
    
    /**
     * 团购券
    */
    public function groupon()
    {
        $admin_id = session('admin_id');
        $data = Db::name('crm_card_list')->where(['admin_id'=>$admin_id,'type'=>'GROUPON'])->select(); 
        $this->assign('data',$data);
        $this->assign('count',count($data));

        return $this->fetch();
    }

    /**
     * 优惠券
     */
    public function general_groupon()
    {
        $admin_id = session('admin_id');
        $data = Db::name('crm_card_list')->where(['admin_id'=>$admin_id,'type'=>'GENERAL_COUPON'])->select(); 
        $this->assign('data',$data);
        $this->assign('count',count($data));

        return $this->fetch();
    }
    

    public function del()
    {
        
        $res = change_time($oldtime);

    }

    /**
     * 添加团购券
     */
    public function add_groupon()
    {
        $PictureList = new PictureList();
        $picture = $PictureList::get_picture_list();
        $this->assign('picture',$picture);

        $admin_id = session('admin_id');
        //加载临时文件
        $CardTemp = new CardTemp();
        $data = $CardTemp->get_temp_log($admin_id);
        $this->assign('data',$data);

        return $this->fetch();
    }

    public function add_general_groupon()
    {
        $PictureList = new PictureList();
        $picture = $PictureList::get_picture_list();

        $admin_id = session('admin_id');
        //加载临时文件
        $CardTemp = new CardTemp();
        $data = $CardTemp->get_temp_log($admin_id);
        $this->assign('data',$data);

        $this->assign('picture',$picture);
        return $this->fetch();
    }

    /**
     * 表单提交
     */
    public function add_groupon_post(){

        $admin_id = session('admin_id');
        
        if($this->request->method() == "POST"){
            $access_token = access_token($admin_id);
            if(!$access_token){
                $this->error("access_token未配置，或配置错误，请联系管理员",'system/index/base');
                exit;
            }

            $base_info = input('');

            $CardTemp = new CardTemp();
            $CardTemp->save_temp_log($admin_id,$base_info);

            $base_info['sku']['quantity'] = (int)$base_info['quantity'];
            unset($base_info['quantity']);

            $card['card_type'] = "GROUPON";
        
            $base_info['date_info']['type'] = "DATE_TYPE_FIX_TIME_RANGE";
            $datemin = $base_info['datemin'];
            $base_info['date_info']['begin_timestamp'] = change_time($datemin);
            $datemax = $base_info['datemax'];
            $base_info['date_info']['end_timestamp'] = change_time($datemax);;
            unset($base_info['datemin']);
            unset($base_info['datemax']);

            $card['groupon']['deal_detail'] = $base_info['deal_detail'];
            unset($base_info['deal_detail']);

            $card['groupon']['base_info'] = $base_info;

            $data['card'] = $card;

            $json = json_encode($data,JSON_UNESCAPED_UNICODE);

            $url = "https://api.weixin.qq.com/card/create?access_token=".$access_token;

            $response = PostRequest($url,$json);

            $res = json_decode($response,true);

            if($res['errcode']  == 0){
                //["errcode"] => int(0)
                //["errmsg"] => string(2) "ok"
                $CardList = new CardList();
                $CardList->admin_id = $admin_id;
                $CardList->type = 'GROUPON';
                $CardList->brand_name = $base_info['brand_name'];
                $CardList->title = $base_info['title'];
                $CardList->sub_title = $base_info['sub_title'];
                $CardList->notice = $base_info['notice'];
                $CardList->service_phone = $base_info['service_phone'];
                $CardList->description = $base_info['description'];
                $CardList->logo_url = $base_info['logo_url'];
                $CardList->quantity = $base_info['sku']['quantity'];
                $CardList->custom_url_name = $base_info['custom_url_name'];
                $CardList->custom_url = $base_info['custom_url'];
                $CardList->promotion_url_name = $base_info['promotion_url_name'];
                $CardList->promotion_url = $base_info['promotion_url'];
                $CardList->deal_detail = $card['groupon']['deal_detail'];

                $CardList->datemin =  $datemin;
                $CardList->datemax = $datemax;

                $CardList->color = $base_info['color'];
                $CardList->card_id = $res['card_id'];

                $CardList->save();

                echo "<h1>"."创建成功"."</h1>";

            }else{
               echo "<h1>".$res['errmsg']."</h1>";
               echo $json;
            }
        }

    }

    /**
     * 表单提交
     */
    public function add_general_groupon_post(){

        $admin_id = session('admin_id');
        
        if($this->request->method() == "POST"){
            $access_token = access_token($admin_id);
            if(!$access_token){
                $this->error("access_token未配置，或配置错误，请联系管理员",'system/index/base');
                exit;
            }

            $base_info = input('');
            
            $CardTemp = new CardTemp();
            $CardTemp->save_temp_log($admin_id,$base_info);

            $base_info['sku']['quantity'] = (int)$base_info['quantity'];
            unset($base_info['quantity']);

            $card['card_type'] = "GENERAL_COUPON";
           
            $base_info['date_info']['type'] = "DATE_TYPE_FIX_TIME_RANGE";
            $datemin = $base_info['datemin'];
            $base_info['date_info']['begin_timestamp'] = change_time($datemin);
            $datemax = $base_info['datemax'];
            $base_info['date_info']['end_timestamp'] = change_time($datemax);;
            unset($base_info['datemin']);
            unset($base_info['datemax']);

            $card['general_coupon']['default_detail'] = $base_info['deal_detail'];
            unset($base_info['deal_detail']);

            $card['general_coupon']['base_info'] = $base_info;

            $data['card'] = $card;

            $json = json_encode($data,JSON_UNESCAPED_UNICODE);

            $url = "https://api.weixin.qq.com/card/create?access_token=".$access_token;

            $response = PostRequest($url,$json);

            $res = json_decode($response,true);

            if($res['errcode']  == 0){
                //["errcode"] => int(0)
                //["errmsg"] => string(2) "ok"
                $CardList = new CardList();
                $CardList->admin_id = $admin_id;
                $CardList->type = 'GENERAL_COUPON';
                $CardList->brand_name = $base_info['brand_name'];
                $CardList->title = $base_info['title'];
                $CardList->sub_title = $base_info['sub_title'];
                $CardList->notice = $base_info['notice'];
                $CardList->service_phone = $base_info['service_phone'];
                $CardList->description = $base_info['description'];
                $CardList->logo_url = $base_info['logo_url'];
                $CardList->quantity = $base_info['sku']['quantity'];
                $CardList->custom_url_name = $base_info['custom_url_name'];
                $CardList->custom_url = $base_info['custom_url'];
                $CardList->promotion_url_name = $base_info['promotion_url_name'];
                $CardList->promotion_url = $base_info['promotion_url'];
                $CardList->deal_detail = $card['general_coupon']['default_detail'];

                $CardList->datemin =  $datemin;
                $CardList->datemax = $datemax;

                $CardList->color = $base_info['color'];
                $CardList->card_id = $res['card_id'];

                $CardList->save();

                echo "<h1>"."创建成功"."</h1>";

            }else{
               echo "<h1>".$res['errmsg']."</h1>";
               echo $json;
            }
        }

    }

    /**
     * 投放卡券
     * {"action_name": "QR_CARD",
     *     "expire_seconds": 1800,
     *          "action_info": {
     *          "card": {
     *          "card_id": "pFS7Fjg8kV1IdDz01r4SQwMkuCKc",
     *          "code": "198374613512",
     *          "openid": "oFS7Fjl0WsZ9AMZqrI80nbIq8xrA",
     *          "is_unique_code": false ,
     *          "outer_str":"12b"
     *          }
     *      }
     *   }
     */

    public function toufang(){
        $CardList = new CardList();
       
        $card_id = input('card_id');
        //判断卡券有没有二维码了
        $detail = $CardList->get_detail($card_id);
    
        if($detail['show_qrcode_url']){
            $this->assign('show_qrcode_url',$detail['show_qrcode_url']);
            //查询情况
            $data = $this->getcardcardinfo($card_id,$detail['datemin']);
            $this->assign('data',$data);
            return $this->fetch();
            exit;
        }
       
        $admin_id = session('admin_id');
        $access_token = access_token($admin_id);
        if(!$access_token){
            $this->error("access_token未配置，或配置错误，请联系管理员",'system/index/base');
            exit;
        }
        $data['action_name'] = "QR_CARD";
        $data['action_info']['card']['card_id'] = $card_id;
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);

        $url = "https://api.weixin.qq.com/card/qrcode/create?access_token=".$access_token;
        
        $response = PostRequest($url,$json);
       
        $res = json_decode($response,true);

        if($res['errcode'] == 0){
            
            $CardList->where(['card_id'=>$card_id])->update(['show_qrcode_url'=> $res['show_qrcode_url'], 'url'=> $res['url'], 'ticket' => $res['ticket']]);
            $this->assign('show_qrcode_url',$res['show_qrcode_url']);
            //查询情况
            $data = $this->getcardcardinfo($card_id,$detail['datemin']);
            $this->assign('data',$data);

            return $this->fetch();
        }else{
            $this->error('生成二维码失败');
        }
 
        
    }

    /**
     * 查询情况
     * ｛
     *   "begin_date":"2015-06-15",
     *  "end_date":"2015-06-30",
     *  "cond_source": 0,
     *  "card_id": "po8pktyDLmakNY2fn2VyhkiEPqGE"
     *  ｝
     */
    public function getcardcardinfo($card_id,$datemin){
        $admin_id = session('admin_id');
        $access_token = access_token($admin_id);
        if(!$access_token){
            $this->error("access_token未配置，或配置错误，请联系管理员",'system/index/base');
            exit;
        }
        $date = strtotime('-1 days');
        $end_date = date('Y-m-d',$date);

        $data['begin_date'] = $datemin;
        $data['end_date'] = $end_date;
        $data['cond_source'] = 1;
        $data['card_id'] = $card_id;

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);

        $url = "https://api.weixin.qq.com/datacube/getcardcardinfo?access_token=".$access_token;
        $response = PostRequest($url,$json);
        $res = json_decode($response,true);
      
        if(!isset($res['view_cnt'])){
            
            $list = array(
            "card_type"=>0,
            "view_cnt"=> 0,
            "view_user"=> 0,
            "receive_cnt"=> 0,
            "receive_user"=> 0,
            "verify_cnt"=> 0,
            "verify_user"=> 0,
            "given_cnt"=> 0,
            "given_user"=> 0,
            "expire_cnt"=> 0,
            "expire_user"=> 0
            );

            return $list;

        }else{
            return $res['list'];
        }

    }


    /**
     * 删除卡券
     */
    public function del_card(){

        $id = input('id');

        $CardList = new CardList();
        $detail = $CardList->get_detail_by_id($id);
        $card_id = $detail['card_id'];
        if(!$card_id){
            echo "删除失败";
        }

        $admin_id = session('admin_id');
        $access_token = access_token($admin_id);
        if(!$access_token){
            echo "access_token未配置";
            exit;
        }

        $url = "https://api.weixin.qq.com/card/delete?access_token=".$access_token;

        $data['card_id'] = $card_id;
        $json = json_encode($data);
        $response = PostRequest($url,$json);

        $res = json_decode($response,true);
    
        if($res['errcode']  == 0){
            //数据库删除
            $CardList->delete_by_id($card_id);
            echo "删除成功";
            
        }else{
            echo "删除失败";
        }


    }









    public function level()
    {

        return $this->fetch();
    }

    public function scoreoperation()
    {

        return $this->fetch();
    }

    public function record_browse()
    {

        return $this->fetch();
    }

    public function record_download()
    {

        return $this->fetch();
    }

    public function record_share()
    {

        return $this->fetch();
    }

    public function show()
    {

        return $this->fetch();
    }

}
