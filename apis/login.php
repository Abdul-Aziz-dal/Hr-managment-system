<?php
session_start();

include('../database/databaseClass.php');
$user_email = $password = "";
$error_msg = "";

// Create an instance of the DatabaseClass
$database = new DatabaseClass(); 
$connection=$database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = mysqli_real_escape_string($connection, $_POST['userEmail']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    if (!empty($user_email) && !empty($password)) {
        $condition = ['user_email' => $user_email];
        $result = $database->viewRecords('users', '*', $condition);
        if (count($result) > 0) {
            $user = $result[0];

            if ($password === $user['user_password']) {
                $_SESSION['user_id'] = $user['user_id']; 
                $_SESSION['user_email'] = $user['user_email']; 
                header('Location: ../googleAuthentication.php');
                exit();
            } else {
                $error_msg = "Invalid username or password!";
            }
        } else {
            $error_msg = "User not found!";
        }
    } else {
        $error_msg = "Please enter both username and password.";
    }
}

header('Location: ../login.php?message='.$error_msg);
 exit();
?>