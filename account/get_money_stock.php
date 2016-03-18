<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_money_stock());

function do_get_money_stock() {
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $stmt = "select sum(money) as all_money from account where type='股票' and used='y'";
    $result = mysqli_query($conn, $stmt) or die_db_error($conn);
//    if (!$result) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }
    $money = 0;
    foreach ($result as $row) {
        $m = $row["all_money"];
        $money+=$m == null ? 0 : $m;
    }

    return array("data" => $money);
}
