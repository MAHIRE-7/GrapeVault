<?php

@include 'config.php';

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
} else {
   header('location:login.php');
   exit;
}

if (isset($_POST['update_order'])) {
   $order_id = $_POST['order_id'];
   $update_status = $_POST['update_status'] ?? 'pending';

   // Update order with the admin ID who approved/declined
   $update_order = $conn->prepare("UPDATE orders SET approval_status = ?, approved_by = ? WHERE id = ?");
   $update_order->execute([$update_status, $admin_id, $order_id]);

   $message[] = 'Order status updated!';
}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_orders.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <div class="box-container">

      <?php
         // Config is already included at the top of the file
         // Admin check is already done at the top of the file

         // Retrieve only orders placed by this admin's users
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
$select_orders->execute();

         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p> User ID : <span><?= $fetch_orders['user_id']; ?></span> </p>
         <p> Placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p> Name : <span><?= $fetch_orders['name']; ?></span> </p>
         <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
         <p> Number : <span><?= $fetch_orders['number']; ?></span> </p>
         <p> Address : <span><?= $fetch_orders['address']; ?></span> </p>
         <p> Total Products : <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> Total Price : <span>₹<?= $fetch_orders['total_price']; ?>/-</span> </p>
         <p> Payment Method : <span><?= $fetch_orders['method']; ?></span> </p>
         <form action="" method="POST">
    <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">

    <label>Approval Status:</label>
    <select name="update_status" class="drop-down">
        <option value="pending" <?= $fetch_orders['approval_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="approved" <?= $fetch_orders['approval_status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
        <option value="declined" <?= $fetch_orders['approval_status'] == 'declined' ? 'selected' : ''; ?>>Declined</option>
    </select>

    <div class="flex-btn">
        <input type="submit" name="update_order" class="option-btn" value="Update">
        <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?');">Delete</a>
    </div>

    <?php if ($fetch_orders['approved_by']) : ?>
        <p> Approved/Declined by Admin ID: <span><?= $fetch_orders['approved_by']; ?></span></p>
    <?php endif; ?>
</form>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No orders placed yet!</p>';
         }
      ?>

   </div>

</section>


<script src="js/script.js"></script>

</body>
</html>