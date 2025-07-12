<?php
session_start();
@include 'config.php';

if(isset($_POST['submit'])){
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = md5($_POST['pass']);
   $pass = htmlspecialchars(strip_tags($pass), ENT_QUOTES, 'UTF-8');

   // Select only from the `admin` table
   $sql = "SELECT * FROM `admin` WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $rowCount = $stmt->rowCount();

   if($rowCount > 0){
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // Set session and redirect for authenticated admin
      $_SESSION['admin_id'] = $row['id'];
      header('location:admin_page.php');
      exit(); // Add exit after header redirect

   } else {
      // Error message if credentials are incorrect or user is not an admin
      $message[] = 'Incorrect email or password, or access restricted to admins only!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://accounts.google.com/gsi/client" async defer></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="css/login.css">
</head>
<style>
   /* Styling for the custom alert */
   
.custom-alert {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1050;
    background-color: #f8d7da; /* Light red background */
    color: #721c24; /* Dark red text */
    border: 1px solid #f5c6cb; /* Light red border */
    border-radius: 0;
    font-weight: bold;
}

.custom-alert .close {
    color: #721c24;
}

.custom-alert .alert-icon {
    margin-right: 8px;
}

</style>
<body style="background: linear-gradient(to right, #1e3c72,rgb(69, 23, 30));">

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
      <div class="col-md-6 d-none d-md-flex align-items-center bg-cover" style="background-image: url('images/dwine3.jpg'); background-size: cover;"></div>
      <div class="col-md-6 d-flex justify-content-center align-items-center p-4">
         <div class="login-card w-100" style="max-width: 400px;background: linear-gradient(to right,rgb(46, 49, 54),rgb(108, 78, 82));">
            <h3 class="text-center mb-4">Login Now</h3>
            <form action="" method="POST">
               <div class="form-group">
                  <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
               </div>

               <div class="form-group position-relative">
                  <input type="password" name="pass" id="password" class="form-control" placeholder="Enter your password" required>
                  <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
               </div>

               <button type="submit" name="submit" class="btn btn-primary btn-block">Login Now</button>
               <p class="text-center mt-3">Don't have an account? <a href="register.php">Register Now</a></p>
            </form>
            
            <div class="text-center mt-4">
               <div id="g_id_onload"
                    data-client_id="309472156167-gml8svai1kihssofustulubqso4fjt1u.apps.googleusercontent.com"
                    data-context="signin"
                    data-ux_mode="popup"
                    data-callback="handleCredentialResponse"
                    data-auto_prompt="false">
               </div>
               <div class="g_id_signin" 
                    data-type="standard"
                    data-shape="rectangular"
                    data-theme="outline"
                    data-text="sign_in_with"
                    data-size="large">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   
   document.addEventListener("DOMContentLoaded", function () {
      const togglePassword = document.querySelector(".toggle-password");
      const password = document.querySelector("#password");

      togglePassword.addEventListener("click", function () {
         const type = password.getAttribute("type") === "password" ? "text" : "password";
         password.setAttribute("type", type);

         // Toggle eye / eye-slash icon
         this.classList.toggle("fa-eye");
         this.classList.toggle("fa-eye-slash");
      });
   });

   function handleCredentialResponse(response) {
      const responsePayload = decodeJwtResponse(response.credential);
      window.location.href = 'process_google_login.php?id_token=' + response.credential;
   }

   function decodeJwtResponse(token) {
      var base64Url = token.split('.')[1];
      var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
         return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
      return JSON.parse(jsonPayload);
   }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>