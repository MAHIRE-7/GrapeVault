<?php
@include 'config.php';
session_start();

if(isset($_POST['submit'])){
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = md5($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $rowCount = $stmt->rowCount();
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($rowCount > 0){
      if($row['user_type'] == 'admin'){
         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');
      } elseif($row['user_type'] == 'user'){
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
      } else {
         $message[] = 'No user found!';
      }
   } else {
      $message[] = 'Incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thank You !</title>
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://accounts.google.com/gsi/client" async defer></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/login.css">
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="alert custom-alert alert-dismissible fade show" role="alert">
         <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
         '.$msg.'
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
         </button>
      </div>
      ';
   }
}
?>


<div class="container-fluid vh-100 d-flex p-0">
   <div class="row w-100 h-100 no-gutters">
      <div class="col-md-6 d-none d-md-flex align-items-center bg-cover" style="background-image: url('images/dwine2.jpg'); background-size: cover;"></div>
      <div class="col-md-6 d-flex justify-content-center align-items-center p-4">
         <div class="login-card w-100" style="max-width: 400px; ">
         <h1 style="align-items: center;">Thank You </h1>
         <h3 style="align-items: center;">for Registration  </h3>
         <h5 style="align-items: center;">We will back with you after Successful evaluation of you profile.</h5>
            
         </div>
      </div>
   </div>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
