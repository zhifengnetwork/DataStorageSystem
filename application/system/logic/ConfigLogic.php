<?php
namespace app\system\logic;
use app\system\model\Config;

/**
 * 配置逻辑
 */
class ConfigLogic 
{
   
    /**
     * 获取配置列表
     */
    public function get_config_list($admin_id){
        if(!$admin_id){
            return [];
        }

        $Config = new Config();

        $res = $Config->where("admin_id",$admin_id)->select();
        if($res){
            foreach($res as $k=>$val){
                $data[$val['name']] = $val['value'];
            }
        }else{
            return [];
        }
        
        return $data;
    }


    /**
     * 保存设置
     */
    public function save_config($admin_id,$data){

        $Config = new Config();
           
        //更新缓存
        $result =  $Config->where(["admin_id"=> $admin_id ])->select();
        if($result){
            foreach($result as $val){
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('name'=>$k,'value'=>trim($v),'admin_id'=>$admin_id);
              
                if(!isset($temp[$k])){
                  
                    $Config->admin_id = $admin_id;
                    $Config->name = $k;
                    $Config->value = trim($v);
                    $Config->save();//新key数据插入数据库
                }else{
                    if($v!=$temp[$k]){

                        $Config->where(["name"=>$k,'admin_id'=>$admin_id])->update($newArr);//缓存key存在且值有变更新此项
                    }
                    
                }
            }
            //更新后的数据库记录
            $newRes = $Config->where(["admin_id"=> $admin_id ])->select();
            foreach ($newRes as $rs){
                $newData[$rs['name']] = $rs['value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('name'=>$k,'value'=>trim($v),'admin_id'=>$admin_id);
            }
            $Config->saveAll($newArr);
            $newData = $data;
        }

        return $newData;
            
    }
    
    
}