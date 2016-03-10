<?php

require_once dirname(__FILE__) . "/../global.php";

class db_test_base extends PHPUnit_Extensions_Database_TestCase {

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
}
