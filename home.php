<?php
session_start();
@include 'config.php';

// Check if user_id exists in session before accessing it
if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   header('location:login.php');
   exit();
}

if (isset($_POST['add_to_wishlist'])) {
   
}

if (isset($_POST['add_to_cart'])) {
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home Page</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/home-product.css">
   <link rel="stylesheet" href="css/chatbot.css"> 
   <link rel="stylesheet" href="style-wine-home.css"> 
</head>
<body>

<?php include 'header.php'; ?>

<style>
 
 body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #fff8f0;
      color: #333;
      margin: 0;
    padding: 0;
    overflow: hidden; /* Prevent scrolling when popup is active */
    }
   

    #ageVerification {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .popup {
        width: 400px; 
        height: 250px; 
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    body.no-scroll {
        overflow: hidden;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); 
        z-index: 999;
    }

    .popup h2 {
        margin-bottom: 15px;
    }

    .popup button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    .popup button:hover {
        background-color: #218838;
    }

    /* Wine Category Section */
    #wine-category {
        padding: 50px 20px;
        background-color: #f4f4f4;
    }

    #wine-category .category-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }

    #wine-category .category-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease-in-out;
    }

    #wine-category .category-box:hover {
        transform: scale(1.05);
    }

    #wine-category .category-box img {
        max-width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 8px;
    }

    #wine-category .category-box h3 {
        font-size: 22px;
        color: #360000;
        margin-top: 15px;
    }

    #wine-category .category-box p {
        font-size: 16px;
        color: #555;
        margin-top: 10px;
    }

    #wine-category .category-box .btn {
        margin-top: 15px;
        background-color: #a31818;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    #wine-category .category-box .btn:hover {
        background-color: #720000;
    }

    /* Health Benefits of Wine Section (Diseases) */
    #disease-category {
        padding: 50px 20px;
        background-color: #f4f4f4;
    }

    #disease-category .category-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }

    #disease-category .category-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease-in-out;
    }

    #disease-category .category-box:hover {
        transform: scale(1.05);
    }

    #disease-category .category-box img {
        max-width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 8px;
    }

    #disease-category .category-box h3 {
        font-size: 24px; /* Make titles bolder and larger */
        font-weight: bold;
        color: #360000;
        margin-top: 15px;
    }

    #disease-category .category-box p {
        font-size: 16px;
        color: #555;
        margin-top: 10px;
    }

    #disease-category .category-box .btn {
        margin-top: 15px;
        background-color: #a31818;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    #disease-category .category-box .btn:hover {
        background-color: #720000;
    }
</style>

