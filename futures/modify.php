<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_modify());

function do_modify() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "参数id或json缺失");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $v = $param["v"];
    $stmt = "set ";
    if (in_array("code", $v)) {
        $code = $param["code"];
        $stmt.="code='$code',";
    }
    if (in_array("name", $v)) {
        $name = $param["name"];
        $stmt.="name='$name',";
    }
    if (in_array("margin", $v)) {
        $margin = $param["margin"];
        $stmt.="margin=$margin,";
    }
    if (in_array("unit", $v)) {
        $unit = $param["unit"];
        $stmt.="unit=$unit,";
    }
    if (in_array("min", $v)) {
        $min = $param["min"];
        $stmt.="min=$min,";
    }
    if (in_array("exchange", $v)) {
        $exchange = $param["exchange"];
        $stmt.="exchange='$exchange',";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 1);
    $stmt = "update futures " . $stmt . " where id=$id";

    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    mysqli_query($conn, $stmt) or die_db_error($conn);
    mysqli_close($conn);
}
