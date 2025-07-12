<?php
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = null;
}

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

<header class="header">

   <div class="flex">

      <!-- Back Button -->
      <button onclick="goBack()" class="back-btn">
         <i class="fas fa-arrow-left"></i> Back
      </button>

      <a href="#" class="logo">GrapeVault<span>.</span></a>

      <nav class="navbar">
         <a href="home.php">home</a>
         <a href="shop.php">shop</a>
         <a href="orders.php">orders</a>
         <a href="about.php">about</a>
         <a href="contact.php">contact</a>
         <a href="wine_info.php">Medical info</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <a href="search_page.php" class="fas fa-search"></a>
         <?php
            if($user_id){
               $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $count_cart_items->execute([$user_id]);

               $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
               $count_wishlist_items->execute([$user_id]);
            }
         ?>
         <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $user_id ? $count_wishlist_items->rowCount() : 0; ?>)</span></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $user_id ? $count_cart_items->rowCount() : 0; ?>)</span></a>
      </div>

      <div class="profile">
         <?php
            if($user_id){
               $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_profile->execute([$user_id]);
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
            <img src="<?= $fetch_profile['profile_picture']; ?>" alt="User Image">
            <p><?= $fetch_profile['name']; ?></p>
            <a href="javascript:void(0);" onclick="openModal('user_profile.php')" class="btn">Profile</a>
            <a href="logout.php" class="delete-btn">logout</a>
         <?php } else { ?>
            <p>Please <a href="login.php">login</a></p>
         <?php } ?>
      </div>

   </div>

</header>

<!-- Modal Popup for User Profile -->
<div id="popupModal" class="modal">
   <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <iframe id="modalFrame" src="" frameborder="0"></iframe>
   </div>
</div>

<!-- Styles -->
<style>
   .modal {
      display: none;
      position: fixed;
      z-index: 999;
      padding-top: 60px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
   }

   .modal-content {
      background-color: #fff;
      margin: auto;
      padding: 0;
      border: 1px solid #888;
      width: 80%;
      height: 80%;
      border-radius: 10px;
      position: relative;
   }

   .close-btn {
      color: #aaa;
      position: absolute;
      top: 10px;
      right: 25px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
   }

   iframe {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 10px;
   }

   .back-btn {
      background: none;
      border: none;
      color: #333;
      font-size: 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      margin-right: 15px;
   }

   .back-btn i {
      margin-right: 5px;
   }

   @media (max-width: 768px) {
      .modal-content {
         width: 95%;
         height: 90%;
      }
   }
</style>

<!-- Scripts -->
<script>
   // Toggle profile section
   document.getElementById('user-btn').addEventListener('click', function() {
      const profile = document.querySelector('.profile');
      profile.style.display = profile.style.display === 'none' ? 'block' : 'none';
   });

   // Modal Functions
   function openModal(pageUrl) {
      document.getElementById("modalFrame").src = pageUrl;
      document.getElementById("popupModal").style.display = "block";
   }

   function closeModal() {
      document.getElementById("popupModal").style.display = "none";
      document.getElementById("modalFrame").src = "";
   }

   // Back Button
   function goBack() {
      window.history.back();
   }
</script>
