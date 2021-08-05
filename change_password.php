<?php session_start(); ?>
<?php require_once('Conn/connection.php'); ?>
<?php require_once('Conn/sql_verify.php'); ?>
<?php
//check user is already logged
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
}

$errors = array();
$user_id = '';
$first_name = '';
$last_name = '';
$email = '';

if (isset($_GET['user_id'])) {
    $user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
    $query = "SELECT * FROM user WHERE id={$user_id} LIMIT 1";

    $result_set = mysqli_query($connection, $query);

    if ($result_set) {
        if (mysqli_num_rows($result_set) == 1) {
            //user found
            $result = mysqli_fetch_assoc($result_set);
            $first_name = $result['first_name'];
            $last_name = $result['last_name'];
            $email = $result['email'];
        } else {
            //user not found
            header('Location: users.php?err=user_not_found');
        }
    } else {
        //query unsuccessful
        header('Location: users.php?err=query_failed');
    }
}

if (isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    //check required field
    if (empty(trim($_POST['user_id']))) {
        $errors[] = 'user id is required';
    }
    if (empty(trim($_POST['password']))) {
        $errors[] = 'password is required';
    }

    //check max length
    $req_fields = array('password');

    $max_len_fields = array('password' => 40);
    foreach ($max_len_fields as $field => $max_len) {
        if (strlen(trim($_POST[$field])) > $max_len) {
            $errors[] = $field . ' must be less than ' . $max_len . ' characters';
        }
    }
    if (empty($errors)) {
        //add new record
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $hashed_password = sha1($password);


        $query = "UPDATE user SET ";
        $query .= "password = '{$hashed_password}' ";
        $query .= "WHERE id = {$user_id} LIMIT 1";

        $result = mysqli_query($connection, $query);

        if ($result) {
            //query successful....redirect to the user page
            header('Location: users.php?user_modified=true');

        } else {
            $errors[] = 'Failed to update password ';
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
        <div class="logged">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a></div>
    </header>
</div>
<div class="container-3">
    <h2>Change Password</h2>
    <a href="users.php"> < Back to User List</a>
    <div class="form-3">
        <?php
        if (!empty($errors)) {
            echo '<div class="er">';
            echo '<b>There were errors on your form.</b><br>';
            foreach ($errors as $error) {
                echo $error . '<br>';
            }
            echo '</div>';
        }
        ?>
        <form action="change_password.php" method="POST">

            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <div class="form-group">
                <label for="">First Name:</label>
                <input type="text" class="form-control" name="first_name"
                       placeholder="Enter first name" <?php echo 'value="' . $first_name . '"'; ?> disabled>
            </div>
            <div class="form-group">
                <label for="">Last Name:</label>
                <input type="text" class="form-control" name="last_name"
                       placeholder="Enter last name" <?php echo 'value="' . $last_name . '"'; ?> disabled>
            </div>
            <div class="form-group">
                <label for="">Email:</label>
                <input type="email" class="form-control" name="email"
                       placeholder="Enter email address" <?php echo 'value="' . $email . '"'; ?> disabled>
            </div>
            <div class="form-group">
                <label for="">New Password: </label>
                <input type="password" class="form-control" name="password" id="password">
            </div>
            <div class="form-group">
                <label class="checkbox-inline"> <input type="checkbox" name="showpassword" id="showpassword">Show Password </label>
            </div>
            <button type="submit" name="submit" class="btn btn-default">Update password</button>
        </form>
    </div>
</div>

<script src="js/jquery.js"></script>
<script>
    $(document).ready(function () {
        $('#showpassword').click(function () {
            if($('#showpassword').is(':checked')){
                $('#password').attr('type', 'text');
            }else{
                $('#password').attr('type','password');
            }

        });

    });

</script>
</body>
</html>