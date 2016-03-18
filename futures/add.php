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
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link() or die_db_link();
    $result = mysqli_query($conn, $stmt) or die_db_error($conn);
    $id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return array("data" => $id);
}
