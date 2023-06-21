<?php
session_start();
if (isset($_SESSION['user'])) {
    header('location:profile.php');
    exit();
}
if (isset($_POST['submit'])) {
    include 'conn.php';
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $gender = $_POST['gender'];

    $errors = [];
    // validate name
    if (empty($name)) {
        $errors[] = "The name must be written";
    } elseif (strlen($name) > 100) {
        $errors[] = "The name must not be greater than 100 characters";
    }

    // handle profile picture upload
    if ($_FILES['profile-pic']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile-pic"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $target_file)) {
                // file uploaded successfully, save path to database
                $profile_pic = $target_file;
            } else {
                $errors[] = "Error uploading profile picture";
            }
        } else {
            $errors[] = "Invalid file type for profile picture";
        }
    }

    // validate email    
    if (empty($email)) {
        $errors[] = "Email must be written";
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $errors[] = "The email is not valid";
    }

    $stm = "SELECT email FROM users WHERE email ='$email'";
    $q = $conn->prepare($stm);
    $q->execute();
    $data = $q->fetch();

    if ($data) {
        $errors[] = "Email already exists";
    }


    // validate password
    if (empty($password)) {
        $errors[] = "Password must be typed";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must not be less than 6 characters";
    }



    // insert or errros 
    if (empty($errors)) {
        // echo "insert db";
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stm = "INSERT INTO users (name,email,password,gender,profile_pic) VALUES ('$name','$email','$password','$gender','$profile_pic')";
        $conn->prepare($stm)->execute();
        $_POST['name'] = '';
        $_POST['email'] = '';

        $_SESSION['user'] = [
            "name" => $name,
            "email" => $email,
        ];
        header('location:profile.php');
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
        input[type="email"],
        input[type="password"],
        select {
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

        /* Style for the label */
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }

        /* Style for the select dropdown */
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            padding-right: 30px;
        }
    </style>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <?php
        if (isset($errors)) {
            if (!empty($errors)) {
                foreach ($errors as $msg) {
                    echo $msg . "<br>";
                }
            }
        }
        ?>
</head>

<body>
    <!-- existing form fields -->
    <label for="profile-pic">Profile Picture:</label>
    <input type="file" name="profile-pic" id="profile-pic">
    <br><br>
    <input type="text" value="<?php if (isset($_POST['name'])) {
                                    echo $_POST['name'];
                                } ?>" name="name" placeholder="name"><br><br>
    <input type="email" value="<?php if (isset($_POST['email'])) {
                                    echo $_POST['email'];
                                } ?>" name="email" placeholder="email"><br><br>
    <input type="password" name="password" placeholder="password"><br><br>
    <label for="cars">Choose a Gender:</label>
    <select id="gen" value="<?php if (isset($_POST['gender'])) {
                                echo $_POST['gender'];
                            } ?>" name="gender" placeholder="gender">
        <option value="male">male</option>
        <option value="female">female</option>
        <option value="not">not</option>
    </select><br><br>
    <input type="submit" name="submit" value="Register">
    <br><br>
    <a href="login.php">login</a><br><br><br>
    </form>
</body>

</html>