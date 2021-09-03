<?php
/**
 * 不区分大小写的in_array实现.
 *
 * @param mixed $value
 * @param mixed $array
 */
function in_array_case($value, $array)
{
    return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 生成guid函数.
 *
 * @return mixed|string
 */
function create_guid()
{
    $guid = new \org\Guid();

    return $guid->uuid();
}

/**
 *  是否以开头.
 *
 * @param string $haystack
 * @param mixed  $needle
 * @param bool   $caseSensitive 大小写敏感 true敏感 false不敏感
 */
function startWith($haystack, $needle, $caseSensitive = true)
{
    return $caseSensitive ? 0 === strpos($haystack, $needle) : 0 == stripos($haystack, $needle);
}

/**
 *  是否以结尾.
 *
 * @param string $haystack
 * @param mixed  $needle
 * @param bool   $caseSensitive 大小写敏感 true敏感 false不敏感
 */
function endWith($haystack, $needle, $caseSensitive = true)
{
    if ($caseSensitive) {
        return false !== ($pos = strrpos($haystack, $needle)) && $pos == strlen($haystack) - strlen($needle);
    }

    return false !== ($pos = strripos($haystack, $needle)) && $pos == strlen($haystack) - strlen($needle);
}

/**
 *  获取首字母.
 *
 * @param string $string       字符串
 * @param mixed  $default_char 替换非字母变成#
 *                             当 = FALSE 时 数字也返回
 */
function get_first_letter($string, $default_char = '#')
{
    if (empty($string)) {
        return '';
    }
    $fchar = ord($string[0]);
    if ($fchar >= ord('A') && $fchar <= ord('z')) {
        return strtoupper($string[0]);
    }
    if (false === $default_char &&
        ($fchar >= ord('0') && $fchar <= ord('9'))) {
        return $string[0];
    }

    return false === $default_char ? '' : $default_char;
}

/**
 *  元素加入数组开头.
 *
 * @param mixed $key
 * @param mixed $val
 */
function array_unshift_assoc(&$arr, $key, $val)
{
    $arr = array_reverse($arr, true);
    $arr[$key] = $val;

    return array_reverse($arr, true);
}

//生成url
function U($array = null)
{
    if (null === $array) {
        $url = '';
    } else {
        $url = '?' . http_build_query($array);
        $url = str_replace('%23', '#', $url);
    }

    return 'index.php' . $url;
}

/**
 * 判断是否海外手机号.
 *
 * @param mixed $country_code
 */
function is_abroad_country_code($country_code)
{
    return '86' !== str_replace('+', '', $country_code);
}

/**
 * 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>.
 *
 * @param string     $start 开始标签
 * @param string     $end   结束标签
 * @param int|string $dec   小数位或者m
 *
 * @return mixed
 */
function G($start, $end = '', $dec = 4)
{
    static $_info = [];
    static $_mem = [];
    if (is_float($end)) { // 记录时间
        $_info[$start] = $end;
    } elseif (!empty($end)) { // 统计时间和内存使用
        if (!isset($_info[$end])) {
            $_info[$end] = microtime(true);
        }
        if (MEMORY_LIMIT_ON && 'm' == $dec) {
            if (!isset($_mem[$end])) {
                $_mem[$end] = memory_get_usage();
            }

            return number_format(($_mem[$end] - $_mem[$start]) / 1024);
        }

        return number_format(($_info[$end] - $_info[$start]), $dec);
    } else { // 记录时间和内存使用
        $_info[$start] = microtime(true);
        if (MEMORY_LIMIT_ON) {
            $_mem[$start] = memory_get_usage();
        }
    }

    return null;
}

/**
 * 格式化字节大小.
 *
 * @param number $size      字节数
 * @param string $delimiter 数字和单位分隔符
 *
 * @return string 格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
        $size /= 1024;
    }

    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 二维数组去重复项.
 *
 * @param      $array2D
 * @param bool $stkeep
 * @param bool $ndformat
 *
 * @return mixed
 */
function unique_arr($array2D, $stkeep = false, $ndformat = true)
{
    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if ($stkeep) {
        $stArr = array_keys($array2D);
    }
    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if ($ndformat) {
        $ndArr = array_keys(end($array2D));
    }

    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
    foreach ($array2D as $v) {
        $v = implode(',', $v);
        $temp[] = $v;
    }

    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);
    //再将拆开的数组重新组装
    foreach ($temp as $k => $v) {
        if ($stkeep) {
            $k = $stArr[$k];
        }
        if ($ndformat) {
            $tempArr = explode(',', $v);
            foreach ($tempArr as $ndkey => $ndval) {
                $output[$k][$ndArr[$ndkey]] = $ndval;
            }
        } else {
            $output[$k] = explode(',', $v);
        }
    }

    return $output;
}

