<?php session_start(); ?>
<?php require_once('Conn/connection.php'); ?>
<?php require_once('Conn/sql_verify.php'); ?>
<?php
        //check user is already logged
        if (!isset($_SESSION['user_id'])) {
         header('Location: index.php');
     }

     if(isset($_GET['user_id'])){
         //get user information
         $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);

         if($user_id == $_SESSION['user_id']){
             //cannot delete user
             header('Location: users.php?err=cannot_delete');
         }else{
             //you can delete user
             $query = "UPDATE user SET is_deleted = 1 WHERE id = {$user_id} LIMIT 1";
             $result = mysqli_query($connection, $query);

             if($result){
                 //user deleted
                 header('Location: users.php?msg=deleted');
             }else{
                 header('Location: users.php?msg=delete_failed');
             }
         }
     }else{
         header('Location: users.php');

     }
