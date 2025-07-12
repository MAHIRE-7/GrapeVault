<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $name = htmlspecialchars(strip_tags($_POST['name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = md5($_POST['pass']);
    $cpass = md5($_POST['cpass']);
    $dob = htmlspecialchars(strip_tags($_POST['dob']), ENT_QUOTES, 'UTF-8');
    $license_no = htmlspecialchars(strip_tags($_POST['license_no']), ENT_QUOTES, 'UTF-8');

    if ($pass !== $cpass) {
        $message[] = 'Passwords do not match!';
    } else {
        $check_email = $conn->prepare("SELECT * FROM admin_reg WHERE email = ?");
        $check_email->execute([$email]);

        if ($check_email->rowCount() > 0) {
            $message[] = 'Email already registered! Please log in.';
        } else {
            try {
                $admin_folder = "admins/{$name}/";

                // Upload profile image
                if (!empty($_FILES['image']['name'])) {
                    $image = $_FILES['image']['name'];
                    $image_tmp_name = $_FILES['image']['tmp_name'];
                    $profile_key = $admin_folder . "profile/" . $image;

                    $result = $s3->putObject([
                        'Bucket' => 'grapevault',
                        'Key'    => $profile_key,
                        'SourceFile' => $image_tmp_name,
                    ]);
                    $image_url = $result['ObjectURL'];
                } else {
                    $image_url = null;
                }

                // Upload license image
                if (!empty($_FILES['license_image']['name'])) {
                    $license_image = $_FILES['license_image']['name'];
                    $license_tmp_name = $_FILES['license_image']['tmp_name'];
                    $license_key = $admin_folder . "license/" . $license_image;

                    $result = $s3->putObject([
                        'Bucket' => "grapevault",
                        'Key' => $license_key,
                        'SourceFile' => $license_tmp_name,
                    ]);
                    $license_image_url = $result['ObjectURL'];
                } else {
                    $license_image_url = null;
                }

                $insert_admin = $conn->prepare("INSERT INTO admin_reg (name, email, password, dob, license_no, license_image, image, created_at, role) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'admin')");
                $inserted = $insert_admin->execute([$name, $email, $pass, $dob, $license_no, $license_image_url, $image_url]);

                if ($inserted) {
                    $message[] = 'Admin registration successful! Your application is under Review !!';
                    header('Location: thankyou.php');
                    exit();
                } else {
                    $message[] = 'Registration failed. Please try again.';
                }
            } catch (Exception $e) {
                $message[] = 'Error uploading images: ' . $e->getMessage();
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <span>' . $msg . '</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        ';
    }
}
?>

<div class="container-fluid d-flex" style="height: 100vh;">
    <!-- Left section  -->
    <div class="left-section" style="background-image: url('images/dwine2.jpg');"></div>

    <!-- Right section -->
    <div class="right-section d-flex justify-content-center align-items-center">
        <div class="login-card">
            <h3 class="text-center">Admin Registration</h3>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" name="pass" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="cpass" class="form-control" placeholder="Confirm your password" required>
                </div>
                <div class="form-group">
                    <input type="text" name="dob" class="form-control" placeholder="Enter your Date of Birth (YYYY-MM-DD)" value="<?php echo isset($dob) ? htmlspecialchars($dob) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" name="license_no" class="form-control" placeholder="Enter License No" value="<?php echo isset($license_no) ? htmlspecialchars($license_no) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Licence Image</label>
                    <input type="file" name="license_image" class="form-control" required accept="image/jpg, image/jpeg, image/png">
                </div>
                <div class="form-group">
                    <label>Profile Picture</label>
                    <input type="file" name="image" class="form-control" required accept="image/jpg, image/jpeg, image/png">
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-block">Register Now</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
