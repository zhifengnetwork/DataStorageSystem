<?php
namespace app\mobile\controller;
use app\mobile\controller\Base;
use think\Db;

use app\picture\model\PictureList;
use app\card\model\CardList;
use app\card\model\CardCheck;

class Check extends Base
{
    
    public function index()
    {
        $card_id = input('card_id');
        $code = input('code');
        $openid = input('openid');

        $CardList = new CardList();

        $CardCheck = new CardCheck();
        $CardCheck->card_id = $card_id;
        $CardCheck->code = $code;
        $CardCheck->openid = $openid;
        $CardCheck->admin_id = $CardList->get_admin_id_by_card_id($card_id);

        $CardCheck->save();


        $this->assign('openid',$openid);

        return $this->fetch();
    }


}