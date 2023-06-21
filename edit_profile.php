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

$iduser = $data["id"];
$namee = $data["name"];
$gender = $data["gender"];
$profile_pic = $data["profile_pic"];


if ($gender == 'not') {
    $gender = 'Not to answer';
} elseif ($gender == 'male') {
    $gender = 'Male';
} elseif ($gender == 'female') {
    $gender = 'Female';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the form data
    $errors = array();
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($gender)) {
        $errors[] = 'Gender is required';
    }
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = 'Current password is required';
        }
        if (empty($confirm_password)) {
            $errors[] = 'Confirm password is required';
        }
        if ($new_password != $confirm_password) {
            $errors[] = 'New password and confirm password do not match';
        }
        // Check if the current password is correct
        $stm = "SELECT password FROM users WHERE email='$email'";
        $q = $conn->prepare($stm);
        $q->execute();
        $result = $q->fetch();
        if (!password_verify($current_password, $result['password'])) {
            $errors[] = 'Current password is incorrect';
        }
    }

        // handle profile picture upload
        if ($_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = array("jpg", "jpeg", "png", "gif");
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                    // file uploaded successfully, save path to database
                    $profile_pic = $target_file;
                } else {
                    $errors[] = "Error uploading profile picture";
                }
            } else {
                $errors[] = "Invalid file type for profile picture";
            }
        }

    // If there are no errors, update the user's profile information in the database
    if (empty($errors)) {
        $stm = "UPDATE users SET name='$name', gender='$gender', profile_pic='$profile_pic' WHERE email='$email'";
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stm = "UPDATE users SET name='$name', gender='$gender', password='$hashed_password', profile_pic='$profile_pic' WHERE email='$email'";
        }
        $q = $conn->prepare($stm);
        $q->execute();

        // Redirect the user back to the profile page
        header('location: profile.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="scr/edit_profile.css">
</head>

<body>
    <!-- <h1>Edit Profile</h1> -->
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Edit photo:</label>
        <input type="file" name="profile_pic" id="profile_pic">
        <div class="profile">
            <img src="<?php echo $data['profile_pic']; ?>" alt="Profile Picture">
        </div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $namee; ?>"><br>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="not" <?php if ($data['gender'] == 'not') echo 'selected'; ?>>Not to answer</option>
            <option value="male" <?php if ($data['gender'] == 'male') echo 'selected'; ?>>Male</option>
            <option value="female" <?php if ($data['gender'] == 'female') echo 'selected'; ?>>Female</option>
        </select>
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password"><br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password"><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password"><br>
        <?php if (!empty($errors)) : ?>
            <div class="error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <button type="submit">Save Changes</button>
        <button id="btn2" type="cancel" onclick="location.href='profile.php'">Cancel</button>
    </form>
</body>

</html>