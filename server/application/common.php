<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 导入
 * @param $file
 * @param int $sheet
 */
function importCG($file,$sheet = 1,$cellName){
    new  PHPExcel();
    $objRead = new PHPExcel_Reader_Excel2007();
    if(!$objRead->canRead($file)){
        $objRead = new PHPExcel_Reader_Excel5();
        if(!$objRead->canRead($file)){
            die('No Excel!');
        }
    }

    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

    $obj = $objRead->load($file);  //建立excel对象
    $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
    $columnH = $currSheet->getHighestColumn();   //取得最大的列号
    $columnCnt = array_search($columnH, $cellName);
    $rowCnt = $currSheet->getHighestRow();   //获取总行数

    $data = array();
    for($_row=1; $_row<=$rowCnt; $_row++){  //读取内容
        for($_column=0; $_column<=$columnCnt; $_column++){
            $cellId = $cellName[$_column].$_row;
            $cellValue = $currSheet->getCell($cellId)->getValue();
            //$cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
            if($cellValue instanceof PHPExcel_RichText){   //富文本转换字符串
                $cellValue = $cellValue->__toString();
            }

            $data[$_row][$cellName[$_column]] = $cellValue;
        }
    }
    return $data;
}
/**
 *
 * @param $data
 * @param string $msg
 * @param int $code
 */
function returnData($data=array(),$msg='成功',$code=200)
{
    $param['data'] = $data;
    $param['msg'] = $msg;
    $param['code'] = $code;
    return json($param);
}
/**
 * 模拟tab产生空格
 * @param int $step
 * @param string $string
 * @param int $size
 * @return string
 */
function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}
/**
 * 随机字符串
 * @param int $length 长度
 * @param int $numeric 类型(0：混合；1：纯数字)
 * @return string
 */
function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    if($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}
/**
 * XML转数组
 * @param string $arr
 * @param boolean $isnormal
 * @return array
 */
function xml2array(&$xml, $isnormal = FALSE) {
    $xml_parser = new Xml($isnormal);
    $data = $xml_parser->parse($xml);
    $xml_parser->destruct();
    return $data;
}

/**
 * 获取统计
 * @param $user_id
 */
function getUserOrderCount($user_id){
    $html = '';
    $model = model('User');
    $data = $model->getOrderCount($user_id);
    $html = '<p>总餐：'.$data['total'].'&nbsp&nbsp'.'已吃：'.$data['use'].'<br/>'.'在用：'.$data['order'].'&nbsp&nbsp'.'剩余：'.$data['rest'].''.'</p>';
    return $html;
}
/**
 * 数组转XML
 * @param array $arr
 * @param boolean $htmlon
 * @param boolean $isnormal
 * @param intval $level
 * @return type
 */
function array2xml($arr, $htmlon = TRUE, $isnormal = FALSE, $level = 1) {
    $s = $level == 1 ? "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
    $space = str_repeat("\t", $level);
    foreach($arr as $k => $v) {
        if(!is_array($v)) {
            $s .= $space."<item id=\"$k\">".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</item>\r\n";
        } else {
            $s .= $space."<item id=\"$k\">\r\n".array2xml($v, $htmlon, $isnormal, $level + 1).$space."</item>\r\n";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    return $level == 1 ? $s."</root>" : $s;
}

/**
 * 对多位数组进行排序
 * @param $multi_array 数组
 * @param $sort_key需要传入的键名
 * @param $sort排序类型
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return FALSE;
            }
        }
    } else {
        return FALSE;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}
/**
 * 根据日期生成唯一订单号
 * @param boolean $refresh 	是否刷新再生成
 * @return string
 */
function _build_order_sn($refresh = FALSE) {
    if ($refresh == TRUE) {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 12);
    }
    return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
}
/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && config('app_debug')) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}
/**
 * 检查用户是否登录
 */
function check_user(){

    if(!session('user.id') || session('user.id') < 0){
        return false;
    }
    return true;
}

/**
 * 商家检测  需要登录
 */

function check_seller(){
    /** 检测是否登录 */

    if(!check_user()){
        return false;
    }

    if(session('user.type') != 2){
        return false;
    }
    return true;
}

/**
 * 短信发送
 * 短信宝账户：
 *   http://www.smsbao.com/member/index.jhtml
 *   zxnm12321
 *   w3763020
 * 短信接口信息：
 *   http://www.smsbao.com/openapi/
 *
 * @param $content
 * @param $phone
 */
function send_SMS($content,$phone){
    $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    $smsapi = "http://www.smsbao.com/"; //短信网关
    $user = "zxnm12321"; //短信平台帐号
    $pass = md5("w3763020"); //短信平台密码
    // $content="短信内容";//要发送的短信内容
    //$phone = "*****";
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    $result =file_get_contents($sendurl) ;
    return $result;
    // echo $statusStr[$result];
}







