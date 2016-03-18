<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_un_interest());

function do_un_interest() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺少字段id");
    }
    $id = $_POST["id"];
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    mysqli_query($conn, "update stock set interest='n' where id=$id") or die_db_error($conn);
//    if (!) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }
    mysqli_close($conn);
}
