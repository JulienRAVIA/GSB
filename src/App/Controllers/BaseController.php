<?php

namespace App\Controllers;

use \App\Utils\Session;

class BaseController
{
    public function __construct($check = null)
    {
        try {
        	(!is_null($check)) ? Session::check('type', $check) : '';
            $this->db = \App\Database::getInstance();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}