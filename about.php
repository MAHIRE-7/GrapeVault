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
   <title>about</title>

  
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .reviews .box-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
    gap: 1.5rem;
    justify-content: center;
    align-items: flex-start;
}

.reviews .box-container .box {
    padding: 1rem;
    text-align: center;
    border: var(--border);
    background-color: var(--white);
    box-shadow: var(--box-shadow);
    border-radius: .5rem;
}

.reviews .box-container .box p {
    padding: 1rem 0;
    font-size: 20px;
    color: var(--light-color);
    line-height: 2;
    width: 200px;
    height: 200px;
}

   </style>


</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">

   <div class="row">

      <div class="box">
         <img src="images/wine_bucket.png" alt="">
         <h3>why choose us?</h3>
         <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quisquam, a quod, quis alias eius dignissimos pariatur laborum dolorem ad ullam iure, consequatur autem animi illo odit! Atque quia minima voluptatibus.</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

      <div class="box">
         <img src="images/about-img-2.png" alt="">
         <h3>what we provide?</h3>
         <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quisquam, a quod, quis alias eius dignissimos pariatur laborum dolorem ad ullam iure, consequatur autem animi illo odit! Atque quia minima voluptatibus.</p>
         <a href="shop.php" class="btn">our shop</a>
      </div>

   </div>

</section>
<!-- Our Team -->
<section class="reviews">

   <h1 class="title">Our Team</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/our_team/manoday.jpg" alt="">
         <h3>Manoday Ahire</h3>
         <p>Developer/ Cloud Architect</p>
         
      </div>

      <div class="box">
         <img src="images/our_team/amey.jpg" alt="">
         <h3>Amey Patil</h3>
         <p>Developer/ Cloud Architect</p>
         
      </div>


      <div class="box">
         <img src="images/our_team/piyus.jpg" alt="">
         <h3>Piyush Ahire</h3>
         <p>QA & Testing/ Resource Collector</p>
         
      </div>


      <div class="box">
         <img src="images/our_team/hemant.jpg" alt="">
         <h3>Hemant Chaudhari</h3>
         <p>QA & Testing/ Resource Collector</p>
         
      </div>
   </div>
</section>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>