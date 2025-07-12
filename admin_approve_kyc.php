<?php
require 'vendor/autoload.php'; // AWS SDK
@include 'config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

session_start();

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
} else {
    header('location:login.php');
    exit;
}

// AWS S3 Configuration
$bucketName = 'grapevault';
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region, 
    'credentials' => [
        'key'    => $aws_key,
        'secret' => $aws_secret,
    ],
]);

// Approve KYC
if (isset($_POST['approve'])) {
    $kyc_id = $_POST['kyc_id'];

    $stmt = $conn->prepare("SELECT * FROM Kyc_requests WHERE id = ?");
    $stmt->execute([$kyc_id]);
    $kyc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kyc) {
        die("KYC record not found.");
    }

    $user_id = $kyc['user_id'];
    $name = $kyc['name'];
    $pan_card = $kyc['pan_card_url'];
    $profile_picture = $kyc['selfie_url'];

    $verified_folder = "KYC_documents/verified/{$user_id}_".str_replace(' ', '_', strtolower($name))."/";
    $verified_urls = [];

    foreach (['pan_card_url', 'selfie_url'] as $type) {
        $oldKey = ltrim(parse_url($kyc[$type], PHP_URL_PATH), '/');
        $newKey = str_replace("KYC_documents/", $verified_folder, $oldKey);

        try {
            $s3->copyObject([
                'Bucket'     => $bucketName,
                'CopySource' => "{$bucketName}/{$oldKey}",
                'Key'        => $newKey,
            ]);

            $s3->deleteObject([
                'Bucket' => $bucketName,
                'Key'    => $oldKey,
            ]);

            $verified_urls[$type] = "https://{$bucketName}.s3.amazonaws.com/{$newKey}";

        } catch (AwsException $e) {
            die("Error processing file: " . $e->getMessage());
        }
    }

    $insert = $conn->prepare("INSERT INTO kyc_details (user_id, name, images, uploaded_by, status) VALUES ( ?, ?, ?, ?, 'Approved')");
    $insert->execute([$user_id, $name, $verified_urls['selfie_url'], $admin_id]);

    $delete = $conn->prepare("DELETE FROM Kyc_requests WHERE id = ?");
    $delete->execute([$kyc_id]);

    echo "<script>alert('KYC approved successfully!'); window.location.href='admin_approve_kyc.php';</script>";
}

// Decline KYC
if (isset($_POST['decline'])) {
    $kyc_id = $_POST['kyc_id'];

    $stmt = $conn->prepare("SELECT * FROM Kyc_requests WHERE id = ?");
    $stmt->execute([$kyc_id]);
    $kyc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kyc) {
        die("KYC record not found.");
    }

    foreach (['pan_card_url', 'selfie_url'] as $type) {
        $key = ltrim(parse_url($kyc[$type], PHP_URL_PATH), '/');

        try {
            $s3->deleteObject([
                'Bucket' => $bucketName,
                'Key'    => $key,
            ]);
        } catch (AwsException $e) {
            die("Error deleting file: " . $e->getMessage());
        }
    }

    $delete = $conn->prepare("DELETE FROM Kyc_requests WHERE id = ?");
    $delete->execute([$kyc_id]);

    echo "<script>alert('KYC declined and deleted successfully!'); window.location.href='admin_approve_kyc.php';</script>";
}

// Fetch Pending KYC Requests
$stmt = $conn->prepare("SELECT * FROM Kyc_requests WHERE status = 'Pending'");
$stmt->execute();
$kyc_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin - KYC Review</title>
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         background: #f4f6f8;
         margin: 0;
         padding: 0;
      }

      .kyc-review {
         max-width: 1200px;
         margin: 50px auto;
         background: #fff;
         border-radius: 12px;
         padding: 30px;
         box-shadow: 0 8px 16px rgba(0,0,0,0.1);
         animation: fadeIn 0.5s ease-in-out;
      }

      .kyc-review .title {
         font-size: 28px;
         color: #333;
         text-align: center;
         margin-bottom: 30px;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         background: #fafafa;
         border-radius: 8px;
         overflow: hidden;
      }

      th, td {
         padding: 15px;
         text-align: center;
         border-bottom: 1px solid #ddd;
      }

      th {
         background-color: #6c63ff;
         color: white;
         font-weight: 600;
      }

      td img {
         border-radius: 8px;
         max-width: 100px;
         max-height: 100px;
         object-fit: cover;
         box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      }

      form {
         display: flex;
         flex-direction: column;
         gap: 8px;
         align-items: center;
      }

      .approve-btn, .decline-btn {
         padding: 8px 16px;
         border: none;
         border-radius: 8px;
         color: white;
         cursor: pointer;
         font-size: 14px;
         transition: background 0.3s ease;
      }

      .approve-btn {
         background-color: #28a745;
      }

      .approve-btn:hover {
         background-color: #218838;
      }

      .decline-btn {
         background-color: #dc3545;
      }

      .decline-btn:hover {
         background-color: #c82333;
      }

      @keyframes fadeIn {
         from {
            opacity: 0;
            transform: translateY(20px);
         }
         to {
            opacity: 1;
            transform: translateY(0);
         }
      }
      .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         padding-top: 60px;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0,0,0,0.9);
      }

      .modal-content {
         margin: auto;
         display: block;
         max-width: 90%;
         max-height: 80vh;
         border-radius: 10px;
      }

      .close {
         position: absolute;
         top: 20px;
         right: 30px;
         color: #fff;
         font-size: 35px;
         font-weight: bold;
         cursor: pointer;
      }

      @media (max-width: 768px) {
         .kyc-review {
            margin: 30px 15px;
            padding: 20px;
         }

         table, thead, tbody, th, td, tr {
            display: block;
         }

         th {
            text-align: left;
         }

         td {
            text-align: left;
            padding: 10px 0;
         }

         td img {
            width: 80px;
         }

         form {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
         }
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="kyc-review">
    <h1 class="title">Pending KYC Requests</h1>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>PAN Card</th>
                <th>Profile Picture</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kyc_requests as $kyc) : ?>
            <tr>
                <td><?= htmlspecialchars($kyc['user_id']) ?></td>
                <td><?= htmlspecialchars($kyc['name']) ?></td>
                <td><img src="<?= htmlspecialchars($kyc['pan_card_url']) ?>" alt="PAN Card" class="clickable-image"></td>
                <td><img src="<?= htmlspecialchars($kyc['selfie_url']) ?>" alt="Selfie" class="clickable-image"></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="kyc_id" value="<?= $kyc['id'] ?>">
                        <button type="submit" name="approve" class="approve-btn">Approve</button>
                        <button type="submit" name="decline" class="decline-btn">Decline</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<!-- Image Modal -->
<div id="imageModal" class="modal">
   <span class="close">&times;</span>
   <img class="modal-content" id="modalImage">
</div>

<script>
   const modal = document.getElementById("imageModal");
   const modalImg = document.getElementById("modalImage");
   const closeBtn = document.querySelector(".close");

   document.querySelectorAll(".clickable-image").forEach(img => {
      img.addEventListener("click", () => {
         modal.style.display = "block";
         modalImg.src = img.src;
      });
   });

   closeBtn.onclick = () => modal.style.display = "none";
   window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };
</script>

<script src="js/script.js"></script>
</body>
</html>
