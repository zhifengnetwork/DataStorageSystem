<?php
namespace app\activity\controller;
use app\common\controller\Base;
use think\Db;

use app\member\model\Member;


class Index extends Base
{
    
    /**
     * 列表
    */
    public function index()
    {

        return $this->fetch();
    }

    public function del()
    {

        return $this->fetch();
    }

    public function add()
    {

        return $this->fetch();
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


    


     /**
     * 详情
     * 
     * 参数 ： id
     */
    public function detail()
    {
        $id = input('id');
        if(!$id){
            $return_arr = array('status' => -1, 'msg' => 'id不存在','data'=>'');
            ajaxReturn($return_arr);
            exit;
        }

        $sell = new Sell();
        $data = $sell->where(array('id'=>$id))->find();
        if(!$data){
            $return_arr = array('status' => -1, 'msg' => '本条信息已不在','data'=>'');
            ajaxReturn($return_arr);
            exit;
        }

        $imges = new Images();
        $data['img'] = $imges->where(array('sell_id'=>$id))->select();
        
        $return_arr = array('status' => 1, 'msg' => '获取详情成功','data'=>$data);
        ajaxReturn($return_arr);
        exit;
    }


}
