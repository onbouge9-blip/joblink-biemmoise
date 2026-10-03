<?php
class Auth {
 public static function hashPassword(string $p):string{return password_hash($p,PASSWORD_DEFAULT);}
 public static function verifyPassword(string $p,string $h):bool{return password_verify($p,$h);}
}
