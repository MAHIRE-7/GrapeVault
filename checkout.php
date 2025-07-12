<?php
require 'vendor/autoload.php'; // Include AWS SDK
use Aws\S3\S3Client;
use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}
$user_query = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_query->execute([$user_id]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);

// Fetch address details
$address_query = $conn->prepare("SELECT flat, street, city, state, country, pin_code FROM addresses WHERE user_id = ?");
$address_query->execute([$user_id]);
$address = $address_query->fetch(PDO::FETCH_ASSOC);

// AWS Configuration
$bucketName = 'grapevault';
$s3 = new S3Client([
    'version' => 'latest',
   
    'region'  => $region, 
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET',
    ],
]);

$rekognition = new RekognitionClient([
    'version' => 'latest',
    'region'  => $region, 
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET',
    ],
]);

if (isset($_POST['order'])) 
{
    $name = htmlspecialchars(strip_tags($_POST['name']), ENT_QUOTES, 'UTF-8');
    $number = isset($_POST['number']) ? htmlspecialchars(strip_tags($_POST['number']), ENT_QUOTES, 'UTF-8') : '';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $method = isset($_POST['method']) ? htmlspecialchars(strip_tags($_POST['method']), ENT_QUOTES, 'UTF-8') : '';

    $flat = htmlspecialchars(strip_tags($_POST['flat']), ENT_QUOTES, 'UTF-8');
    $street = htmlspecialchars(strip_tags($_POST['street']), ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars(strip_tags($_POST['city']), ENT_QUOTES, 'UTF-8');
    $state = htmlspecialchars(strip_tags($_POST['state']), ENT_QUOTES, 'UTF-8');
    $country = htmlspecialchars(strip_tags($_POST['country']), ENT_QUOTES, 'UTF-8');
    $pin_code = htmlspecialchars(strip_tags($_POST['pin_code']), ENT_QUOTES, 'UTF-8');

    $address = "Flat No. $flat, $street, $city, $state, $country - $pin_code";
    $placed_on = date('d-M-Y');

    // Check if file is uploaded
    if (!isset($_POST['image_data']) || empty($_POST['image_data'])) {
        die("Error: No photo taken.");
    }

    $photo_data = $_POST['image_data'];
    $photo_data = str_replace('data:image/jpeg;base64,', '', $photo_data);
    $photo_data = base64_decode($photo_data);
    $photo_name = time() . '_photo.jpg';
    $photo_s3_key = "checkout_verification/$user_id/$photo_name";

    try {
        $s3->putObject([
            'Bucket' => 'grapevault',
            'Key'    => "checkout_verification/$user_id/$photo_name",
            'Body'   => $photo_data,
            'ContentType' => 'image/jpeg',
        ]);
    } catch (AwsException $e) {
        die("S3 Upload Error: " . $e->getMessage());
    }

    // Function to compare faces using AWS Rekognition
    function compareFaces($rekognition, $sourceImage, $targetImage)
    {
        global $bucketName;
        try {
            $result = $rekognition->compareFaces([
                'SourceImage' => ['S3Object' => ['Bucket' => $bucketName, 'Name' => $sourceImage]],
                'TargetImage' => ['S3Object' => ['Bucket' => $bucketName, 'Name' => $targetImage]],
                'SimilarityThreshold' => 80,
            ]);
            return isset($result['FaceMatches'][0]['Similarity']) ? $result['FaceMatches'][0]['Similarity'] : 0;
        } catch (AwsException $e) {
            return 0;
        }
    }

    // Fetch stored KYC images from the database
    $kyc_images = [];
    $query = $conn->prepare("SELECT images FROM kyc_details WHERE user_id = ?");
    $query->execute([$user_id]);

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $kyc_images[] = $row['images']; // Store image URLs
    }

    // Perform face comparison
    $match_found = false;
    foreach ($kyc_images as $kyc_image_url) {
        $s3_key = parse_url($kyc_image_url, PHP_URL_PATH); // Extract S3 Key from URL
        $s3_key = ltrim($s3_key, '/'); // Remove leading slash if exists

        $similarity = compareFaces($rekognition, $s3_key, $photo_s3_key);

        if ($similarity > 80) {
            $match_found = true;
            break;
        }
    }

   

    // Process Order
    $cart_total = 0;
    $cart_products = [];

    $cart_query = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $cart_query->execute([$user_id]);
    while ($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)) {
        $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ')';
        $cart_total += ($cart_item['price'] * $cart_item['quantity']);
    }

    

    $total_products = implode(', ', $cart_products);
    $insert_order = $conn->prepare("INSERT INTO orders (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);

    $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $delete_cart->execute([$user_id]);
    if($match_found)
    {
    header("Location: success.html");
    }

    if (!$match_found) {
        
        header("Location: failed.html");

    }

    if ($cart_total == 0) {
        die("Your cart is empty");
    }
    
    

   
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centered Order Form</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Form Container (Card) */
        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 550px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
        }

        /* Form Inputs */
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #555;
            text-align: left;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Submit Button */
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            margin-top: 15px;
            border-radius: 5px;
            transition: 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .form-container {
                padding: 15px;
            }

            input, select {
                font-size: 14px;
                padding: 8px;
            }

            input[type="submit"] {
                font-size: 16px;
            }
        }

        /* Webcam Container */
        #webcam-container {
            margin-top: 20px;
            position: relative;
            width: 100%;
            height: 300px;
        }

        video {
            width: 100%;
            height: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Place Your Order</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="name">Full Name:</label>
            <input type="text" name="name" value="<?= $user['name'] ?? '' ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" required>

            <label for="flat">Flat No.:</label>
            <input type="text" name="flat" value="<?= $address['flat'] ?? '' ?>" required>

            <label for="street">Street:</label>
            <input type="text" name="street" value="<?= $address['street'] ?? '' ?>" required>

            <label for="city">City:</label>
            <input type="text" name="city" value="<?= $address['city'] ?? '' ?>" required>

            <label for="state">State:</label>
            <input type="text" name="state" value="<?= $address['state'] ?? '' ?>" required>

            <label for="country">Country:</label>
            <input type="text" name="country" value="<?= $address['country'] ?? '' ?>" required>

            <label for="pin_code">PIN Code:</label>
            <input type="text" name="pin_code" value="<?= $address['pin_code'] ?? '' ?>" required>

            <label for="number">Phone Number:</label>
            <input type="text" name="number" required>

            <label for="method">Payment Method:</label>
            <select name="method" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>

            <!-- Webcam Section -->
            <div id="webcam-container">
                <video id="webcam" autoplay></video>
                <button type="button" id="capture-btn">Capture Image</button>
            </div>

            <input type="hidden" name="image_data" id="image_data">
            <input type="submit" name="order" value="Place Order">
        </form>
    </div>

    <script>
        const video = document.getElementById('webcam');
        const captureBtn = document.getElementById('capture-btn');
        const imageDataInput = document.getElementById('image_data');

        // Access webcam
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    video.srcObject = stream;
                })
                .catch(function (error) {
                    alert("Error accessing webcam: " + error.message);
                });
        }

        // Capture photo from webcam
        captureBtn.addEventListener('click', function () {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg');
            imageDataInput.value = dataUrl;
        });
    </script>

</body>
</html>
