<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .modal {
         display: none;
         position: fixed;
         z-index: 10;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0, 0, 0, 0.5);
      }

      .modal-content {
         background: #fff;
         width: 450px;
         height: 500px;
         margin: 5% auto;
         padding: 25px;
         border-radius: 10px;
         position: relative;
         overflow-y: auto;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
         font-size: 18px;
         line-height: 1.6;
      }

      #modalDetails p {
         margin-bottom: 12px;
      }

      .close-btn {
         position: absolute;
         top: 10px;
         right: 20px;
         font-size: 28px;
         cursor: pointer;
      }

      .box {
         cursor: pointer;
         border: 1px solid #ccc;
         padding: 15px;
         margin-bottom: 10px;
         border-radius: 5px;
         transition: 0.3s;
      }

      .box:hover {
         background-color: #f9f9f9;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
      $select_orders->execute([$user_id]);
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){ 
            $order_data = htmlspecialchars(json_encode($fetch_orders), ENT_QUOTES, 'UTF-8');
   ?>
      <div class="box" onclick='openOrderModal(<?= $order_data ?>)'>
         <p><strong>Order:</strong> <?= $fetch_orders['placed_on']; ?> — ₹<?= $fetch_orders['total_price']; ?>/-
         <br><small>Status: 
            <span style="color:<?= $fetch_orders['payment_status'] == 'pending' ? 'red' : 'green' ?>">
               <?= $fetch_orders['payment_status']; ?>
            </span>
         </small></p>
      </div>
   <?php
         }
      } else {
         echo '<p class="empty">No orders placed yet!</p>';
      }
   ?>

   </div>

</section>

<!-- Modal -->
<div id="orderModal" class="modal">
   <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div id="modalDetails"></div>
   </div>
</div>

<?php include 'footer.php'; ?>

<script>
function openOrderModal(order) {
   const modal = document.getElementById("orderModal");
   const details = document.getElementById("modalDetails");

   details.innerHTML = `
      <p><strong>Placed on:</strong> ${order.placed_on}</p>
      <p><strong>Name:</strong> ${order.name}</p>
      <p><strong>Number:</strong> ${order.number}</p>
      <p><strong>Email:</strong> ${order.email}</p>
      <p><strong>Address:</strong> ${order.address}</p>
      <p><strong>Payment Method:</strong> ${order.method}</p>
      <p><strong>Your Orders:</strong> ${order.total_products}</p>
      <p><strong>Total Price:</strong> ₹${order.total_price}/-</p>
      <p><strong>Payment Status:</strong> 
         <span style="color:${order.payment_status == 'pending' ? 'red' : 'green'}">${order.payment_status}</span>
      </p>
   `;

   modal.style.display = "block";
}

function closeModal() {
   document.getElementById("orderModal").style.display = "none";
}

window.onclick = function(event) {
   const modal = document.getElementById("orderModal");
   if (event.target == modal) {
      modal.style.display = "none";
   }
}
</script>

<script src="js/script.js"></script>

</body>
</html>
