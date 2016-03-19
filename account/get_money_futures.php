<?php

namespace account;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_money_futures());

function do_get_money_futures() {
    $conn = db_connect();
    $stmt = "select sum(money) as all_money from account where type='期货' and used='y'";
    $result = mysqli_query($conn, $stmt) or die_db_error($conn);
    $money = 0;
    foreach ($result as $row) {
        $m = $row["all_money"];
        $money+=$m == null ? 0 : $m;
    }

    return array("data" => $money);
}
