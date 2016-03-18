<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_clone());

function do_clone() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "参数id或json缺失");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $new_code = $param["newCode"];

    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $result = mysqli_query($conn, "select * from futures where id=$id") or die_db_error($conn);
    $old_contract = mysqli_fetch_assoc($result);
    $old_code = $old_contract["code"];
    // 名称不能相同
    if ($old_code == $new_code) {
        return array("status" => "fail", "message" => "新合约名称重复");
    }
    // 且必须跟原来的是同一个合约
    $old_code_prefix = substr($old_code, 0, strlen($old_code) - 4);
    if (substr($new_code, 0, strlen($new_code) - 4) != $old_code_prefix) {
        return array("status" => "fail", "message" => "新合约必须是" . $old_code_prefix);
    }
    $name = $old_contract["name"];
    $margin = $old_contract["margin"];
    $unit = $old_contract["unit"];
    $min = $old_contract["min"];
    $exchange = $old_contract["exchange"];
    $stmt = "insert into futures (code,name,margin,unit,min,exchange) "
            . "values('$new_code','$name',$margin,$unit,$min,'$exchange')";
    mysqli_query($conn, $stmt) or die_db_error($conn);
    $new_id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return array("data" => $new_id);
}
