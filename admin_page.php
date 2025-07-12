<?php

@include 'config.php';

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
} else {
   header('location:login.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   
   <style>
      body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         background: #f4f6f8;
         margin: 0;
         padding: 0;
      }

      .dashboard-container {
         max-width: 1200px;
         margin: 50px auto;
         background: #ffffff;
         border-radius: 12px;
         box-shadow: 0 8px 16px rgba(0,0,0,0.1);
         padding: 30px;
         animation: fadeIn 0.5s ease-in-out;
      }

      .dashboard {
         padding: 30px;
         background: #f9f9f9;
         border-radius: 8px;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      .dashboard .title {
         text-align: center;
         font-size: 28px;
         color: #333;
         margin-bottom: 20px;
      }

      nav {
         display: flex;
         justify-content: center;
         gap: 15px;
         margin-bottom: 20px;
      }

      .btn {
         background:  #651827;
         color: white;
         padding: 12px 20px;
         border: none;
         border-radius: 8px;
         font-size: 16px;
         cursor: pointer;
         transition: background 0.3s ease;
      }

      .btn:hover {
         background:  #651827;
      }

      .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
         gap: 20px;
         padding: 20px;
      }

      .box {
         background: #fff;
         border-radius: 8px;
         padding: 20px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         text-align: center;
      }

      .box h3 {
         font-size: 28px;
         color: #333;
         margin-bottom: 10px;
      }

      .box p {
         font-size: 16px;
         color: #666;
         margin-bottom: 15px;
      }

      .box a {
         font-size: 16px;
         background:  #651827;
         color: white;
         padding: 8px 16px;
         text-decoration: none;
         border-radius: 8px;
         transition: background 0.3s ease;
      }

      .box a:hover {
         background:  #651827;
      }

      @media (max-width: 768px) {
         .dashboard-container {
            padding: 20px;
            margin: 30px 15px;
         }

         .dashboard .title {
            font-size: 24px;
         }

         .box-container {
            grid-template-columns: 1fr;
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

<section class="dashboard-container">
   <div class="dashboard">
      <h1 class="title">Hello&nbsp;&nbsp;&nbsp;Mr. Admin,&nbsp;&nbsp;&nbsp;Welcome to Dashboard</h1>

      <nav>
         <?php if($admin_id == 3): // Only show Add Admin button for admin with ID 3 ?>
            <a href="add_admin.php" class="btn">Add Admin</a>
         <?php endif; ?>
         <?php if($admin_id == 3): // Only show Admin Applications button for admin with ID 3 ?>
            <a href="admin_approve.php" class="btn">Admin Applications</a>
         <?php endif; ?>
      </nav>

      <div class="box-container">
         <div class="box">
            <?php
               $total_pendings = 0;
               $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
               $select_pendings->execute(['pending']);
               while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                  $total_pendings += $fetch_pendings['total_price'];
               };
            ?>
            <h3>₹<?= $total_pendings; ?>/-</h3>
            <p>Total Pendings</p>
            <a href="admin_orders.php" class="btn">See Orders</a>
         </div>

         <div class="box">
            <?php
               $select_orders = $conn->prepare("SELECT COUNT(*) AS approved_count FROM `orders` WHERE approval_status = 'approved'");
               $select_orders->execute();
               $approved_orders = $select_orders->fetch(PDO::FETCH_ASSOC);
               $approved_count = $approved_orders['approved_count'];
            ?>
            <h3><?= $approved_count; ?>/-</h3>
            <p>Completed Orders</p>
            <a href="admin_orders.php" class="btn">See Orders</a>
         </div>

         <div class="box">
            <?php
               $select_orders = $conn->prepare("SELECT * FROM `orders`");
               $select_orders->execute();
               $number_of_orders = $select_orders->rowCount();
            ?>
            <h3><?= $number_of_orders; ?></h3>
            <p>Orders Placed</p>
            <a href="admin_orders.php" class="btn">See Orders</a>
         </div>

         <div class="box">
            <?php
               $select_products = $conn->prepare("SELECT * FROM `products`");
               $select_products->execute();
               $number_of_products = $select_products->rowCount();
            ?>
            <h3><?= $number_of_products; ?></h3>
            <p>Products Added</p>
            <a href="admin_products.php" class="btn">See Products</a>
         </div>

         <div class="box">
            <?php
               $select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
               $select_users->execute(['user']);
               $number_of_users = $select_users->rowCount();
            ?>
            <h3><?= $number_of_users; ?></h3>
            <p>Users</p>
            <a href="admin_all_users.php" class="btn">See Accounts</a>
         </div>

         <div class="box">
            <?php
               $select_admins = $conn->prepare("SELECT * FROM `admin` WHERE user_type = ?");
               $select_admins->execute(['admin']);
               $number_of_admins = $select_admins->rowCount();
            ?>
            <h3><?= $number_of_admins; ?></h3>
            <p>Admins</p>
            <a href="admin_all.php" class="btn">See Accounts</a>
         </div>

         <div class="box">
            <?php
               $select_accounts = $conn->prepare("SELECT * FROM `users`");
               $select_accounts->execute();
               $number_of_accounts = $select_accounts->rowCount();
            ?>
            <h3><?= $number_of_accounts; ?></h3>
            <p>Total Accounts</p>
            <a href="admin_all_users.php" class="btn">See Accounts</a>
         </div>

         <div class="box">
            <?php
               $select_accounts = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
               $select_accounts->execute(['admin']);
               $number_of_admins = $select_accounts->rowCount();
            ?>
            <h3><?= $number_of_admins; ?></h3>
            <p>Admins</p>
            <a href="admin_all.php" class="btn">See Accounts</a>
         </div>

         <div class="box">
            <?php
               $select_accounts = $conn->prepare("SELECT * FROM `users`");
               $select_accounts->execute();
               $number_of_accounts = $select_accounts->rowCount();
            ?>
            <h3><?= "null" ?></h3>
            <p>KYC Requests</p>
            <a href="admin_kyc_req.php" class="btn">Manual Approve</a>
         </div>

         <div class="box">
            <?php
               $select_accounts = $conn->prepare("SELECT * FROM `kyc_requests`");
               $select_accounts->execute();
               $number_of_req = $select_accounts->rowCount();
            ?>
            <h3><?= $number_of_req; ?></h3>
            <p>KYC Requests</p>
            <a href="admin_approve_kyc.php" class="btn">Approve KYC</a>
         </div>

         <div class="box">
            <?php
               $select_messages = $conn->prepare("SELECT * FROM `message`");
               $select_messages->execute();
               $number_of_messages = $select_messages->rowCount();

               $select_ebook = $conn->prepare("SELECT * FROM `ebooks`");
               $select_ebook->execute();
               $number_of_ebook = $select_ebook->rowCount();
            ?>
            <h3><?= $number_of_messages; ?></h3>
            <p>Total Messages</p>
            <a href="admin_contacts.php" class="btn">See Messages</a>
         </div>

         <div class="box">
            <h3><?= $number_of_ebook; ?></h3>
            <p>Total Ebooks</p>
            <a href="admin_ebook.php" class="btn">Add Ebook</a>
         </div>
      </div>
   </div>
</section>

<script src="js/script.js"></script>

</body>
</html>