<script>
    const AGE_VERIFICATION_DURATION = 600000; // 10 minutes in milliseconds

    // Retrieve values from localStorage
    const isVerified = localStorage.getItem("ageVerified") === "true";
    const verifiedTime = parseInt(localStorage.getItem("ageVerifiedTime"));

    // If verified and time is within 10 minutes
    if (isVerified && !isNaN(verifiedTime) && (Date.now() - verifiedTime) < AGE_VERIFICATION_DURATION) {
        document.getElementById("ageVerification").style.display = "none";
        document.body.style.overflow = "auto";
    } else {
        // Either not verified or 10 minutes passed
        localStorage.removeItem("ageVerified");
        localStorage.removeItem("ageVerifiedTime");
        document.getElementById("ageVerification").style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    function allowAccess() {
        localStorage.setItem("ageVerified", "true");
        localStorage.setItem("ageVerifiedTime", Date.now().toString());
        document.getElementById("ageVerification").style.display = "none";
        document.body.style.overflow = "auto";
    }
    
</script>




<div id="ageVerification" class="overlay">
    <div class="popup">
    <h1>📢 Important:</h1> <h2>This website promotes the <b>ethical and informed sale of wine</b>.  
  <h2>Wine may have health benefits when consumed in moderation, such as supporting heart health and providing antioxidants.</h2>
  <h2><b>Please do not misuse alcohol</b>.Drink responsibly and only if you are of legal drinking age.</h2>
        <button onclick="allowAccess()">I am aware</button>
    </div>
</div>

<script>
    // Check if user has already confirmed during the session
    if (sessionStorage.getItem("ageVerified")) {
        document.getElementById("ageVerification").style.display = "none";
        document.body.style.overflow = "auto"; // Restore scrolling

        // Set timeout to clear sessionStorage after 10 seconds
        setTimeout(() => {
            sessionStorage.removeItem("ageVerified");
            alert("Session expired. Please verify again.");
            location.reload(); // Reload the page
        }, 10000000000000); 
    }
    function showPopup() {
    document.getElementById("ageVerification").style.display = "block";
    document.body.classList.add("no-scroll"); // Disable scrolling
}

    function allowAccess() {
        sessionStorage.setItem("ageVerified", "true"); // Store confirmation for the session
        document.getElementById("ageVerification").style.display = "none";
        document.body.style.overflow = "auto"; // Restore scrolling
        document.body.classList.remove("no-scroll"); // Re-enable scrolling
        
        // Set timeout to clear sessionStorage after 10 seconds
        setTimeout(() => {
            sessionStorage.removeItem("ageVerified");
            alert("Session expired. Please verify again.");
            location.reload(); // Reload the page
        }, 100000000000); // 10 seconds
    }
</script>

<div class="home-bg">

   <section class="home">
      <div class="content">
         <span style="color:aliceblue">Welcome to India's First Wine Selling Platform</span>
         <h3 style="color:#360000">GrapeVault</h3>
         <p>Preserving the Essence of Fine Wines, So You Can Relish Every Drop, Every Time.</p>
         <a href="about.php" style="background:#a31818" class="btn">about us</a>
      </div>
   </section>

</div>

<section id="wine-category">
    <h1 class="title">Shop by Category</h1>

    <div class="category-container">

        <div class="category-box">
            <img src="images/red_wine.jpg" alt="A bottle of Red Wine">
            <h3>Red Wine</h3>
            <p>Experience the rich flavors and deep aromas of our finest red wines.</p>
            <a href="category.php?category=red_wine" class="btn">Explore Red Wine</a>
        </div>

        <div class="category-box">
            <img src="images/white_wine.jpg" alt="A bottle of White Wine">
            <h3>White Wine</h3>
            <p>Crisp and refreshing white wines, perfect for any occasion.</p>
            <a href="category.php?category=white_wine" class="btn">Explore White Wine</a>
        </div>

        <div class="category-box">
            <img src="images/sparkling_wine.jpg" alt="A bottle of Sparkling Wine">
            <h3>Sparkling Wine</h3>
            <p>Celebrate in style with our finest selection of sparkling wines.</p>
            <a href="category.php?category=sparkling_wine" class="btn">Explore Sparkling Wine</a>
        </div>

        <div class="category-box">
            <img src="images/rose_wine.jpg" alt="A bottle of Rose Wine">
            <h3>Rosé Wine</h3>
            <p>Light, refreshing, and elegant rosé wines for every taste.</p>
            <a href="category.php?category=rose_wine" class="btn">Explore Rosé Wine</a>
        </div>

    </div>
</section>

<!-- Health Benefits of Wine Section (Diseases) -->
<section id="disease-category">
   <h1 class="title">Health Benefits of Wine</h1>

   <div class="category-container">

      <!-- Cardiovascular Health -->
      <div class="category-box">
         <img src="images/heart_diseases.jpeg" alt="Cardiovascular Health">
         <h3>Cardiovascular Health</h3>
         <p>Wines, especially red wines, have been linked to a reduced risk of heart disease and improved cardiovascular health.</p>
         <a href="shop.php" class="btn">Explore Wines for Heart Health</a>
      </div>

      <!-- Diabetes -->
      <div class="category-box">
         <img src="images/diabetes.jpg" alt="Diabetes">
         <h3>Diabetes</h3>
         <p>Moderate wine consumption may help improve blood sugar control and insulin sensitivity in diabetes patients.</p>
         <a href="shop.php" class="btn">Explore Wines for Diabetes</a>
      </div>

      <!-- Cancer Prevention -->
      <div class="category-box">
         <img src="images/cancer_prevention.jpg" alt="Cancer Prevention">
         <h3>Cancer Prevention</h3>
         <p>Certain compounds in wine may offer antioxidant properties that help fight cancer-causing cells.</p>
         <a href="shop.php" class="btn">Explore Wines for Cancer Prevention</a>
      </div>

      <!-- Liver Health -->
      <div class="category-box">
         <img src="images/liver_health.jpeg" alt="Liver Health">
         <h3>Liver Health</h3>
         <p>Studies show that moderate wine consumption can help protect the liver against certain diseases.</p>
         <a href="shop.php" class="btn">Explore Wines for Liver Health</a>
      </div>

      <!-- Bone Health -->
      <div class="category-box">
         <img src="images/bone_health.jpg" alt="Bone Health">
         <h3>Bone Health</h3>
         <p>Moderate consumption of wine can promote stronger bones and improve bone density.</p>
         <a href="shop.php" class="btn">Explore Wines for Bone Health</a>
      </div>

      <!-- Weight Loss -->
      <div class="category-box">
         <img src="images/weight_loss.jpg" alt="Weight Loss">
         <h3>Weight Loss</h3>
         <p>Wine can help with weight loss due to its ability to improve metabolism and reduce fat.</p>
         <a href="shop.php" class="btn">Explore Wines for Weight Loss</a>
      </div>

      <!-- Brain Health -->
      <div class="category-box">
         <img src="images/brain_health.jpg" alt="Brain Health">
         <h3>Brain Health</h3>
         <p>Wine, particularly red wine, has been linked to improved cognitive function and memory.</p>
         <a href="shop.php" class="btn">Explore Wines for Brain Health</a>
      </div>

      <!-- Anxiety and Stress Relief -->
      <div class="category-box">
         <img src="images/stress.jpg" alt="Anxiety and Stress Relief">
         <h3>Anxiety & Stress Relief</h3>
         <p>Moderate wine consumption may help reduce stress and anxiety levels, promoting relaxation.</p>
         <a href="shop.php" class="btn">Explore Wines for Stress Relief</a>
      </div>

   </div>
</section>

<!-- Latest Products Section -->
<section class="products">
   <h1 class="title">Latest Products</h1>
   <div class="box-container">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
         $select_products->execute();
         if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) { 
      ?>
      <form action="" class="box" method="POST">
         <div class="price">₹<span><?= $fetch_products['price']; ?></span>/-</div>
         <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <img src="<?= $fetch_products['image']; ?>" alt="Product Image">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
         <input type="number" min="1" value="1" name="p_qty" class="qty">
         <input type="submit" value="Add to Wishlist" class="option-btn" name="add_to_wishlist">
         <input type="submit" value="Add to Cart" class="btn" name="add_to_cart">
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>

<!-- Chatbot -->
<!-- Font Awesome for icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Chatbot Icon -->
<div class="chatbot-icon" onclick="toggleChatbot()" style="position: fixed; bottom: 20px; right: 20px; cursor: pointer; font-size: 2rem; z-index: 1000;">
   <i class="fas fa-comments"></i>
</div>

<!-- Chatbot Container -->
<div id="chatbot-container" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 350px; height: 500px; background: white; border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); overflow: hidden; font-family: 'Segoe UI', sans-serif; z-index: 1000;">
   <!-- Header -->
   <div style="background: linear-gradient(135deg, #4f46e5, #3b82f6); color: white; padding: 15px; font-size: 18px; font-weight: bold; display: flex; align-items: center;">
      <img src="https://i.imgur.com/8Km9tLL.png" alt="Bot Avatar" style="width: 35px; height: 35px; border-radius: 50%; margin-right: 10px;">
      Chat with SmartBot
   </div>

   <!-- Chat Window -->
   <div id="chat-window" style="height: 75%; overflow-y: auto; padding: 10px; display: flex; flex-direction: column; gap: 10px;"></div>

   <!-- Input Field -->
   <div style="padding: 10px; display: flex; align-items: center; border-top: 1px solid #eee;">
      <input type="text" id="user-input" placeholder="Type a message..." style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 20px; font-size: 14px;">
      <button onclick="sendMessage()" style="background: #3b82f6; border: none; color: white; margin-left: 10px; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
         <i class="fas fa-paper-plane"></i>
      </button>
   </div>
