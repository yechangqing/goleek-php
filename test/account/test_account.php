<?php

require_once dirname(__FILE__) . "/../db_test_base.php";    // 这个路径比较奇怪，用../db_test_base.php就找不到
require_once dirname(__FILE__) . "/../../account/get_list_futures.php";
require_once dirname(__FILE__) . "/../../account/get_list_futures_used.php";
require_once dirname(__FILE__) . "/../../account/add.php";
require_once dirname(__FILE__) . "/../../account/modify.php";
require_once dirname(__FILE__) . "/../../account/remove.php";

class test_account extends db_test_base {

    public function test_do_get_list_futures_data() {
        $objects = account\do_get_list_futures_data();
        $this->assertEquals(2, count($objects["data"]));
    }

    public function test_do_get_list_futures_used_data() {
        $objects = account\do_get_list_futures_used_data();
        $this->assertEquals(1, count($objects["data"]));
    }

    public function test_add() {
        $_POST["json"] = json_encode(array("code" => "12345432", "company" => "光大期货", "money" => 70000, "type" => "期货"), JSON_UNESCAPED_UNICODE);
        $result = account\do_add();
        $this->assertGreaterThan(0, $result["data"]);
    }

    public function test1_add() {
        $_POST["json"] = json_encode(array("code" => "12345432", "company" => "光大期货", "money" => 70000), JSON_UNESCAPED_UNICODE);
        $result = account\do_add();
        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("参数缺失", $result["message"]);
    }

    public function test_do_modify() {
        $param = array("code" => "909090", "money" => 650001, "v" => ["code", "money"]);
        $_POST["json"] = json_encode($param, JSON_UNESCAPED_UNICODE);
        $_POST["id"] = 1;
        account\do_modify();
        $result = $this->getConnection()->createQueryTable("account", "select * from account where id=1");
        $this->assertEquals(1, $result->getRowCount());
        $row = $result->getRow(0);
        $this->assertEquals("909090", $row["code"]);
        $this->assertEquals(650001, $row["money"]);
    }

    public function test1_do_modify() {
        $param = array("code" => "909090", "money" => 650001, "v" => ["code", "money"]);
        $_POST["json"] = json_encode($param, JSON_UNESCAPED_UNICODE);
        $ret = account\do_modify();
        $this->assertEquals("缺失字段id或json", $ret["message"]);
        $this->assertEquals("error", $ret["status"]);
    }

    public function test2_do_modify() {
        $_POST["id"] = 1;
        $ret = account\do_modify();
        $this->assertEquals("缺失字段id或json", $ret["message"]);
    }

    public function test_do_remove() {
        $_POST["id"] = 2;
        account\do_remove();
        $ret = $this->getConnection()->createQueryTable("account", "select * from account where id=2");
        $number = $ret->getRowCount();
        $this->assertEquals(0, $number);
    }

    public function test1_do_remove() {
        $str = account\do_remove();
        $this->assertEquals("缺失字段id", $str["message"]);
    }

}
