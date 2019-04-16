<?php
namespace app\system\controller;
use app\common\controller\Base;
use app\system\model\Config;
use app\system\logic\ConfigLogic;
use think\Db;


class Index extends Base
{
    
    public function add(){
        return $this->fetch();
    }
   
    /**
     * 系统设置
     */
    public function base(){
       
        $admin_id = session('admin_id');

        $ConfigLogic = new ConfigLogic();

        if($this->request->method() == "POST"){
            $post = input('');
           
            $ConfigLogic->save_config($admin_id,$post);

            $this->success("修改成功");
            exit;
        }
       
        $config = $ConfigLogic->get_config_list($admin_id);

        if(!isset($config['access_token_url'])){
            $config['access_token_url'] = "";
        }
        if(!isset($config['website_title'])){
            $config['website_title'] = "";
        }
        if(!isset($config['website_keywords'])){
            $config['website_keywords'] = "";
        }
        if(!isset($config['key_id'])){
            $config['key_id'] = "";
        }
        if(!isset($config['key_secret'])){
            $config['key_secret'] = "";
        }
        if(!isset($config['website_info'])){
            $config['website_info'] = "";
        }

        
        
        $this->assign('config',$config);

        return $this->fetch();
    }
    
    public function category(){
        return $this->fetch();
    }
    
    public function log(){
        return $this->fetch();
    }

    public function data(){
        return $this->fetch();
    }
    
    public function shielding(){
        return $this->fetch();
    }
    

}
