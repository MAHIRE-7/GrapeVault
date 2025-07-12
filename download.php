<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

try {
   $conn = new PDO($db_name, $username, $password);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Connection failed: " . $e->getMessage();
}

// Fetch eBooks from the database
$select_ebooks = $conn->prepare("SELECT * FROM ebooks");
$select_ebooks->execute();try {
   $conn = new PDO($db_name, $username, $password);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Connection failed: " . $e->getMessage();
}

// Fetch eBooks from the database
$select_ebooks = $conn->prepare("SELECT * FROM ebooks");
$select_ebooks->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <style>
        .ebooks {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.ebook-card {
    perspective: 1000px;
    width: 200px;
    height: 300px;
}

.card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.6s;
}

.card-front, .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 10px;
}

.card-front {
    background: #f9f9f9;
}

.card-back {
    background: #f3f3f3;
    transform: rotateY(180deg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

.ebook-card.flipped .card-inner {
    transform: rotateY(180deg);
}

.ebook-cover {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}

.ebook-info {
    text-align: center;
}

.ebook-buttons {
    margin-top: 10px;
}

.ebook-buttons a, .ebook-buttons button {
    margin: 5px;
    padding: 5px 10px;
    text-decoration: none;
    color: #fff;
    background: #007bff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

</style>
   
<?php include 'header.php'; ?>

<section class="downloads">
    <h1 class="title">Downloads</h1>

    <!-- Android App Download Section -->
    <div class="android-app">
        <h2>Download Android App</h2>
        <p>Get our app to access your orders anytime, anywhere.</p>
        <a href="#" download class="download-button">Download App</a>
    </div>

    <!-- eBooks Section -->
    <div class="ebooks">
    <?php if ($select_ebooks->rowCount() > 0) {
        while ($ebook = $select_ebooks->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="ebook-card">
                <div class="card-inner">
                    <!-- Front side of the card (Cover Image) -->
                    <div class="card-front">
                        <img src="uploaded_img/<?= htmlspecialchars($ebook['cover_image']); ?>" alt="eBook Cover" class="ebook-cover" onclick="flipCard(this)">
                    </div>
                    
                    <!-- Back side of the card (Info) -->
                    <div class="card-back">
                        <div class="ebook-info">
                            <h3 class="ebook-title"><?= htmlspecialchars($ebook['title']); ?></h3>
                            <p class="ebook-author">Author: <?= htmlspecialchars($ebook['author']); ?></p>
                            <p class="ebook-description"><?= htmlspecialchars($ebook['description']); ?></p>
                            <div class="ebook-buttons">
                                <a href="<?= htmlspecialchars($ebook['file_path']); ?>" download>Download</a>
                                <button onclick="openDialog('<?= htmlspecialchars(addslashes($ebook['title'])); ?>', '<?= htmlspecialchars(addslashes($ebook['description'])); ?>')">Read More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } 
    } else {
        echo "<p class='empty'>No eBooks available for download at the moment.</p>";
    } ?>
</div>

</section>

<!-- Dialog Box for eBook Preview -->
<div id="ebookDialog" class="dialog-box" onclick="closeDialog()">
    <div class="dialog-content" onclick="event.stopPropagation();">
        <span class="close" onclick="closeDialog()">&times;</span>
        <h2 id="dialogTitle"></h2>
        <p id="dialogDescription"></p>
    </div>
</div>



<script>
    
    function openDialog(title, description) {
        document.getElementById('ebookDialog').style.display = 'flex';
        document.getElementById('dialogTitle').textContent = title;
        document.getElementById('dialogDescription').textContent = description;
    }

    function closeDialog() {
        document.getElementById('ebookDialog').style.display = 'none';
    }
    function flipCard(element) {
    const card = element.closest('.ebook-card');
    card.classList.toggle('flipped');
}

</script>









<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>