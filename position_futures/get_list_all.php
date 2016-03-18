<?php

namespace position_futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_all());

function do_get_list_all() {
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $result = mysqli_query($conn, "select * from v_position_futures order by id") or die_db_error($conn);
    $objects = array();
    foreach ($result as $row) {
        $tmp = array();
        $tmp["id"] = $row["id"];
        $tmp["contract"] = $row["contract"];
        $tmp["direct"] = $row["direct"];
        $tmp["lot"] = $row["lot"];
        $tmp["open_price"] = $row["open_price"];
        $tmp["action"] = $row["action"];
        $tmp["quit_price"] = $row["quit_price"];
        $tmp["account"] = $row["account"];
        $tmp["open_date"] = $row["open_date"];
        // 不返回is_ready_close了，客户端也相应取消这个字段
        $objects[] = $tmp;
    }
    return array("data" => $objects);
}
