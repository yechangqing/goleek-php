<?php

namespace stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_interest());

function do_interest() {
    if (!request_params_exist(array("id"))) {
        return array("status" => "error", "message" => "缺少字段id");
    }
    $id = $_POST["id"];
    $conn = db_connect();
    mysqli_query($conn, "update stock set interest='y' where id=$id") or die_db_error($conn);
    mysqli_close($conn);
}
