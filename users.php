<?php session_start(); ?>
<?php require_once ('Conn/connection.php'); ?>
<?php require_once ('Conn/sql_verify.php'); ?>
<?php
    //check user is already logged
    if(!isset($_SESSION['user_id'])){
        header('Location: index.php');
    }

    $user_list = '';

    //query the list of users

    $query = "SELECT * FROM user WHERE is_deleted = 0 ORDER BY first_name";
    $users = mysqli_query($connection,$query);

    verify_query($users);
        while ($user = mysqli_fetch_assoc($users)){
            $user_list .= "<tr>";
            $user_list .= "<td>{$user['first_name']}</td>";
            $user_list .= "<td>{$user['last_name']}</td>";
            $user_list .= "<td>{$user['last_login']}</td>";
            $user_list .= "<td><a href=\"modify_user.php?user_id={$user['id']}\">Edit</a></td>";
            $user_list .= "<td><a href=\"delete_user.php?user_id={$user['id']}\" 
                            onclick=\"return confirm('Are you sure?');\">Delete</a></td>";
            $user_list .= "</tr>";
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
             <div class="appname"> Simple User Management </div>
             <div class="logged">Welcome <?php echo $_SESSION['first_name']; ?>! <a href="logout.php">Logout</a> </div>
        </header>
    </div>

    <div class="container">
        <h2>Users Table</h2>
        <a href="add_user.php">+ Add New User</a>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Last Login</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>

            <?php
                 echo $user_list;
            ?>
            </thead>
        </table>
    </div>

</body>
</html>

