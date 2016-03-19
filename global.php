<?php

/**
 * 定义全局变量和属性
 */
// 部署到sae时删除以下配置
define('SAE_MYSQL_HOST_M', 'localhost:3306');
define('SAE_MYSQL_USER', 'yecq');
define('SAE_MYSQL_PASS', '801111');
define('SAE_MYSQL_PORT', '3303');
define('SAE_MYSQL_DB', 'goleek_sae_php_test');

// 输入口令方可访问
$pass = get_param_from_request("pass");
if ($pass !== "本宝宝") {
    die("你无权限访问");
}
unset($_POST["pass"]);

function get_json_result($arr) {
    if ($arr === null || !is_array($arr)) {
        return '[{"status":"ok","message":"ok"}]';
    }

    $sret = array();
    $sret["status"] = array_key_exists("status", $arr) ? $arr["status"] : "ok";
    $sret["message"] = array_key_exists("message", $arr) ? $arr["message"] : $sret["status"];
    $data = array_key_exists("data", $arr) ? $arr["data"] : null;

    $ret = array();
    $ret[0] = $sret;
    if ($data !== null && (!is_array($data) || count($data) > 0)) {
        $ret[1] = $data;
    }

    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

// 检查有没有需要的参数
function request_params_exist($param_names) {
    foreach ($param_names as $name) {
        if (!array_key_exists($name, $_POST) || $_POST[$name] === null) {
            return false;
        }
    }
    return true;
}

// 从数组中返回值，不存在则返回null
function get_value($array, $key) {
    if ($key === null || $array === null) {
        return null;
    }

    if (!array_key_exists($key, $array)) {
        return null;
    }
    return $array[$key];
}

function get_param_from_request($key) {
    return get_value($_POST, $key);
}

// 不得不写一个数据库连接封装代码了
function db_connect() {
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT) or die_db_link();
    $charset = "utf8";
    mysqli_set_charset($conn, $charset) or die_db_error($conn, "字符集" . $charset . "设置失败");
    return $conn;
}

function die_db_error($conn) {
    $args = func_get_args();
    $num = count($args);
    $msg = mysqli_error($conn);
    if ($num > 1) {
        $msg = $args[1];
    }
    mysqli_close($conn);
    die(get_json_result(array("status" => "error", "message" => $msg)));
}

function die_db_link() {
    $args = func_get_args();
    $num = count($args);
    $msg = $num === 0 ? "数据库连接失败" : $args[0];
    die(get_json_result(array("status" => "error", "message" => $msg)));
}
