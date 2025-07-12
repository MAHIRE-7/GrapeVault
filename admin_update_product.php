<?php

require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php');
}

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region, 
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET'
    ],
]);

$bucketName = 'grapevault';

if (isset($_POST['update_product'])) {
    $pid = $_POST['pid'];
    $name = htmlspecialchars(strip_tags($_POST['name']), ENT_QUOTES, 'UTF-8');
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    if ($price === false) {
        die("Invalid price format.");
    }
    $category = htmlspecialchars(strip_tags($_POST['category']), ENT_QUOTES, 'UTF-8');
    $details = htmlspecialchars(strip_tags($_POST['details']), ENT_QUOTES, 'UTF-8');
    $old_image = $_POST['old_image'];

    $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ? WHERE id = ?");
    $update_product->execute([$name, $category, $details, $price, $pid]);
    $message[] = 'product updated successfully!';

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $file_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            die("Invalid file type.");
        }

        $unique_image_name = uniqid() . '_' . time() . '_' . $image;
        $s3_key = "Product_img/" . $unique_image_name;

        try {
            // Upload new image to S3 without ACL
            $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => $s3_key,
                'SourceFile' => $image_tmp_name,
                'ContentType' => mime_content_type($image_tmp_name),
            ]);

            // Get the public URL of the uploaded image
            $image_url = "https://{$bucketName}.s3.amazonaws.com/{$s3_key}";

            // Update the image URL in the database
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $update_image->execute([$image_url, $pid]);

            // Delete old image from S3
            if (!empty($old_image)) {
                $old_key = str_replace("https://{$bucketName}.s3.amazonaws.com/", "", $old_image);
                $s3->deleteObject([
                    'Bucket' => $bucketName,
                    'Key'    => $old_key,
                ]);
            }

            $message[] = 'image updated successfully!';
        } catch (AwsException $e) {
            echo "Error uploading image: " . $e->getMessage();
        }
    }
}

?>
