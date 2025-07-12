<?php

require 'vendor/autoload.php'; // Include AWS SDK
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

@include 'config.php';

$bucketName = 'grapevault';
$s3 = new S3Client([
    'version' => 'latest',
   'region'  => $region, 
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET'

            
    ],
]);

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['submit_kyc'])){
   $user_id = htmlspecialchars(strip_tags($_POST['user_id']), ENT_QUOTES, 'UTF-8');
   
   // Fetch user name from database
   $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
   $stmt->execute([$user_id]);
   $user = $stmt->fetch(PDO::FETCH_ASSOC);
   
   if (!$user) {
       die("User not found.");
   }
   
   $name = $user['name'];
   
   // Create directory format user_id_name
   $kyc_folder = "KYC_documents/" . $user_id . "_" . str_replace(' ', '_', strtolower($name)) . "/";

   $uploaded_images = [];

   foreach ($_FILES['kyc_images']['name'] as $key => $image_name) {
        $file_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_extension, $allowed_extensions)) {
            die("Invalid file type.");
        }

        $image_tmp_name = $_FILES['kyc_images']['tmp_name'][$key];
        $s3Key = $kyc_folder . time() . '_' . $image_name;

        try {
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => $s3Key,
                'SourceFile' => $image_tmp_name,
            ]);
            $uploaded_images[] = $result['ObjectURL'];
        } catch (AwsException $e) {
            echo "S3 Upload Error: " . $e->getMessage();
        }
    }

   if (!empty($uploaded_images)) {
       $image_urls = implode(",", $uploaded_images);
       $insert_kyc = $conn->prepare("INSERT INTO `kyc_details`(user_id, name, images, uploaded_by) VALUES(?, ?, ?, ?)");
       $insert_kyc->execute([$user_id, $name, $image_urls, $admin_id]);
       $message[] = 'KYC documents uploaded successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>KYC Face Verification</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         background: #f4f6f8;
         margin: 0;
         padding: 0;
      }

      .kyc-verification-container {
         max-width: 900px;
         margin: 50px auto;
         background: #ffffff;
         border-radius: 12px;
         box-shadow: 0 8px 16px rgba(0,0,0,0.1);
         padding: 30px;
         animation: fadeIn 0.5s ease-in-out;
      }

      .kyc-verification {
         padding: 30px;
         background: #f9f9f9;
         border-radius: 8px;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      .kyc-verification .title {
         text-align: center;
         font-size: 28px;
         color: #333;
         margin-bottom: 20px;
      }

      form {
         display: flex;
         flex-direction: column;
         gap: 20px;
      }

      .flex {
         display: flex;
         flex-direction: column;
         gap: 15px;
      }

      .inputBox {
         display: flex;
         flex-direction: column;
      }

      .inputBox input[type="text"],
      .inputBox input[type="file"] {
         padding: 12px 15px;
         border: 1px solid #ccc;
         border-radius: 8px;
         font-size: 16px;
         outline: none;
         transition: 0.3s;
      }

      .inputBox input[type="text"]:focus,
      .inputBox input[type="file"]:focus {
         border-color: #6c63ff;
         box-shadow: 0 0 5px rgba(108, 99, 255, 0.2);
      }

      .btn {
         background: #6c63ff;
         color: white;
         padding: 12px 20px;
         border: none;
         border-radius: 8px;
         font-size: 16px;
         cursor: pointer;
         transition: background 0.3s ease;
         align-self: center;
      }

      .btn:hover {
         background: #5753d9;
      }

      @media (max-width: 600px) {
         .kyc-verification-container {
            padding: 20px;
            margin: 30px 15px;
         }

         .title {
            font-size: 24px;
         }

         .btn {
            width: 100%;
         }
      }

      @keyframes fadeIn {
         from {
            opacity: 0;
            transform: translateY(20px);
         }
         to {
            opacity: 1;
            transform: translateY(0);
         }
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="kyc-verification-container">
   <div class="kyc-verification">
      <h1 class="title">Upload KYC Documents</h1>

      <form action="" method="POST" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <input type="text" name="user_id" class="box" required placeholder="Enter User ID">
            </div>
            <div class="inputBox">
               <input type="file" name="kyc_images[]" multiple required class="box" accept="image/jpg, image/jpeg, image/png">
            </div>
         </div>
         <input type="submit" class="btn" value="Upload KYC" name="submit_kyc">
      </form>
   </div>
</section>

<script src="js/script.js"></script>
</body>
</html>
