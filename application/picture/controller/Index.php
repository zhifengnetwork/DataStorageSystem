<?php
namespace app\picture\controller;
use app\common\controller\Base;
use app\picture\model\PictureList;
use think\Db;


class Index extends Base
{
    
    public function picture_add()
    {
        $admin_id = session('admin_id');
        $access_token = access_token($admin_id);
        if(!$access_token){
                $this->error("access_token未配置，或配置错误，请联系管理员",'system/index/base');
                exit;
        }
        if($this->request->method() == "POST"){
            $file = request()->file("fff");
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $name = $info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }else{
                $this->error('请选择图片');
            }

            if(!$name){
                $this->error('图片上传错误');
            }
            $rootname = "/home/wwwroot/crm.c3w.cc/public/uploads/".$name;
            $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$access_token;
            $strPOST = array('buffer'=> new \CURLFile($rootname));
            // 'C:\Users\langbai\Downloads\timg_看图王.jpg'
          
            $ci = curl_init();
            /* Curl settings */
            curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
            curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
            curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
            curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ci, CURLOPT_POST, true);
            curl_setopt($ci, CURLOPT_POSTFIELDS, $strPOST);
            curl_setopt($ci, CURLOPT_URL, $url);
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
            //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
            curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
            curl_setopt($ci, CURLINFO_HEADER_OUT, true);
            /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
            $response = curl_exec($ci);
            $res = json_decode($response,true);


            $PictureList = new PictureList();
            $PictureList->name = input('name');
            $PictureList->url = $res['url'];
            $PictureList->admin_id = $admin_id;
            $PictureList->realurl = "/public/uploads/".$name;
            $PictureList->save();

            $this->success("上传成功",'picture_list');
            exit;
        }

        return $this->fetch();
    }




    public function picture_list()
    {
        $admin_id = session('admin_id');

        $PictureList = new PictureList();
        $data = $PictureList::get_picture_list();


       
        $this->assign('data',$data);
        $this->assign('count',count($data));

        return $this->fetch();
    }

    public function picture_show()
    {

        return $this->fetch();
    }


    /**
    * 删除图片
    */
    public function delete_pic(){
        $id = input('id');
        if(!$id){
            return false;
        }
        $admin_id = session('admin_id');
        $PictureList = new PictureList();
        $PictureList->where(['id'=>$id,'admin_id'=>$admin_id])->delete();
    }
   

}
