<?php
  class dbFact{
      //Variables
      private $dbHost = 'localhost';
      private $dbUser = 'root';
      private $dbPass = 'root';
      private $dbName = 'wwlaca_pruebas_facturacion';

      //Conexión
      public function conectDB(){
        $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
        $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
      }
  }
?>
