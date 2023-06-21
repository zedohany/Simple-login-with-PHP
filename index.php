<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Style for the links */
        a {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            border: 2px solid #ccc;
            border-radius: 5px;
            background-color: #f2f2f2;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        /* Hover style for the links */
        a:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>

<body>
    <br><br><br>
    <?php
    if (isset($_SESSION['user'])) {
    ?>

        <a href="profile.php">profile</a><br><br><br>
    <?php
    } else {
    ?>
        <a href="register.php">register</a><br><br><br>
        <a href="login.php">login</a>
    <?php
    }
    ?>

    <br><br><br>
</body>

</html>