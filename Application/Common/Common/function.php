<?php

/*权限检查*/
function auth_check($rule, $id)
{
    $auth = new \Think\Auth();
    return $auth->check($rule, $id);

}



/*登录检查*/
function login_check()
{
//	if (session("admin.id") == null) {
//		return false;
//	}else{
//		return true;
//	}
    /*cookie存在 且用户不在登录状态*/
    if (isset($_COOKIE['admin_login']) && $_SESSION['admin'] == null) {
        $auto = explode(':', $_COOKIE['admin_login']);
        $str = $auto[0];
        $value = $auto[1];
        $key = 'quanxiaoyun2018';
        if ($str = md5($value . $key)) {
            $_SESSION['admin']['id'] = explode(';', $value)[0];
            $_SESSION['admin']['name'] = explode(';', $value)[1];
        }
    }

    if (isset($_SESSION['admin'])) {
        return true;
    }
}

/*用户登录检查*/
function user_login_check()
{
    if (session("user.id") == null) {
        return FALSE;
    } else {
        return true;
    }
}



/**
 * 获取当前页面完整URL地址
 */
function get_current_url()
{
    $sys_protocal = isset ($_SERVER ['SERVER_PORT']) && $_SERVER ['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER ['PHP_SELF'] ? $_SERVER ['PHP_SELF'] : $_SERVER ['SCRIPT_NAME'];
    $path_info = isset ($_SERVER ['PATH_INFO']) ? $_SERVER ['PATH_INFO'] : '';
    $relate_url = isset ($_SERVER ['REQUEST_URI']) ? $_SERVER ['REQUEST_URI'] : $php_self . (isset ($_SERVER ['QUERY_STRING']) ? '?' . $_SERVER ['QUERY_STRING'] : $path_info);
    return $sys_protocal . (isset ($_SERVER ['HTTP_HOST']) ? $_SERVER ['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 初始化微信
 *
 * @return \Common\Common\Wechat
 */
function init_weixin()
{
    $options = array(
        'token' => C("wx_token"), // 填写你设定的key
        'encodingaeskey' => C("wx_encodingaeskey"), // 填写加密用的EncodingAESKey
        'appid' => C("wx_appid"), // 填写高级调用功能的app id
        'appsecret' => C("wx_appsecret")  // 填写高级调用功能的密钥
    );
    $weObj = new \Common\Common\Wechat ($options);
    return $weObj;
}

function check_sign($data)
{
    if (!is_array($data) || !isset($data['sign'])) return false;
    $sign = $data['sign'];
    unset($data['sign']);       //销毁sign

    $my_sign = get_sign($data);

    if ($sign != $my_sign) {
        return false;
    } else {
        return true;
    }
}

/**
 * @param $data -参与签名的数据数组
 * @return bool|string -返回值
 */
function get_sign($data)
{
    if (!is_array($data)) return false;
    ksort($data);       //对数组按照键名进行排序
    $str = '';
    foreach ($data as $key => $val) {
        $str .= $key . '=' . $val . '&';        //$str = $str.$key.'='.$val.'&'
    }
    return md5($str . 'key=' . C('index_key'));
}


/**
 * @param $data -参与签名的数据数组
 * @return bool|string -返回值
 */
function get_pay_sign($data)
{
    if (!is_array($data)) return false;
    ksort($data);       //对数组按照键名进行排序
    $str = '';
    foreach ($data as $key => $val) {
        $str .= $key . '=' . $val . '&';        //$str = $str.$key.'='.$val.'&'
    }
    return md5($str . 'key=' . C('key'));
}

/**
 *    作用：生成支付签名
 */
function getSign($Obj)
{
    foreach ($Obj as $k => $v) {
        $Parameters[$k] = $v;
    }
    //签名步骤一：按字典序排序参数
    ksort($Parameters);
    $String = formatBizQueryParaMap($Parameters, false);
    //echo '【string1】'.$String.'</br>';
    //签名步骤二：在string后加入KEY
    $String = $String . "&key=" . C('key');
    //echo "【string2】".$String."</br>";
    //签名步骤三：MD5加密
    $String = md5($String);
    //echo "【string3】 ".$String."</br>";
    //签名步骤四：所有字符转为大写
    $result_ = strtoupper($String);
    //echo "【result】 ".$result_."</br>";
    return $result_;
}

/**
 *    作用：格式化参数，签名过程需要使用
 */
function formatBizQueryParaMap($paraMap, $urlencode)
{
    $buff = "";
    ksort($paraMap);
    foreach ($paraMap as $k => $v) {
        if ($urlencode) {
            $v = urlencode($v);
        }
//		$buff .= strtolower($k) . "=" . $v . "&";
        $buff .= $k . "=" . $v . "&";
    }
    $reqPar = '';
    if (strlen($buff) > 0) {
        $reqPar = substr($buff, 0, strlen($buff) - 1);
    }
    return $reqPar;
}


/**
 * 写日志到文件
 * @param $content -日志内容
 */
function write_log($content)
{
    $dir = './log_data';
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $file = $dir . '/' . date('Ymd') . '.txt';
    $content .= "\n";
    $content .= "\r";
    if (file_exists($file)) {
        file_put_contents($file, $content, FILE_APPEND);
    } else {
        $resource = fopen($file, 'w+');
        file_put_contents($file, $content, FILE_APPEND);
        fclose($resource);
    }
}

function write_file($data)
{
    $dir = './user_data';
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $file = $dir . '/' . date('Ymd') . '.txt';

    $content = '';
    foreach ($data as $val) {
        $content .= $val;
        $content .= "\r";
        $content .= "\n";
    }

    fclose(fopen($file, 'w'));

    if (file_exists($file)) {
        file_put_contents($file, $content, FILE_APPEND);
    } else {
        $resource = fopen($file, 'w+');
        file_put_contents($file, $content, FILE_APPEND);
        fclose($resource);
    }
}


function http_get($url)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}


function http_post($url, $data, $header = array())
{
    if (empty($header)) {
        $header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($res, true);
    return $res;
}

/**
 * 发起请求
 * @param string $url 请求地址
 * @param string $data 请求数据包
 * @return   string      请求返回数据
 */
function http_post_json($url, $data)
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话
    return $tmpInfo; // 返回数据
}

/**
 *    作用：以post方式提交xml到对应的接口url
 */
function postXmlCurl($xml, $url, $second = 30)
{
    //初始化curl
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOP_TIMEOUT, $second);
    //这里设置代理，如果有的话
//         curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
//         curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    //运行curl
    $data = curl_exec($ch);
    curl_close($ch);
    //返回结果
    if ($data) {
        curl_close($ch);
        return $data;
    } else {
        $error = curl_errno($ch);
        echo "curl出错，错误码:$error" . "<br>";
        echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
        curl_close($ch);
        return false;
    }
}


/**
 *作用：array转xml
 */
function arrayToXml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

        } else
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 *作用：将xml转为array
 */
function xmlToArray($xml)
{
    //将XML转为array
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

    return $array_data;
}



/**
 * 生成随机字符串
 * @param $len
 * @return string
 */
function rand_str($len)
{
    $source_str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $re_str = '';
    for ($i = 0; $i < $len; $i++) {
        $re_str .= substr($source_str, mt_rand(0, strlen($source_str) - 1), 1);
    }
    return $re_str;
}


/**
 * 生成随机数
 * @param $len
 * @return string
 */
function rand_num($len)
{
    $source_str = '0123456789';
    $re_str = '';
    for ($i = 0; $i < $len; $i++) {
        $re_str .= substr($source_str, mt_rand(0, strlen($source_str) - 1), 1);
    }
    return $re_str;
}


/**
 * 生成二维码
 * @param $text
 * @return string
 */
function create_qr_img($text)
{
    $qrcode = "http://api.k780.com:88/?app=qr.get&level=L&size=20&data=" . urlencode($text);
    return $qrcode;
}



/***
 * 获取小程序的token
 * */
function getAccessToken()
{
    $access_token = S('weixin_access_token');
    if (!$access_token) {
        $appid = C('xcx_appid');
        $appSecret = C('xcx_appsecret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appSecret;
        $result = http_get($url);
        $result = json_decode($result, true);
        $access_token = $result['access_token'];
        S('weixin_access_token', $access_token, 3000);
    }
    return $access_token;

}


/**
 * 获取客户端IP
 * @return string|unknown
 */
function getIP()
{
    $IPaddress = '';
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $IPaddress = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $IPaddress = getenv("HTTP_CLIENT_IP");
        } else {
            $IPaddress = getenv("REMOTE_ADDR");
        }
    }
    return $IPaddress;
}

/**
 * 获取客户端城市名称
 * @return array city,code
 */
function getcity()
{
    $clientIP = getIP();
    //http://user.ip138.com
    $area = http_get('http://api.ip138.com/query/?ip=' . $clientIP . '&datatype=jsonp&token=4d490ba345079b540a903d033eded356');
    $area = json_decode($area, true);
    $returnArr = array();
    $returnArr['city'] = '';
    $returnArr['code'] = '';
    $city = '';
    if ($area['ret'] == 'ok') {
        // 	        $returnArr['region'] = $area['data'][1];
        if ($area['data'][2]) {
            $returnArr['city'] = $area['data'][2];
        } else {
            $returnArr['city'] = $area['data'][1];//取到所在省
        }

        if ($returnArr['city']) {
//            $code = M('city')->where(array('name' => $returnArr['city']))->getField('city_code');
            $returnArr['code'] = $area['data'][4];
        }
    }

    return $returnArr;
}



