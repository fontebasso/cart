<?php

namespace Fontebasso\Core;

class Controller
{
    public $db;
    
    public function __construct()
    {
        $this->db = new Database;
    }
}
