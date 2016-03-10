<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_add());

function do_add() {
    if (!request_params_exist(array("json"))) {
        return array("status" => "error", "message" => "参数缺失");
    }
    $params = json_decode($_POST["json"], true);
    $code = $params["code"];
    $name = $params["name"];
    $margin = $params["margin"];
    $unit = $params["unit"];
    $min = $params["min"];
    $exchange = $params["exchange"];
    $stmt = "insert into futures (code,name,margin,unit,min,exchange) values ('$code','$name',$margin,$unit,$min,'$exchange')";
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    $result = mysqli_query($conn, $stmt);
    if (!$result) {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $error);
    }
    $id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return array("data" => $id);
}
