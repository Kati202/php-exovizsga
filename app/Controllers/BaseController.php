<?php
namespace App\Controllers;

use App\Config;

class BaseController {
    protected $db;

    public function __construct() {
        
        $this->db = new \MongoDB\Client(
            'mongodb://' . Config::MONGODB_HOST . ':' . Config::MONGODB_PORT
        );
    }
}