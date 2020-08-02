<?php
session_start(); 
unset($_SESSION['user']);
header("Location: ../");
//header("Location: login.php");
?> 
