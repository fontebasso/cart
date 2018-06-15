<?php

namespace Fontebasso\Core;

class Database
{
    private $fields;
    private $table;
    private $where = array();
    private $driver;
    
    public function __construct()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
        $dotenv->load();
        
        $driver = getenv('DB_DRIVER');
        
        $driver = '\\Fontebasso\\Driver\\' . ucfirst($driver);
        $this->driver = new $driver();
    }
    
    public function select($fields)
    {
        $this->fields = explode(',', $fields);
    }
    
    public function from($table)
    {
        $this->table = $table;
    }
    
    public function where($condition, $value)
    {
        $criteria = explode(' ', $condition);
        $where = array(
            $criteria[0],
            ((isset($criteria[1])) ? $criteria[1] : '='),
            $value,
        );
        $this->where[] = $where;
    }
    
    public function get()
    {
        $where = $this->where;
        $this->where = array();
        $this->driver->get($this->fields, $this->table, $where);
        return $this->driver;
    }
    
    public function insert($table, $data)
    {
        return $this->driver->insert($table, $data);
    }
    
    public function update($table, $data)
    {
        $where = $this->where;
        $this->where = array();
        return $this->driver->update($table, $data, $where);
    }
    
    public function delete($table)
    {
        $where = $this->where;
        $this->where = array();
        return $this->driver->delete($table, $where);
    }
    
    public function inner($table, $condition)
    {
        $this->driver->inner($table, $condition);
    }
    
    public function left($table, $condition)
    {
        $this->driver->left($table, $condition);
    }
}
