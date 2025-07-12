<?php
require 'vendor/autoload.php';
require 'config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT name, email, profile_picture, created_at FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowed_types)) {
        $error_message = 'Invalid file type. Only JPG, PNG, and GIF types are allowed.';
    } else {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => [
                'key'    => $aws_key,
                'secret' => $aws_secret,
            ],
        ]);

        $bucketName = 'grapevault';
        $uploadKey = 'user_img/' . basename($file['name']);

        try {
            $result = $s3Client->putObject([
                'Bucket' => $bucketName,
                'Key'    => $uploadKey,
                'SourceFile' => $file['tmp_name'],
            ]);

            $profilePictureUrl = $result['ObjectURL'];

            $query = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $query->execute([$profilePictureUrl, $user_id]);

            $user['profile_picture'] = $profilePictureUrl;
            $success_message = 'Profile picture updated successfully.';
        } catch (AwsException $e) {
            $error_message = 'Error uploading file: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .profile-container {
            width: 40%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-pic-container {
            position: relative;
            display: inline-block;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #333;
        }
        .camera-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
        }
        .camera-icon img {
            width: 20px;
            height: 20px;
        }
        .info {
            text-align: center;
            margin-top: 10px;
        }
        .info p {
            font-size: 18px;
            margin: 10px 0;
        }
        .info strong {
            color: #333;
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background: #007BFF;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        #profile_picture_input {
            display: none;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            height: 80%;
            border-radius: 10px;
            position: relative;
        }

        .close-btn {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h2>User Profile</h2>

        <div class="profile-pic-container">
            <img class="profile-img" src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
            <div class="camera-icon" onclick="document.getElementById('profile_picture_input').click();">
                <img src="tmp/cam.png" alt="Change Profile Picture">
            </div>
        </div>

        <div class="info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Account Created:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?></p>
        </div>

        <a href="javascript:void(0);" class="btn" onclick="openModal('update_password.php')">Update Password</a>
        <a href="javascript:void(0);" class="btn" onclick="openModal('update_address.php')">Update Address</a>
        <a href="javascript:void(0);" class="btn" onclick="openModal('update_kyc.php')">Update KYC Details</a>

        <form id="profile_picture_form" action="" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*" onchange="document.getElementById('profile_picture_form').submit();">
        </form>
    </div>

    <!-- Modal -->
    <div id="popupModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <iframe id="modalFrame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        function openModal(pageUrl) {
            document.getElementById("modalFrame").src = pageUrl;
            document.getElementById("popupModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("popupModal").style.display = "none";
            document.getElementById("modalFrame").src = "";
        }
    </script>

</body>
</html>
