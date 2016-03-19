<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_modify());

function do_modify() {
    if (!request_params_exist(array("id", "json"))) {
        return array("status" => "error", "message" => "缺失字段id或json");
    }
    $id = $_POST["id"];
    $param = json_decode($_POST["json"], true);
    $v = $param["v"];

    $sql = "update account set ";
    if (in_array("code", $v)) {
        $code = $param["code"];
        $sql.="code='$code',";
    }
    if (in_array("company", $v)) {
        $company = $param["company"];
        $sql.="company='$company',";
    }
    if (in_array("money", $v)) {
        $money = $param["money"];
        $sql.="money=$money,";
    }
    if (in_array("used", $v)) {
        $used = $param["used"];
        $sql.="used='$used',";
    }
    $sql = substr($sql, 0, strlen($sql) - 1) . " where id=$id";

    $connect = db_connect();
    mysqli_query($connect, $sql) or die_db_error($connect);
    mysqli_close($connect);
}