</div>

<!-- AWS SDK -->
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1198.0.min.js"></script>

<!-- Script -->
<script>
   function toggleChatbot() {
      const chatbot = document.getElementById('chatbot-container');
      chatbot.style.display = chatbot.style.display === 'none' ? 'block' : 'none';
   }

   AWS.config.region = 'us-east-1';
   AWS.config.credentials = new AWS.CognitoIdentityCredentials({
      IdentityPoolId: 'us-east-1:c9208a3e-6556-4720-b6f4-549e47684e08'
   });

   const lexruntime = new AWS.LexRuntimeV2();
   const botId = 'QEIHQHA1FP';
   const aliasId = 'TSTALIASID';
   const localeId = 'en_US';
   const sessionId = 'user-' + Date.now();

   function sendMessage() {
      const inputField = document.getElementById('user-input');
      const message = inputField.value.trim();
      if (!message) return;

      appendMessage('You', message);
      inputField.value = '';

      const params = {
         botAliasId: aliasId,
         botId: botId,
         localeId: localeId,
         sessionId: sessionId,
         text: message
      };

      lexruntime.recognizeText(params, function (err, data) {
         if (err) {
            console.error(err);
            appendMessage('Bot', '😕 Sorry, something went wrong.');
         } else {
            const response = data.messages?.[0]?.content || "🤖 I didn't get that.";
            appendMessage('Bot', '🤖 ' + response);
         }
      });
   }

   function appendMessage(sender, message) {
      const chatWindow = document.getElementById('chat-window');
      const msgDiv = document.createElement('div');
      msgDiv.style.maxWidth = '80%';
      msgDiv.style.padding = '10px';
      msgDiv.style.borderRadius = '15px';
      msgDiv.style.fontSize = '15px';
      msgDiv.style.lineHeight = '1.4';
      msgDiv.style.alignSelf = sender === 'You' ? 'flex-end' : 'flex-start';
      msgDiv.style.backgroundColor = sender === 'You' ? '#3b82f6' : '#f3f4f6';
      msgDiv.style.color = sender === 'You' ? 'white' : 'black';
      msgDiv.innerHTML = `<strong>${sender === 'You' ? '' : '🤖'} </strong>${message}`;
      chatWindow.appendChild(msgDiv);
      chatWindow.scrollTop = chatWindow.scrollHeight;
   }
</script>
</body>
</html>