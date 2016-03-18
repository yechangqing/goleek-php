<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_add());

function do_add() {
//从request获取信息
    $param_names = array("json");
    if (!request_params_exist($param_names)) {
        return array("status" => "error", "message" => "参数缺失");
    }
    $param = json_decode($_POST["json"], true);
    $link = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $code = get_value($param, "code");
    $company = get_value($param, "company");
    $money = get_value($param, "money");
    $type = get_value($param, "type");
    if ($code == null || $company == null || $money == null || $type == null) {
        return array("message" => "参数缺失", "status" => "error");
    }
    $stmt = "insert into account(code,company,money,type) values('$code','$company',$money,'$type')";
    $result = mysqli_query($link, $stmt) or die_db_error($link);
//    if (!$result) {
//        $msg = mysqli_error($link);
//        mysqli_close($link);
//        return array("status" => "error", "message" => $msg);
//    }
    $id = mysqli_insert_id($link);
    mysqli_close($link);
    return array("data" => $id);
}
