<?php
namespace app\user\controller;
use app\common\controller\Base;


class Index extends Base
{
    public function index()
    {

        $admin_id = session('admin_id');


        return $this->fetch();
    }
}
