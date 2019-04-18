<?php
namespace app\user\model;

class User extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'crm_member';

    public static function login($username,$password){
        if(!$username || !$password) return '-2';
        $res = self::get(['name'=>$username]);
        if(!$res) return '-1';
        if(!$res->status) return 0;
        
        $password = pwd($password,$res->salt);
        if($password==$res->pwd){
            return $res->data;
        }

        return 2;
    }

}