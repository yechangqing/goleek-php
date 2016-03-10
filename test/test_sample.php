<?php

class sample extends PHPUnit_Framework_TestCase {

    public function testMethod1() {
        $a = "1";
        $this->assertEquals($a, "1");
    }

    public function testMethod2() {
        $b = null;
        $this->assertEmpty($b);
    }

}

DEFINE('HOST', 'localhost:3306');
DEFINE('USER', 'yecq');
DEFINE('PASSWD', '801111');
DEFINE('DB', 'goleek_test');

class sample_db extends PHPUnit_Extensions_Database_TestCase {

    // 只实例化 pdo 一次，供测试的清理和装载基境使用
    static private $pdo = null;
    // 对于每个测试，只实例化 PHPUnit_Extensions_Database_DB_IDatabaseConnection 一次
    private $conn = null;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection() {
        if ($this->conn == null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DB, USER, PASSWD);
            }
//            $this->conn = $this->createDefaultDBConnection(self::$pdo, ':memory:');
            $this->conn = $this->createDefaultDBConnection(self::$pdo);
        }
        return $this->conn;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet() {
        return $this->createFlatXMLDataSet('data.xml');
    }

    public function test_do_get_list_futures() {
        $number = $this->getConnection()->getRowCount("account", "type='期货'");
        $this->assertEquals(2, $number);
    }

    public function test_do_get_list_futures_used() {
        $number = $this->getConnection()->getRowCount("account", "type='期货' and used='y'");
        $this->assertEquals(1, $number);
    }

    public function test1_do_get_list_futures_used() {
        $result = $this->getConnection()->createQueryTable("account", "select * from account where type='期货' and used='y'");
        $row = $result->getRow(0);
        $this->assertEquals("100003109", $row["code"]);
        $this->assertEquals("新纪元期货", $row["company"]);
    }

    public function test_a() {
        $connect = mysqli_connect(HOST, USER, PASSWD, DB);
        $result = mysqli_query($connect, "select * from account");
        $this->assertEquals(3, mysqli_num_rows($result));
    }

    public function test_b() {
        $connect = mysqli_connect(HOST, USER, PASSWD, DB);
        mysqli_query($connect, "insert into account(id,code,company,money,used,type) values(10,'1234','Company1',30000,'n','期货')");
        $result = mysqli_query($connect, "select * from account");
        $this->assertEquals(4, mysqli_num_rows($result));
    }

    public function test_c() {
        $connect = mysqli_connect(HOST, USER, PASSWD, DB);
        mysqli_query($connect, "delete from account where id=2");
        $result = mysqli_query($connect, "select * from account");
        $this->assertEquals(2, mysqli_num_rows($result));
    }

    public function test_d() {
        $connect = mysqli_connect(HOST, USER, PASSWD, DB);
        mysqli_query($connect, "update account set money=999999 where id=2");
        $result = mysqli_query($connect, "select * from account where id=2");
        $money = mysqli_fetch_array($result)["money"];
        $this->assertEquals(999999, $money);
    }

}
