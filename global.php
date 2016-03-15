<?php

/**
 * 定义全局变量和属性
 */
DEFINE('HOST', 'localhost:3303');
DEFINE('USER', 'yecq');
DEFINE('PASSWD', '801111');
DEFINE('DB', 'goleek_test');

function get_json_result($arr) {
    if ($arr == null || !is_array($arr)) {
        return '{"status":"ok","message":"ok"}';
    }

    $sret = array();
    $sret["status"] = array_key_exists("status", $arr) ? $arr["status"] : "ok";
    $sret["message"] = array_key_exists("message", $arr) ? $arr["message"] : $sret["status"];
    $data = array_key_exists("data", $arr) ? $arr["data"] : null;

    $ret = array();
    $ret[0] = $sret;
    if ($data != null && (!is_array($data) || count($data) > 0)) {
        $ret[1] = $data;
    }

    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

// 检查有没有需要的参数
function request_params_exist($param_names) {
    foreach ($param_names as $name) {
        if (!array_key_exists($name, $_POST) || $_POST[$name] == null) {
            return false;
        }
    }
    return true;
}

// 从数组中返回值，不存在则返回null
function get_value($array, $key) {
    if ($key == null || $array == null) {
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

// 从param里解析出update
//function analysis_sql_update($table, $id, $params) {
//    if ($table == null || $id == null || $params == null) {
//        return null;
//    }
//    if (!is_array($params) || count($params) == 0) {
//        return null;
//    }
//
//    $sql = "";
//    $keys = array_keys($params);
//    foreach ($keys as $k) {
//        $sql.=$k . "=" . $params[$k] . ",";
//    }
//
//    $sql = "update " . $table . " set " . substr($sql, 0, strlen($sql) - 1) . " where id=" . $id;
//    return $sql;
//}

// 从param里解析出insert
//function analysis_sql_insert($table, $params) {
//    if ($table == null || $params == null) {
//        return null;
//    }
//    if (!is_array($params) || count($params) == 0) {
//        return null;
//    }
//
//    $sql = "";
//
//    return $sql;
//}
