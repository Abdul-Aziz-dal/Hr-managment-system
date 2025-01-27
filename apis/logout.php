<?php 
session_start();
session_destroy();
foreach($_SESSION as $k => $v){
    unset($_SESSION[$k]);
}
header('location:../login.php');