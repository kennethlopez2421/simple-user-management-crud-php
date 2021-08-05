<?php session_start(); ?>
<?php require_once ('Conn/connection.php'); ?>
<?php require_once ('Conn/sql_verify.php'); ?>
<?php

    if(isset($_POST['submit'])){

    $errors = array();

    if(!isset($_POST['email']) || strlen(trim($_POST['email'])) < 1 ){
        $errors[] = 'Username is Empty or Invalid';
    }

    if(!isset($_POST['password']) || strlen(trim($_POST['password'])) < 1 ){
        $errors[] = 'Password is Empty or Invalid';
    }

    if(empty($errors)){
        $email    = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $hashed_password = sha1($password);

        $query = "SELECT * FROM user 
                  WHERE email = '{$email}' 
                  AND password = '{$hashed_password}' 
                  LIMIT 1";

        $result_set = mysqli_query($connection, $query);

        verify_query($result_set);
            if(mysqli_num_rows($result_set) == 1){
                // valid user found
                $user = mysqli_fetch_assoc($result_set);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                //update last login date & time

                $query = "UPDATE user SET last_login = NOW()";
                $query .= "WHERE id = {$_SESSION['user_id']} LIMIT 1";

                $result_set = mysqli_query($connection,$query);

                verify_query($result_set);
                //redirect page
                header('Location: users.php');

            }else{
                //user name and password invalid
                $errors[]= 'Invalid Username or Password';
            }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Simple User Management</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="name">
        <h1>Simple User Management System</h1>
     </div>
        <div class="login">

        <form action="index.php" method="POST">

            <fieldset>
                <legend><h1>Login</h1></legend>

                <?php
                    if(isset($errors) && !empty($errors)){
                        echo '<p class="error">Invalid Username or Password</p>';
                    }
                ?>

                <?php
                    if(isset($_GET['logout'])){
                        echo '<p class="info">You have successfully logout</p>';
                    }

                ?>

                <p>
                    <label for="">Username:</label><br>
                    <input type="text" name="email" id="" placeholder="Email Address" required autofocus>
                </p>

                <p>
                    <label for="">Password:</label><br>
                    <input type="password" name="password" id="" placeholder="Enter Password" required autofocus>
                </p>

                <p>
                    <button type="submit" name="submit">Login</button>
                </p>


            </fieldset>
        </form>
    </div>


</body>
</html>
<?php mysqli_close($connection); ?>


