<?php

namespace Fontebasso\Driver;

use Fontebasso\Interfaces\DriverInterface;
use PDO;

class Mysql implements DriverInterface
{

    public static $instance;
    private $stmt;
    private $inner;
    private $bindParams = [];

    public function __construct()
    {
        
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS'), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
        }

        return self::$instance;
    }

    public function get($fields, $table, $wheres = array())
    {
        $sql = "SELECT ";
        foreach ($fields as $field) {
            $sql .= $field . ', ';
        }
        $sql = substr($sql, 0, -2);
        $sql .= " FROM {$table}";

        if ($this->inner) {
            $sql .= $this->inner;
        }

        if ($wheres) {
            $this->resetBindParams();
            $sqlWhere = ' WHERE ';
            foreach ($wheres as $where) {
                $sqlWhere .= $where[0] . ' ' . $where[1] . ' :' . str_replace('.', '', $where[0]) . ' AND ';
            }
            $sqlWhere = substr($sqlWhere, 0, -5);
            $sql .= $sqlWhere;
        }
        
        $this->stmt = self::getInstance()->prepare($sql);
        if ($wheres) {
            foreach ($wheres as $where) {
                $this->addBindParam(str_replace('.', '', $where[0]), $where[2]);
            }
        }
        
        $this->stmt->execute($this->getBindParams());
        return $this;
    }

    public function result()
    {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inner($table, $condition)
    {
        $this->inner = " INNER JOIN {$table} ON {$condition}";
    }
    
    public function left($table, $condition)
    {
        $this->inner = " LEFT JOIN {$table} ON {$condition}";
    }

    public function row()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $values = array_values($data);

        $sql = "INSERT INTO `{$table}` (`";
        foreach ($fields as $field) {
            $sql .= $field . "`, `";
        }
        $sql = substr($sql, 0, -3) . ") VALUES ('";

        foreach ($values as $value) {
            $sql .= $value . "', '";
        }
        $sql = substr($sql, 0, -3) . ")";

        $this->stmt = self::getInstance()->prepare($sql);
        $this->stmt->execute();
        return self::getInstance()->lastInsertId();
    }

    public function update($table, $data, $wheres)
    {

        $sql = "UPDATE `{$table}` SET ";
        $this->resetBindParams();

        foreach ($data as $field => $value) {
            $sql .= $field . ' = :' . $field;
        }
        if ($wheres) {
            $this->resetBindParams();
            $sqlWhere = ' WHERE ';
            foreach ($wheres as $where) {
                $sqlWhere .= $where[0] . ' ' . $where[1] . ' :' . $where[0] . ' AND ';
            }
            $sqlWhere = substr($sqlWhere, 0, -5);
            $sql .= $sqlWhere;
        }
        
        $this->stmt = self::getInstance()->prepare($sql);

        if ($wheres) {
            foreach ($wheres as $where) {
                $this->addBindParam($where[0], $where[2]);
            }
        }

        $this->stmt->execute($this->getBindParams());
        return $this->stmt->rowCount();
    }
    
    public function delete($table, $wheres)
    {
        $sql = "DELETE FROM `{$table}`";

        if ($wheres) {
            $this->resetBindParams();
            $sqlWhere = ' WHERE ';
            foreach ($wheres as $where) {
                $sqlWhere .= $where[0] . ' ' . $where[1] . ' :' . $where[0] . ' AND ';
            }
            $sqlWhere = substr($sqlWhere, 0, -5);
            $sql .= $sqlWhere;
            $this->addBindParam($where[0], $where[2]);
        }
        
        $this->stmt = self::getInstance()->prepare($sql);
        $this->stmt->execute($this->getBindParams());
        return $this->stmt->rowCount();
    }
    
    private function addBindParam($ind, $val)
    {
        $this->bindParams[':' . $ind] = $val;
    }
    
    private function getBindParams()
    {
        return $this->bindParams;
    }
    
    private function resetBindParams()
    {
        $this->bindParams = [];
    }

}
