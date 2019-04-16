<?php
namespace app\user\model;

class Admin extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_admin';

    public static function login($username,$password){
        $res = self::get(['username'=>$username,'password'=>$password]);
        if(!$res){
            return false;
        }
        return $res->data;
    }

}