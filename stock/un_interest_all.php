<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_un_interest_all());

function do_un_interest_all() {
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    mysqli_query($conn, "update stock set interest='n' where id>0") or die_db_error($conn);
//    if (!) {
//        $msg = mysqli_error($conn);
//        mysqli_close($conn);
//        return array("status" => "error", "message" => $msg);
//    }
    mysqli_close($conn);
}
