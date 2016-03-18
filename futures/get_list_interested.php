<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_list_interested());

function do_get_list_interested() {
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $result = mysqli_query($conn, "select * from futures where interest='y' order by exchange,code") or die_db_error($conn);
    $objects = array();
    foreach ($result as $row) {
        $objects[] = $row;
    }
    mysqli_close($conn);
    return array("data" => $objects);
}
