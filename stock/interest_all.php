<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_interest_all());

function do_interest_all() {
    $conn = mysqli_connect(HOST, USER, PASSWD, DB) or die("无法连接到数据库");
    if (!mysqli_query($conn, "update stock set interest='y' where id>0")) {
        $msg = mysqli_error($conn);
        mysqli_close($conn);
        return array("status" => "error", "message" => $msg);
    }
    mysqli_close($conn);
}
