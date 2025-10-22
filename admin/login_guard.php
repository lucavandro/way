<?php
session_start();
if(!isset($_SESSION["lscadmin:username"] )){
    header("Location: login.php");
}