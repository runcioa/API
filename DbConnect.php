<?php

require_once('./db-cred.php');

class DbConnect
{
    private $dns;


    public function connect()
    {
        $this->dns = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

        try {
            $conn = new PDO($this->dns, DB_USER, DB_PASS);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
}

