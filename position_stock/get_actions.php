<?php

namespace position_stock;

require_once dirname(__FILE__) . "/../global.php";

echo get_json_result(do_get_actions());

function do_get_actions() {
    return array("data" => array("上海证券", "深圳证券"));
}
