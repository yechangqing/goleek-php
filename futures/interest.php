<?php

namespace futures;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_interest());

function do_interest() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺失字段id");
    }
    $id = $_POST["id"];
    $conn = @mysqli_connect(HOST, USER, PASSWD, DB) or die_db_link();
    $stmt = "update futures set interest='y' where id=$id";
    mysqli_query($conn, $stmt) or die_db_error($conn);
    mysqli_close($conn);
}
