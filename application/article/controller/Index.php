<?php
namespace app\article\controller;
use app\common\controller\Base;
use think\Db;

use app\article\model\Article;

class Index extends Base
{
    
    /**
     * 列表
    */
    public function index()
    {

        return $this->fetch();
    }

    public function add()
    {

        return $this->fetch();
    }

    public function category()
    {

        return $this->fetch();
    }

    public function article_list()
    {

        return $this->fetch();
    }

    public function category_edit()
    {

        return $this->fetch();
    }

}
