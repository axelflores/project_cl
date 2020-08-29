<?php

class db
{

    // used to connect to the database
    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPass = 'root';
    private $dbName = 'cdelasluces';

    // get the database connection
    public function conectDB()
    {
        try {
          $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
          $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
          $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Database Connection Error: " . $exception->getMessage();
        }
        return $dbConnection;
    }
}
