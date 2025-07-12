<?php
require 'vendor/autoload.php';
@include 'config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = $conn->prepare("SELECT name FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$user_name = $user['name'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // AWS S3 Configuration
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $region, 
        'credentials' => [
            'key'    => 'AKIASXXDCPQ2TR57ZSLC',
            'secret' => 'AWS_SECRET'
        ],
    ]);

    $bucketName = 'grapevault';
    $folderPath = "User_Kyc_Requests/{$user_id}_{$user_name}/";

    if (isset($_FILES['pan_card']) && isset($_FILES['selfie'])) {
        $uploads = [
            'pan_card' => $_FILES['pan_card'],
            'selfie'   => $_FILES['selfie'],
        ];

        $uploadedUrls = [];

        foreach ($uploads as $key => $file) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $key . ".jpg";

            try {
                $result = $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key'    => $folderPath . $fileName,
                    'Body'   => fopen($fileTmpPath, 'rb'),
                ]);

                $uploadedUrls[$key] = $result['ObjectURL'];

            } catch (AwsException $e) {
                $message = "Error uploading {$key}: " . $e->getMessage();
            }
        }

        if (!empty($uploadedUrls['pan_card']) && !empty($uploadedUrls['selfie'])) {
            $stmt = $conn->prepare("INSERT INTO kyc_requests (user_id, name, pan_card_url, selfie_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $user_name, $uploadedUrls['pan_card'], $uploadedUrls['selfie']]);

            $message = "KYC request submitted successfully!";
        }
    } else {
        $message = "Please upload both images.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Request</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6dd5ed, #f5f5dc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        p {
            margin: 10px 0 5px;
            font-weight: 600;
            color: #444;
            align-self: flex-start;
        }

        input[type="file"] {
            padding: 8px;
            font-size: 14px;
            margin-bottom: 15px;
            width: 100%;
        }

        input[type="submit"] {
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
        }

        input[type="submit"]:hover {
            background-color: #176e87;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-top: 15px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            h2 {
                font-size: 22px;
            }

            input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>KYC Verification</h2>
    
    <?php if ($message): ?>
        <p class="message"><?= $message; ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <p>Upload a photo with your Aadhar Card:</p>
        <input type="file" name="pan_card" required>
        
        <p>Upload a selfie demonstrating the required pose:</p>
        <input type="file" name="selfie" required>

        <input type="submit" value="Submit KYC Request">
    </form>
</div>

</body>
</html>
