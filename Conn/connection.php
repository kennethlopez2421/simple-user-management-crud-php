<?php

    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '123456';
    $dbname = 'userdb';

    $connection = mysqli_connect( $dbhost, $dbuser, $dbpass , $dbname);

    //check the connection

    if(mysqli_connect_errno()){
        die('Database connection failed' . mysqli_connect_error());
    }

