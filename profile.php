<?php

include 'conn.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('location:login.php');
    exit();
}

$email = $_SESSION['user']['email'];

$stm = "SELECT * FROM users WHERE email ='$email'";
$q = $conn->prepare($stm);
$q->execute();
$data = $q->fetch();

if ($data['email'] == '') {
    header('location:logout.php');
}

$iduser = $data["id"];
$name = $data["name"];
$gender = $data["gender"];
$role = $data["role"];
$profile_pic = $data["profile_pic"];
$role = $data["role"];


if ($gender == 'not') {
    $gender = 'Not to answer';
} elseif ($gender == 'male') {
    $gender = 'Male';
} elseif ($gender == 'female') {
    $gender = 'Female';
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="scr/profile.css">
    <title>profile</title>

</head>

<body>
    <div class="profile">
        <img src="<?php echo $data['profile_pic']; ?>" alt="Profile Picture">
        <div class="profile-details">
            <h1><?php echo $name; ?><?php if ($role == '1') echo '<img class="verified" src="scr/img/setting.png" alt="Verified" style="width: 22px; height: 22px; margin-left: 8px; vertical-align: initial;">'; ?></h1>
            <div class="profile-data">
                <label>Email:</label>
                <p><?php echo $_SESSION['user']['email']; ?></p>
            </div>
            <div class="profile-data">
                <label>Gender:</label>
                <p><?php echo $gender; ?></p>
            </div>
            <button onclick="location.href='edit_profile.php'">Edit Profile</button>
        </div>
    </div>
    <a href="logout.php">logout</a>
    <br>
    <a href="index.php">home</a>
</body>

</html>