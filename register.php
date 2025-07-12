<?php
session_start();
require 'vendor/autoload.php'; // Load AWS SDK
include 'config.php';

use Aws\Textract\TextractClient;
use Aws\S3\S3Client;

$bucketName = 'grapevault';

// Initialize AWS S3 client
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region, 
    'credentials' => [
        'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET',
    ]
]);

// Initialize AWS Textract client
$textract = new TextractClient([
    'version' => 'latest',
    'region'  => $region, 
    'credentials' => [
        'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET',
    ]
]);

// Define default profile image URL (from S3) for restricted registrations
$defaultProfileImage = 'https://grapevault.s3.us-east-1.amazonaws.com/user_img/default_icon.jpg';

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = md5($_POST['pass']);
    $cpass = md5($_POST['cpass']);
    $dob = null;
    $aadhaar_number = null;
    $aadhaarImageUrl = null; // URL of the uploaded Aadhaar image used for extraction

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $s3FilePath = 'uploads/' . time() . '_' . $image;

        // Upload Aadhaar image to S3 for Textract analysis
        $result = $s3->putObject([
            'Bucket' => $bucketName,
            'Key' => $s3FilePath,
            'SourceFile' => $image_tmp_name,
        ]);

        $aadhaarImageUrl = $result['ObjectURL'];

        // Pass the Aadhaar image to Textract
        $textractResult = $textract->analyzeDocument([
            'Document' => ['S3Object' => ['Bucket' => $bucketName, 'Name' => $s3FilePath]],
            'FeatureTypes' => ['FORMS']
        ]);

        // Extract DOB and Aadhaar Number from the Textract result
        foreach ($textractResult['Blocks'] as $block) {
            if ($block['BlockType'] == 'LINE') {
                if (!$dob && preg_match('/DOB[:\\s]+(\\d{2}\\/\\d{2}\\/\\d{4})/', $block['Text'], $matches)) {
                    $dob = $matches[1];
                }
                if (preg_match('/\\b\\d{4}\\s\\d{4}\\s\\d{4}\\b/', $block['Text'], $aadhaar_match)) {
                    $aadhaar_number = str_replace(' ', '', $aadhaar_match[0]);
                }
            }
        }
    }

    // Check if required details were extracted successfully
    if (!$dob || !$aadhaar_number) {
        $_SESSION['message'] = 'Failed to extract required details from Aadhaar. Please check your image.';
        $_SESSION['message_type'] = 'danger';
        header('Location: register.php');
        exit();
    }

    // Convert DOB to YYYY-MM-DD format
    $dobFormatted = DateTime::createFromFormat('d/m/Y', $dob)->format('Y-m-d');

    // Check if Aadhaar is already registered
    $check_aadhaar = $conn->prepare("SELECT id FROM users WHERE aadhaar_number = ?");
    $check_aadhaar->execute([$aadhaar_number]);

    if ($check_aadhaar->rowCount() > 0) {
        $_SESSION['message'] = 'This Aadhaar number is already registered.';
        $_SESSION['message_type'] = 'warning';
        header('Location: register.php');
        exit();
    }

    // Check age restriction
    $birthdate = new DateTime($dobFormatted);
    $today = new DateTime();
    $age = $today->diff($birthdate)->y;

    if ($age < 15) {
        // Insert user into 'restricted' table with the default profile image stored in the image_url column
        $insert_restricted = $conn->prepare("INSERT INTO restricted (name, email, dob, aadhaar_number, image_url) VALUES (?, ?, ?, ?, ?)");
        $insert_restricted->execute([$name, $email, $dobFormatted, $aadhaar_number, $defaultProfileImage]);

        $_SESSION['message'] = 'Registration restricted due to age. You must be 22 or older. Your details have been saved for future eligibility.';
        $_SESSION['message_type'] = 'danger';
        header('Location: register.php');
        exit();
    } else {
        // Insert user into 'users' table with the Aadhaar image URL stored in the aadhar_url column
        $insert_user = $conn->prepare("INSERT INTO users (name, email, password, dob, aadhaar_number, image_url, profile_picture, created_at, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'user')");
        $inserted = $insert_user->execute([$name, $email, $pass, $dobFormatted, $aadhaar_number, $aadhaarImageUrl, $defaultProfileImage]);

        if ($inserted) {
            $_SESSION['message'] = 'Registration successful! Please log in.';
            $_SESSION['message_type'] = 'success';
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['message'] = 'Registration failed. Please try again.';
            $_SESSION['message_type'] = 'danger';
            header('Location: register.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
        }
        
        .left-section {
            flex: 1;
            background: url('images/dwine.jpg') no-repeat center center;
            background-size: cover;
        }
        
        .right-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: white;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            backdrop-filter: blur(5px);
        }
        
        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="left-section"></div>
    <div class="right-section">
        <div class="register-container">
            <h3>Register Now</h3>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="pass" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="cpass" class="form-control" placeholder="Confirm your password" required>
                </div>
                <div class="mb-3">
                    Upload Aadhaar: <input type="file" id="aadhaarImage" name="image" class="form-control" required accept="image/jpg, image/jpeg, image/png">
                </div>
                
                <button type="submit" name="submit" class="btn btn-primary btn-block">Register Now</button>
            </form>
        </div>
    </div>
</body>
</html>