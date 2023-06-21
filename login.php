<?php
session_start();
if (isset($_SESSION['user'])) {
    header('location:profile.php');
    exit();
}
if (isset($_POST['submit'])) {
    include 'conn.php';
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $errors = [];

    // validate email
    if (empty($email)) {
        $errors[] = "Email must be written";
    }


    // validate password
    if (empty($password)) {
        $errors[] = "Password must be typed";
    }



    // insert or errros 
    if (empty($errors)) {

        // echo "check db";

        $stm = "SELECT * FROM users WHERE email ='$email'";
        $q = $conn->prepare($stm);
        $q->execute();
        $data = $q->fetch();
        if (!$data) {
            $errors[] = "login error";
        } else {

            $password_hash = $data['password'];

            if (!password_verify($password, $password_hash)) {
                $errors[] = "The password is incorrect";
            } else {
                $_SESSION['user'] = [
                    "name" => $data['name'],
                    "email" => $email,
                ];
                header('location:profile.php');
            }
        }
    }
}

?>



<form action="login.php" method="POST">
    <?php
    if (isset($errors)) {
        if (!empty($errors)) {
            foreach ($errors as $msg) {
                echo $msg . "<br>";
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <style>
            /* Style for the form */
            form {
                max-width: 400px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background-color: #f2f2f2;
            }

            /* Style for the input fields */
            input[type="text"],
            input[type="password"] {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                box-sizing: border-box;
                border: 2px solid #ccc;
                border-radius: 4px;
                background-color: #fff;
                font-size: 16px;
            }

            /* Style for the submit button */
            input[type="submit"] {
                background-color: #4CAF50;
                color: #fff;
                padding: 12px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }

            /* Style for the error messages */
            .error {
                color: red;
                font-size: 14px;
                margin-bottom: 10px;
            }
        </style>
    </head>

    <body>
        <input type="text" value="<?php if (isset($_POST['email'])) {
                                        echo $_POST['email'];
                                    } ?>" name="email" placeholder="email"><br><br>
        <input type="password" name="password" placeholder="password"><br><br>
        <input type="submit" name="submit" value="Login">
        <br><br>
        <a href="register.php">register</a><br><br><br>
</form>
</body>