<?php
@include 'config.php';
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

// AWS S3 Configuration
$bucketName = 'grapevault';
$s3 = new S3Client([
   'version' => 'latest',
   'region'  => $region, 
        'credentials' => [
         'key'    => $aws_key,
         'secret' => $aws_secret,
   ]
]);

// Update Profile
if (isset($_POST['update_profile'])) {
   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $user_id]);

   // Handle Image Upload
   $image = $_FILES['profile_picture']['name'];
   $image_tmp_name = $_FILES['profile_picture']['tmp_name'];
   $old_image = $_POST['old_image'];

   if (!empty($image)) {
      $image_extension = pathinfo($image, PATHINFO_EXTENSION);
      $new_image_name = uniqid() . '.' . $image_extension;
      $s3_path = 'user_img/' . $new_image_name;
      
      try {
         // Upload to S3
         $s3->putObject([
            'Bucket' => $bucketName,
            'Key'    => $s3_path,
            'Body'   => fopen($image_tmp_name, 'rb')
        
         ]);

         // S3 URL
         $s3_url = "https://{$bucketName}.s3.amazonaws.com/{$s3_path}";

         // Update image URL in database
         $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
         $update_image->execute([$s3_url, $user_id]);
         
         $message[] = 'Image updated successfully!';
      } catch (S3Exception $e) {
         $message[] = 'Image upload failed: ' . $e->getMessage();
      }
   }

   // Password Update
   $old_pass = $_POST['old_pass'];
   $update_pass = md5($_POST['update_pass']);
   $new_pass = md5($_POST['new_pass']);
   $confirm_pass = md5($_POST['confirm_pass']);

   if (!empty($update_pass) && !empty($new_pass) && !empty($confirm_pass)) {
      if ($update_pass != $old_pass) {
         $message[] = 'Old password not matched!';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = 'Confirm password not matched!';
      } else {
         $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$confirm_pass, $user_id]);
         $message[] = 'Password updated successfully!';
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
   <title>Update User Profile</title>
   <link rel="stylesheet" href="css/components.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="update-profile">
   <h1 class="title">Update Profile</h1>
   <form action="" method="POST" enctype="multipart/form-data">
      <img src="<?= $fetch_profile['image']; ?>" alt="User Image">
      <div class="flex">
         <div class="inputBox">
            <span>Username:</span>
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="Update username" required class="box">
            <span>Email:</span>
            <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="Update email" required class="box">
            <span>Update Pic:</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <input type="hidden" name="old_image" value="<?= $fetch_profile['profile_picture']; ?>">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
            <span>Old Password:</span>
            <input type="password" name="update_pass" placeholder="Enter previous password" class="box">
            <span>New Password:</span>
            <input type="password" name="new_pass" placeholder="Enter new password" class="box">
            <span>Confirm Password:</span>
            <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box">
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" class="btn" value="Update Profile" name="update_profile">
         <a href="home.php" class="option-btn">Go Back</a>
      </div>
   </form>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
