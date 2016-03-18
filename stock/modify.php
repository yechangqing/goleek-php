<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_modify());

function do_modify() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "缺失字段id或者json");
    }

    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $v = $param["v"];
    $stmt = "update stock set ";
    if (in_array("code", $v)) {
        $code = $param["code"];
        $stmt.="code='$code',";
    }
    if (in_array("name", $v)) {
        $name = $param["name"];
        $stmt.="name='$name',";
    }
    if (in_array("exchange", $v)) {
        $exchange = $param["exchange"];
        $stmt.="exchange='$exchange',";
    }
    if (in_array("interest", $v)) {
        $interest = $param["interest"];
        $stmt.="interest='$interest',";
    }
    $stmt = substr($stmt, 0, strlen($stmt) - 1) . " where id=$id";
    $conn = @mysqli_connect(SAE_MYSQL_HOST_M . ":" . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB) or die_db_link();
    mysqli_query($conn, $stmt) or die_db_error($conn);
    mysqli_close($conn);
}
