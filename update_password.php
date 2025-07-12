<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = $conn->prepare("SELECT password FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Update Password
$message = "";
if (isset($_POST['update_password'])) {
    $old_pass = md5($_POST['old_pass']);
    $new_pass = md5($_POST['new_pass']);
    $confirm_pass = md5($_POST['confirm_pass']);

    if ($old_pass != $user['password']) {
        $message = "Old password is incorrect!";
    } elseif ($new_pass != $confirm_pass) {
        $message = "New password and Confirm password do not match!";
    } else {
        $update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_pass->execute([$new_pass, $user_id]);
        $message = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6dd5ed,  #f5f5dc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h2, .title {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            align-self: flex-start;
            font-weight: 600;
            margin-top: 15px;
            color: #444;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input[type="password"]:focus {
            border-color: #2193b0;
            outline: none;
            box-shadow: 0 0 5px rgba(33, 147, 176, 0.5);
        }

        input[type="submit"], .option-btn {
            margin-top: 20px;
            padding: 12px;
            font-size: 18px;
            background-color: #2193b0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        input[type="submit"]:hover, .option-btn:hover {
            background-color: #176e87;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
                margin: 10px;
            }

            .title {
                font-size: 22px;
            }

            input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="title">Update Password</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="old_pass">Old Password:</label>
        <input type="password" name="old_pass" placeholder="Enter current password" required>

        <label for="new_pass">New Password:</label>
        <input type="password" name="new_pass" placeholder="Enter new password" required>

        <label for="confirm_pass">Confirm Password:</label>
        <input type="password" name="confirm_pass" placeholder="Confirm new password" required>

        <input type="submit" value="Update Password" name="update_password">
        <a href="user_profile.php" class="option-btn">Go Back</a>
    </form>
</div>

</body>
</html>
