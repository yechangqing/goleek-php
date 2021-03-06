<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_add());

function do_add() {
    if (!request_params_exist(array("json"))) {
        return array("status" => "error", "message" => "缺失字段json");
    }
    $param = json_decode($_POST["json"], true);
    $code = $param["code"];
    $name = $param["name"];
    $exchange = $param["exchange"];
    $stmt = "insert into stock (code,name,exchange) values('$code','$name','$exchange')";
    $conn = db_connect();
    mysqli_query($conn, $stmt) or die_db_error($conn);
    $id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return array("data" => $id);
}
