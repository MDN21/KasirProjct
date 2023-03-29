<?php

require 'function.php';

if(isset($_SESSION['login'])){
    //Sudah Login
   
} else {
     header('location:login.php');
}

?>