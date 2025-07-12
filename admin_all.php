<?php

@include 'config.php';

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
} else {
   header('location:login.php');
   exit();
}

if(isset($_GET['delete'])){

   // Allow only the admin with ID 31 to delete users
   if($admin_id == 31){
      $delete_id = $_GET['delete'];
      $delete_admins = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
      $delete_admins->execute([$delete_id]);
      header('location:admin_all_users.php');
   } else {
      // Show a message if someone other than admin ID 31 tries to delete
      echo "<script>alert('Only admin with ID 31 can delete admins!');</script>";
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Accounts</title>

   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">Admin Accounts</h1>

   <div class="box-container">

      <?php
         // Select only admins from the admins table
         $select_admins = $conn->prepare("SELECT * FROM `admin`");
         $select_admins->execute();
         while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box" style="<?php if($fetch_admins['id'] == $admin_id){ echo 'display:none'; }; ?>">
      <img src="<?php echo isset($fetch_admins['image']) ? htmlspecialchars($fetch_admins['image']) : 'https://grapevault.s3.amazonaws.com/uploaded_img/default_profile.jpg'; ?>" 
                             alt="Profile Picture" class="profile-pic">
         <p> Admin ID : <span><?= $fetch_admins['id']; ?></span></p>
         <p> Username : <span><?= $fetch_admins['name']; ?></span></p>
         <p> Email : <span><?= $fetch_admins['email']; ?></span></p>
         <p> User Type : <span style="color:orange;">admin</span></p>
         <?php if($admin_id == 31): // Only show delete button to admin with id 31 ?>
         <a href="admin_all_users.php?delete=<?= $fetch_admins['id']; ?>" onclick="return confirm('Delete this admin?');" class="delete-btn">delete</a>
         <?php endif; ?>
      </div>
      <?php
      }
      ?>
   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
