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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];


    //check required field
    if (empty(trim($_POST['user_id']))) {
        $errors[] = 'user id is required';
    }
    if (empty(trim($_POST['first_name']))) {
        $errors[] = 'First name is required';
    }
    if (empty(trim($_POST['last_name']))) {
        $errors[] = 'last name is required';
    }
    if (empty(trim($_POST['email']))) {
        $errors[] = 'email is required';
    }

    //check max length
    $req_fields = array('first_name', 'last_name', 'email');

    $max_len_fields = array('first_name' => 50, 'last_name' => 100, 'email' => 100);

    foreach ($max_len_fields as $field => $max_len) {
        if (strlen(trim($_POST[$field])) > $max_len) {
            $errors[] = $field . ' must be less than ' . $max_len . ' characters';
        }
    }

    //check if email address already exist
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $query = "SELECT * FROM user WHERE email = '{$email}' AND id != {$user_id} LIMIT 1";

    $result_set = mysqli_query($connection, $query);

    if ($result_set) {
        if (mysqli_num_rows($result_set) == 1) {
            $errors[] = 'Email address already exists';
        }
    }

    if (empty($errors)) {
        //add new record
        $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);


        $query = "UPDATE user SET ";
        $query .= "first_name = '{$first_name}',";
        $query .= "last_name = '{$last_name}',";
        $query .= "email = '{$email}' ";
        $query .= "WHERE id = {$user_id} LIMIT 1";

        $result = mysqli_query($connection, $query);

        if ($result) {
            //query successful....redirect to the user page
            header('Location: users.php?user_modified=true');

        } else {
            $errors[] = 'Failed to update user ';
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
    <title>Users</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div>
    <header>
        <div class="appname"> Simple User Management </div>
        <div class="logged">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a></div>
    </header>
</div>
<div class="container-2">
    <h2>View / Modify User</h2>
    <a href="users.php"> < Back to User List</a>
    <div class="form-2">
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
        <form action="modify_user.php" method="POST">

            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <div class="form-group">
                <label for="">First Name:</label>
                <input type="text" class="form-control" name="first_name"
                       placeholder="Enter first name" <?php echo 'value="' . $first_name . '"'; ?> >
            </div>
            <div class="form-group">
                <label for="">Last Name:</label>
                <input type="text" class="form-control" name="last_name"
                       placeholder="Enter last name" <?php echo 'value="' . $last_name . '"'; ?> >
            </div>
            <div class="form-group">
                <label for="">Email:</label>
                <input type="email" class="form-control" name="email"
                       placeholder="Enter email address" <?php echo 'value="' . $email . '"'; ?> >
            </div>
            <div class="form-group">
                <label for="">Password: </label>
                <span> **********</span> | <a href="change_password.php?user_id=<?php echo $user_id; ?>"> Change Password</a>
            </div>
            <button type="submit" name="submit" class="btn btn-default">Update</button>
        </form>
    </div>
</div>
</body>
</html>