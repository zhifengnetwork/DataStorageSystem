<?php
use think\Db;

//添加登录记录
function admin_log($admin_id){

    $AdminLog = new \app\admin\model\AdminLog();
    $AdminLog->admin_id = $admin_id;
    $AdminLog->time = time();
    $AdminLog->ip = get_client_ip();
    $AdminLog->save();

}

function pre($data){
    echo '<pre>';
    print_r($data);
}

function pred($data){
    echo '<pre>';
    print_r($data);die;
}

function password($pwd,$salt){
    return sha1( $salt . sha1($pwd) . $salt );
}

function pwd($pwd,$salt){
    return md5( md5($pwd) . $salt );
}

function getTree($list,$pid=0,$level=0){
    static $tree = array(); //定义一个静态的数组,此数组只会初始化一次
    foreach ($list as $value) {
        //先找出顶级栏目pid=0
        if($value['pid']==$pid){
            $value['level']=$level;//给当前数组$value加一个元素level
            $tree[]=$value;
            getTree($list,$value['id'],$level+1);//递归调用自身
        }
    }
    return $tree;//返回结果
}

function returnJson($data, $message = 'ok', $code = 1){
    header("Content-Type:text/html; charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT');
    $result = array();
    $result['code'] = $code;
    $result['msg'] = $message;
    $result['data'] = $data;
    if(empty($data)){
        class k{}
        $result['data'] = new k;
    }
    $json = json_encode($result,JSON_UNESCAPED_UNICODE);
    exit($json);
}

//关闭iframe子窗口
function layer_close($ts=''){
    if($ts){
        echo '<script>alert("'.$ts.'")</script>';
    }
    echo '<script type="text/javascript" src="'.config('view_replace_str.__ADMIN__').'/lib/jquery/1.9.1/jquery.min.js"></script>';
    echo '<script type="text/javascript" src="'.config('view_replace_str.__ADMIN__').'/js/H-ui.admin.js"></script>';
    echo "<script>window.parent.location.reload();</script>";
    echo "<script>layer_close();</script>";
}

function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的客户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的客户计算机的ip地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 * 时间转换
 */
function change_time($oldtime){
    $catime = strtotime($oldtime);
    return $catime;
}

function access_token($admin_id){
    $access_token_url = Db::table('crm_config')->where(['admin_id'=>$admin_id,'name'=>'access_token_url'])->value('value');
    $access_token = httpRequest($access_token_url, 'GET');
    return $access_token;
}



function ajaxReturn($data){
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($data,JSON_UNESCAPED_UNICODE));
}


function PostRequest($url, $json){
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ci, CURLOPT_POST, true);
    curl_setopt($ci, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ci, CURLOPT_URL, $url);
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    return curl_exec($ci);
}


/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
 function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if($ssl){
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
	//return array($http_code, $response,$requestinfo);
}
