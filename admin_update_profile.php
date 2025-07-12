<?php
@include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('Location: login.php');
    exit();
}

// Fetch current admin data
$fetch_admin = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$fetch_admin->execute([$admin_id]);
$admin_data = $fetch_admin->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['update_profile'])) {
    $name = htmlspecialchars(strip_tags($_POST['name']), ENT_QUOTES, 'UTF-8');

    try {
        // Handle profile image upload to S3
        $image_url = $admin_data['profile_image'];

        if (!empty($_FILES['image']['name'])) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $s3_key = "admins/{$admin_id}/" . basename($image_name);

            $result = $s3->putObject([
                'Bucket' => 'grapevault',
                'Key' => $s3_key,
                'SourceFile' => $image_tmp_name,
            ]);

            $image_url = $result['ObjectURL'];
        }

        // Update admin details in database
        $update = $conn->prepare("UPDATE admin SET name = ?, profile_image = ? WHERE id = ?");
        $update->execute([$name, $image_url, $admin_id]);

        $message[] = 'Profile updated successfully!';
    } catch (Exception $e) {
        $message[] = 'Update failed: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Admin Profile</title>
   <link rel="stylesheet" href="css/components.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="update-profile">
   <h1 class="title">Update Profile</h1>

 

   <form action="" method="POST" enctype="multipart/form-data">
      <img src="<?= htmlspecialchars($admin_data['profile_image']); ?>" alt="Profile" style="width: 150px; border-radius: 10px;">

      <div class="flex">
         <div class="inputBox">
            <span>Name:</span>
            <input type="text" name="name" value="<?= htmlspecialchars($admin_data['name']); ?>" class="box" required>

            <span>Update Profile Picture:</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
         </div>
      </div>

      <div class="flex-btn">
         <input type="submit" name="update_profile" value="Update Profile" class="btn">
         <a href="admin_page.php" class="option-btn">Go Back</a>
      </div>
   </form>
</section>

<script src="js/script.js"></script>

</body>
</html>
