<?php

$dbcon = new mysqli('localhost','root','', 'todolistphp'); 


if($dbcon-> connect_errno){
    die("Connection echouée"); 
    exit(); 
}else{
    $query = 
    "CREATE TABLE IF NOT EXISTS todoitem (
    item_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_title VARCHAR(50) NOT NULL,
    item_do BOOLEAN NOT NULL DEFAULT 0,
    item_archived BOOLEAN NOT NULL DEFAULT 0)
    "; 
    if(!$dbcon->query($query)){
        die ("error query (create table) " . $dbcon->error );
    } 
}




?>