/**
 * 生成请求signature.
 *
 * @return string
 * @throws \think\exception\DbException
 */
function generate_request_sign()
{
    $app_secret = Apps::cache_data(CLIENT_APP_ID, 'app_secret');
    $iv = Env::get('sign.iv');
    $params = [
        'app_id' => CLIENT_APP_ID,
        'app_version' => CLIENT_APP_VERSION,
        'api_version' => CLIENT_API_VERSION,
        '_time' => time(),
        '_salt' => get_salt(),
    ];
    $string = http_build_query($params);
    $encrypted_string = openssl_encrypt($string, 'AES-128-CBC', $app_secret, OPENSSL_RAW_DATA, $iv);

    return base64_encode($encrypted_string);
}

//首字母大写
function getUcFirst($string)
{
    return ucfirst(strtolower($string));
}

//格式化国外手机号
function format_mobile($phone)
{
    if (is_string($phone) && $phone) {
        return ltrim(trim($phone), 0);
    }

    return $phone;
}

/**
 * 使用libphonenumber验证手机号有效性
 * 同时判断国内和国外手机号规则.
 *
 * @param $countryCode
 * @param $phoneNumber
 *
 * @return bool
 */
function isValidPhoneNumber($countryCode, $phoneNumber)
{
    $raw = '+' . $countryCode . '' . ltrim($phoneNumber);

    try {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phone = $phoneUtil->parse($raw);

        return $phoneUtil->isValidNumber($phone);
    } catch (\Exception $e) {
        __LOG_MESSAGE($e, 'isValidPhoneNumberException');
    }

    return false;
}

/**
 *  生成房源sku.
 *
 * @param mixed $house_id
 * @param mixed $type_id
 */
function gen_sku($house_id, $type_id)
{
    if (!in_array($type_id, [1, 2, 3, 4], true)) {
        return '';
    }
    $skus = ['', 'R', 'B', 'S', 'N'];

    return $skus[$type_id] . sprintf('%08d', $house_id);
}

/**
 *  生成unit sku.
 *
 * @param mixed $house_id
 * @param mixed $index_of_house
 * @param mixed $type_id
 */
function gen_unit_sku($house_id, $index_of_house, $type_id)
{
    if (!in_array($type_id, [1, 2, 3, 4], true)) {
        return '';
    }
    $skus = ['', 'R', 'B', 'S', 'N'];

    return $skus[$type_id] . sprintf('%08d', $house_id) . sprintf('%04d', $index_of_house);
}

/**
 * @param $lat
 * @param $lng
 *
 * @return bool
 */
function verifyLatlng($lat, $lng)
{
    return (-90 <= $lat && $lat <= 90) && (-180 <= $lng && $lng <= 180);
}

/**
 * 解析url域名变更为国内访问域名
 *
 * @param $url
 *
 * @return bool|string
 */
function parseUrl($url)
{

    if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
        if (substr($url, 0, 7) == 'http://') {
            $url = substr($url, 7);
        } else {
            if (substr($url, 0, 8) == 'https://') {
                $url = substr($url, 8);
            }
        }
        if (strpos($url, '/') !== false) {
            $url = config('API_DOMAIN') . substr($url, strpos($url, '/'));
        }
    }
    return $url;
}

/**
 * 加入邮件处理队列.
 *
 * @param       $target
 * @param       $send_key
 * @param array $params
 *
 * @return bool
 * @throws \Exception
 */
function deal_send_email_mns($target, $send_key, $params = [])
{
    try {
        $queue_array = [
            //分发key  包含2部分  3位大写码+.+key
            'key' => $send_key,
            //发送时间
            'created_at' => time(),
            //发送参数
            'target' => $target,
            //提交参数
            'params' => $params,
        ];
        $array_string = json_encode($queue_array, JSON_UNESCAPED_UNICODE);
        __LOG_MESSAGE($array_string, 'SendEmailAndServicePush_array_string:');
        //发送测试数据到队列
        $queue_name = IS_PRODUCTION ? 'nts-polling' : 'nts-testing-polling';
        $mnsClient = new \org\Mns\MnsClient($queue_name);
        $response = $mnsClient->send($queue_array);
        __LOG_MESSAGE('SendEmailAndServicePush_response_end');
        if ($response->isSucceed()) {
            return true;
        }

        return false;
    } catch (\Exception $e) {
        __LOG_MESSAGE($e, 'SendEmailAndServicePush_exception');

        return false;
    }

}

/**
 * 创建mns URL请求队列.
 *
 * @param       $params
 * @param mixed $url
 * @param mixed $request_type
 *
 * @throws Exception
 * @return bool
 */
