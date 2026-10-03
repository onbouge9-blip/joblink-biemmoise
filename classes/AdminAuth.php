<?php
class AdminAuth { public static function requireLogin():void{if(!Session::get('admin_connecte')){header('Location: login.php');exit;}} }
