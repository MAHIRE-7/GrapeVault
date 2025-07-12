<?php
@include 'config.php';
session_start();
$message = $message ?? [];

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// admin with id 3 to approve and deny new admin
if ($admin_id != 3) {
    echo "<script>alert('Access denied! Only admin with ID 3 can approve or deny new admins.');</script>";
    header('location:admin_page.php');
    exit();
}

// Fetch pending admin registrations
$select_pending_admins = $conn->prepare("SELECT * FROM `admin_reg`");
$select_pending_admins->execute();
$pending_admins = $select_pending_admins->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Storing message
$message = [];

if (isset($_GET['approve'])) {
    $pending_id = $_GET['approve'];

    // Fetching admin registration data from admin_reg table
    $select_pending = $conn->prepare("SELECT * FROM `admin_reg` WHERE id = ?");
    $select_pending->execute([$pending_id]);
    $pending_data = $select_pending->fetch(PDO::FETCH_ASSOC);

    if ($pending_data) {
        $profileImageUrl = $pending_data['image'];
        $licenseImageUrl = $pending_data['license_image'];

        // Inserting data into `admin` table with S3 
        $insert_admin = $conn->prepare("INSERT INTO `admin` (name, email, password, user_type, license_no, created_at, profile_image, license_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_admin->execute([
            $pending_data['name'],
            $pending_data['email'],
            $pending_data['password'],
            'admin',
            $pending_data['license_no'],
            $pending_data['created_at'],
            $profileImageUrl,  
            $licenseImageUrl   
        ]);

        // Remove from admin_reg table
        $delete_pending = $conn->prepare("DELETE FROM `admin_reg` WHERE id = ?");
        $delete_pending->execute([$pending_id]);

        $message[] = 'Admin approved successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Approval</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         background: #f4f6f8;
         margin: 0;
         padding: 0;
      }

      h3 {
         text-align: center;
         font-size: 28px;
         color: #333;
         margin: 30px 0 10px;
      }

      .form-container {
         max-width: 1200px;
         margin: 0 auto;
         padding: 20px;
      }

      .modal {
         display: none;
         position: fixed;
         z-index: 1;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         background-color: rgba(0, 0, 0, 0.4);
      }

      .modal-content {
         background-color: #fefefe;
         margin: 10% auto;
         padding: 20px;
         border: 1px solid #888;
         width: 90%;
         max-width: 600px;
         border-radius: 10px;
         animation: fadeIn 0.3s ease-in-out;
      }

      .close {
         color: #aaa;
         float: right;
         font-size: 28px;
         font-weight: bold;
      }

      .close:hover,
      .close:focus {
         color: black;
         text-decoration: none;
         cursor: pointer;
      }

      .admin-cards {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 20px;
         padding: 30px;
      }

      .admin-card {
         background: #fff;
         border-radius: 12px;
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
         overflow: hidden;
         transition: transform 0.2s ease;
         display: flex;
         flex-direction: column;
         justify-content: space-between;
         padding: 20px;
      }

      .admin-card:hover {
         transform: translateY(-4px);
      }

      .profile-pic {
         width: 100%;
         height: 180px;
         object-fit: cover;
         border-radius: 10px;
         margin-bottom: 15px;
      }

      .card-body p {
         margin: 6px 0;
         font-size: 14px;
         color: #444;
      }

      .card-body strong {
         color: #222;
      }

      .license-section {
         margin-top: 10px;
      }

      .license-pic {
         width: 100%;
         max-height: 120px;
         object-fit: cover;
         border-radius: 8px;
         margin-top: 10px;
         cursor: pointer;
         border: 1px solid #ddd;
      }

      .card-footer {
         margin-top: 16px;
         display: flex;
         justify-content: center;
         gap: 10px;
      }

      .btn {
         background-color: #6c63ff;
         color: white;
         padding: 10px 18px;
         border: none;
         border-radius: 8px;
         font-size: 14px;
         text-decoration: none;
         cursor: pointer;
         transition: background-color 0.3s ease;
      }

      .btn:hover {
         background-color: #5548e3;
      }

      @keyframes fadeIn {
         from {
            opacity: 0;
            transform: scale(0.95);
         }
         to {
            opacity: 1;
            transform: scale(1);
         }
      }

      @media (max-width: 768px) {
         .card-footer {
            flex-direction: column;
            gap: 10px;
         }

         .admin-cards {
            padding: 10px;
         }
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="form-container">
   <h3>Pending Admin Approvals</h3>
   <?php if (!empty($message)): ?>
      <p><?php echo implode('<br>', $message); ?></p>
   <?php endif; ?>

   <div class="admin-cards">
      <?php if (count($pending_admins) > 0): ?>
         <?php foreach ($pending_admins as $admin): ?>
            <div class="admin-card">
               <img src="<?php echo htmlspecialchars($admin['image']); ?>" 
                    alt="Profile Picture" class="profile-pic" 
                    onerror="this.src='https://grapevault.s3.amazonaws.com/uploaded_img/default_profile.jpg';">
               <div class="card-body">
                  <p><strong>ID:</strong> <?php echo htmlspecialchars($admin['id']); ?></p>
                  <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                  <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                  <p><strong>Password:</strong> <?php echo htmlspecialchars($admin['password']); ?></p>
                  <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($admin['dob']); ?></p>
                  <p><strong>Role:</strong> <?php echo htmlspecialchars($admin['role']); ?></p>
                  <p><strong>License No:</strong> <?php echo htmlspecialchars($admin['license_no']); ?></p>
                  <p><strong>Created At:</strong> <?php echo htmlspecialchars($admin['created_at']); ?></p>
                  <div class="license-section">
                     <strong>License Image:</strong><br>
                     <img src="<?php echo htmlspecialchars($admin['license_image']); ?>" 
                          alt="License Image" class="license-pic" 
                          onclick="openModal('<?php echo htmlspecialchars($admin['license_image']); ?>')">
                  </div>
               </div>
               <div class="card-footer">
                  <a href="admin_approve.php?approve=<?php echo $admin['id']; ?>" class="btn">Approve</a>
                  <a href="admin_approve.php?deny=<?php echo $admin['id']; ?>" class="btn" style="background-color:#dc3545;">Deny</a>
               </div>
            </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p style="text-align:center;">No pending admin approvals.</p>
      <?php endif; ?>
   </div>
</section>

<div id="licenseModal" class="modal">
   <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalImage" src="" alt="License Image" style="width: 100%; height: auto; border-radius: 10px;">
   </div>
</div>

<script>
   function openModal(imageUrl) {
      var modal = document.getElementById("licenseModal");
      var modalImage = document.getElementById("modalImage");
      modal.style.display = "block";
      modalImage.src = imageUrl;
   }

   function closeModal() {
      document.getElementById("licenseModal").style.display = "none";
   }

   window.onclick = function(event) {
      var modal = document.getElementById("licenseModal");
      if (event.target == modal) {
         modal.style.display = "none";
      }
   }
</script>

<script src="js/script.js"></script>
</body>
</html>
