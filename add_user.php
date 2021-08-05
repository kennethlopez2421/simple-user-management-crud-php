<?php session_start(); ?>
<?php require_once ('Conn/connection.php'); ?>
<?php require_once ('Conn/sql_verify.php'); ?>
<?php
//check user is already logged
if(!isset($_SESSION['user_id'])){
    header('Location: index.php');
}
?>
<?php

    $errors = array();
    $first_name = '';
    $last_name = '';
    $email = '';
    $password = '';

    if(isset($_POST['submit'])){

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];


        //check required field
        if(empty(trim($_POST['first_name']))) {
            $errors[] = 'First name is required';
        }
        if(empty(trim($_POST['last_name']))) {
            $errors[] = 'last name is required';
        }
        if(empty(trim($_POST['email']))) {
            $errors[] = 'email is required';
        }
        if(empty(trim($_POST['password']))) {
            $errors[] = 'password is required';
        }
            //check max length
        $req_fields = array('first_name','last_name','email','password');

        $max_len_fields = array('first_name' => 50,'last_name' =>100,'email' => 100,'password' =>40);

        foreach ($max_len_fields as $field => $max_len){
            if(strlen(trim($_POST[$field])) > $max_len){
                $errors[] = $field . ' must be less than ' . $max_len . ' characters';
            }
        }

        //check if email address already exist
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $query = "SELECT * FROM user WHERE email = '{$email}' LIMIT 1";

        $result_set = mysqli_query($connection, $query);

        if($result_set){
            if(mysqli_num_rows($result_set) == 1){
                $errors[] = 'Email address already exists';
            }
        }

        if(empty($errors)){
            //add new record
            $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
            $password = mysqli_real_escape_string($connection, $_POST['password']);

            $hashed_password = sha1($password);

            $query = "INSERT INTO user ( ";
            $query .= "first_name, last_name, email, password, is_deleted";
            $query .= ") VALUES (";
            $query .= "'{$first_name}','{$last_name}','{$email}','{$hashed_password}', 0";
            $query .= ")";

            $result = mysqli_query($connection,$query);

            if($result){
                //query successful....redirect to the user page
                header('Location: users.php?user_added=true');

            }else{
                $errors[]= 'Failed to add user ';
            }


        }



    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
    <div>
        <header>
             <div class="appname"> User Managment System</div>
             <div class="logged">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a> </div>
        </header>
    </div>
    <div class="container-2">
        <h2>Add User</h2>
        <a href="users.php"> < Back to User List</a>
        <div class="form-2">
            <?php
            if(!empty($errors)){
                echo '<div class="er">';
                echo '<b>There were errors on your form.</b><br>';
                foreach ($errors as $error){
                    echo $error . '<br>';
                }
                echo '</div>';
            }
            ?>
        <form action="add_user.php" method="POST">
            <div class="form-group">
                <label for="">First Name:</label>
                <input type="text" class="form-control" name="first_name" placeholder="Enter first name" <?php echo 'value="' . $first_name . '"';?> >
            </div>
            <div class="form-group">
                <label for="">Last Name:</label>
                <input type="text" class="form-control" name="last_name" placeholder="Enter last name" <?php echo 'value="' . $last_name . '"';?> >
            </div>
            <div class="form-group">
                <label for="">Email:</label>
                <input type="email" class="form-control" name="email" placeholder="Enter email address" <?php echo 'value="' . $email . '"';?> >
            </div>
            <div class="form-group">
                <label for="">New Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" <?php echo 'value="' . $password . '"';?> >
            </div>
            <button type="submit" name="submit" class="btn btn-default">Submit</button>
        </form>
        </div>
    </div>
</body>
</html>