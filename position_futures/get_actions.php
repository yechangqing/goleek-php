<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_actions());

function do_get_actions() {
    if (!request_params_exist(array("json"))) {
        return array("status" => "error", "message" => "缺失字段json");
    }
    $param = json_decode($_POST["json"], true);
    $direct = $param["direct"];
    if ($direct === "多") {
        return array("data" => array("卖出平仓 <=", "卖出平仓 >="));
    } else if ($direct === "空") {
        return array("data" => array("买入平仓 >=", "买入平仓 <="));
    } else {
        return array("status" => "error", "message" => "错误的方向值");
    }
}
