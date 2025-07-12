<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit();
}

// Restrict access to admin with id 31
if($admin_id != 3){
   echo "<script>alert('Access denied! Only admin with ID 31 can add new users.');</script>";
   header('location:admin_page.php');
   exit();
}

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = htmlspecialchars(strip_tags($_POST['name']), ENT_QUOTES, 'UTF-8');
   $email = $_POST['email'];
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $pass = md5($_POST['pass']);
   $pass = htmlspecialchars(md5($_POST['pass']), ENT_QUOTES, 'UTF-8');
   $cpass = md5($_POST['cpass']);
   $cpass = htmlspecialchars(md5($_POST['cpass']), ENT_QUOTES, 'UTF-8');

   $image = $_FILES['image']['name'];
   $image = htmlspecialchars(strip_tags($image), ENT_QUOTES, 'UTF-8');

   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select = $conn->prepare("SELECT * FROM `admin` WHERE email = ?");
   $select->execute([$email]);

   if($select->rowCount() > 0){
      $message[] = 'Admin email already exists!';
   } else {
      if($pass != $cpass){
         $message[] = 'Confirm password does not match!';
      } else {
         $insert = $conn->prepare("INSERT INTO `admin`(name, email, password,profile_image) VALUES(?,?,?,?)");
         $insert->execute([$name, $email, $pass, $image]);

         if($insert){
            if($image_size > 2000000){
               $message[] = 'Image size is too large!';
            } else {
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'Admin added successfully!';
               header('location:admin_all_users.php');
            }
         }
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
   <title>Add Admin</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<section class="form-container">

   <form action="" enctype="multipart/form-data" method="POST">
      <h3>Add New Admin</h3>
      <input type="text" name="name" class="box" placeholder="Enter admin name" required>
      <input type="email" name="email" class="box" placeholder="Enter admin email" required>
      <input type="password" name="pass" class="box" placeholder="Enter admin password" required>
      <input type="password" name="cpass" class="box" placeholder="Confirm admin password" required>
      <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="Add Admin" class="btn" name="submit">
      <a href="admin_page.php" class="btn">Back to Dashboard</a>
   </form>

</section>

<script src="js/script.js"></script>

</body>
</html>
