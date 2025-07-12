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
         'secret' => 'AWS_SECRET',
    ],
]);

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
} else {
   header('location:login.php');
   exit;
}

if(isset($_POST['add_product'])){

   $name = htmlspecialchars(strip_tags($_POST['name']), ENT_QUOTES, 'UTF-8');
   $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
   if ($price === false) {
       die("Invalid price format.");
   }

   $category = htmlspecialchars(strip_tags($_POST['category']), ENT_QUOTES, 'UTF-8');
   $details = htmlspecialchars(strip_tags($_POST['details']), ENT_QUOTES, 'UTF-8');
   $image = htmlspecialchars(strip_tags($_FILES['image']['name']), ENT_QUOTES, 'UTF-8');

   $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
   $file_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
   if (!in_array($file_extension, $allowed_extensions)) {
       die("Invalid file type.");
   }

   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   if($image_size > 2000000){
       die("Image size is too large!");
   }

   $s3Key = 'Product_img/' . time() . '_' . $image;

   try {
      $result = $s3->putObject([
         'Bucket' => $bucketName,
         'Key'    => $s3Key,
         'SourceFile' => $image_tmp_name,
     ]);
     
     $image_url = $result['ObjectURL'];

       $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image, admin_id) VALUES(?, ?, ?, ?, ?, ?)");
       $insert_products->execute([$name, $category, $details, $price, $image_url, $admin_id]);

       $message[] = 'New product added!';
   } catch (AwsException $e) {
       echo "S3 Upload Error: " . $e->getMessage();
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">add new product</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
         <input type="text" name="name" class="box" required placeholder="enter product name">
         <select name="category" class="box" required>
            <option value="" selected disabled>select category</option>
               <option value="red_wine">Red Wine</option>
               <option value="white_wine">White Wine</option>
               <option value="sparkling_wine">Sparkling Wine</option>
               <option value="rose_wine">Rose Wine</option>
         </select>
         </div>
         <div class="inputBox">
         <input type="number" min="0" name="price" class="box" required placeholder="enter product price">
         <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
      </div>
      <textarea name="details" class="box" required placeholder="enter product details" cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="add product" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="title">products added</h1>

   <div class="box-container">

   <?php
      $show_products = $conn->prepare("SELECT * FROM `products`");
      $show_products->execute();
include 'config.php'; // ensure DB connection is included

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Optionally, delete image from the server if stored
    $select_image = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $select_image->execute([$delete_id]);
    $fetch_image = $select_image->fetch();
    if ($fetch_image && file_exists('uploaded_img/' . $fetch_image['image'])) {
        unlink('uploaded_img/' . $fetch_image['image']);
    }

    // Delete from database
    $delete_product = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_product->execute([$delete_id]);

    // Redirect to avoid repeated deletion on page refresh
    header('location:admin_products.php');
    exit;
}


      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <div class="price">₹<?= $fetch_products['price']; ?>/-</div>
      <img src="<?= $fetch_products['image']; ?>" alt="Product Image">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="cat"><?= $fetch_products['category']; ?></div>
      <div class="details"><?= $fetch_products['details']; ?></div>
      <div class="flex-btn">
         <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
         <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">now products added yet!</p>';
   }
   ?>

   </div>

</section>


<script src="js/script.js"></script>

</body>
</html>