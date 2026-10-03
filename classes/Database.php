<?php
class Database {
 private PDO $pdo;
 public function __construct(){
  $c=require __DIR__.'/../config/database.php';
  $dsn="mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}";
  $this->pdo=new PDO($dsn,$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
 }
 public function getConnection():PDO{return $this->pdo;}
}