function send_mns_url_queue($url, $params, $request_type = 'GET')
{
    $target = [
        'method' => $request_type,
        'url' => $url,
        'params' => $params,
    ];
    $options = [];
    $queue_array = [
        'mode' => 'URL',
        //发送请求id
        'request_id' => msectime(),
        //发送时间
        'created_at' => time(),
        //发送参数
        'target' => $target,
        //提交配置参数
        'options' => $options,
    ];
    //发送测试数据到队列
    $mnsClient = new MnsClient(MNS_PREFIX . 'queue');
    $response = $mnsClient->send($queue_array);
    __LOG_MESSAGE($response,1111);
    return $response;
}

/**
 * 比较两个值
 *
 * @param $old_value
 * @param $new_value
 *
 * @return bool
 */
function verifyDuplication($old_value, $new_value)
{
    if ($old_value == $new_value) {
        return false;
    }
    if (is_numeric($old_value)) {
        if (bccomp($old_value, $new_value, 4) == 0) {
            return false;
        }
    }
    if (strtolower($old_value) == strtolower($new_value)) {
        return false;
    }
    return true;
}

/**
 * 标准化数字
 */
function format_number($number)
{
    return strval(number_format($number, 2));
}

/**
 * 验证多维数组存在某个值
 *
 * @param $value
 * @param $array
 *
 * @return bool
 */
function deep_in_array($value, $array)
{
    foreach ($array as $item) {
        if (!is_array($item)) {
            if ($item == $value) {
                return $item;
            } else {
                continue;
            }
        }
        if (in_array($value, $item)) {
            return $item;
        } else {
            if (deep_in_array($value, $item)) {
                return $item;
            }
        }
    }
    return false;
}

/**
 * 创建订单编号
 * #生成24位唯一订单
 * 格式：YYYY-MMDD-HHII-SS-NNNN,NNNN-CC
 * $type 订单类型
 *
 * @param mixed $type
 */
function generateOrderNumber($type)
{
    $prexs = [1 => 'MV'];
    $order_prefix = isset($prexs[$type]) ? $prexs[$type] : 'R';
    //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
    $order_id_main = date('YmdHis') . rand(1000000, 9999999);
    //订单号码主体长度
    $order_id_len = strlen($order_id_main);
    $order_id_sum = 0;
    for ($i = 0; $i < $order_id_len; ++$i) {
        $order_id_sum += (int)(substr($order_id_main, $i, 1));
    }
    //唯一订单号码（XYYYYMMDDHHIISSNNNNNNNCC）
    return $order_prefix . $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
}

/**
 * json特殊符号替换
 *
 * @param $jsonStr
 *
 * @return array|mixed
 */
function jsonSymbolReplace($jsonStr)
{
    if (!empty($jsonStr)) {
        $jsonStr = str_replace('&quot;', '"', $jsonStr);
        $jsonStr = json_decode($jsonStr, true);
    } else {
        $jsonStr = [];
    }
    return $jsonStr;
}

/**
 * 数字转换
 *
 * @param $num
 *
 * @return mixed|string
 */
function numToWord($num)
{
    $chiNum = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    $chiUni = ['', '十', '百', '千', '万', '十', '百', '千', '亿', '十', '百', '千', '万', '十', '百', '千'];
    $uniPro = [4, 8];
    $chiStr = '';

    $num_str = (string)$num;

    $count = strlen($num_str);
    $last_flag = true; //上一个 是否为0
    $zero_flag = true; //是否第一个
    $temp_num = null; //临时数字
    $uni_index = 0;

    $chiStr = '';//拼接结果
    if ($count == 2) {//两位数
        $temp_num = $num_str[0];
        $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num] . $chiUni[1];
        $temp_num = $num_str[1];
        $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
    } else {
        if ($count > 2) {
            $index = 0;
            for ($i = $count - 1; $i >= 0; $i--) {
                $temp_num = $num_str[$i];
                if ($temp_num == 0) {
                    $uni_index = $index % 15;
                    if (in_array($uni_index, $uniPro)) {
                        $chiStr = $chiUni[$uni_index] . $chiStr;
                        $last_flag = true;
                    } else {
                        if (!$zero_flag && !$last_flag) {
                            $chiStr = $chiNum[$temp_num] . $chiStr;
                            $last_flag = true;
                        }
                    }
                } else {
                    $chiStr = $chiNum[$temp_num] . $chiUni[$index % 16] . $chiStr;

                    $zero_flag = false;
                    $last_flag = false;
                }
                $index++;
            }
        } else {
            $chiStr = $chiNum[$num_str[0]];
        }
    }
    return $chiStr;
}

/**
 * 格式化时间es格式到Db格式
 *
 * @param $value
 *
 * @return null|string
 */
function parseEsTimeToDbTime($value)
{
    if (empty($value)) {
        return null;
    }
    return str_replace('+0800', '', str_replace('T', ' ', $value));
}
