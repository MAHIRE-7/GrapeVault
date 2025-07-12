<?php

@include 'config.php';

session_start();

if(isset($_SESSION['admin_id'])){
    $admin_id = $_SESSION['admin_id'];
} else {
    header('location:login.php');
    exit();
}

if (isset($_GET['delete'])) {
    // Allow only the admin with ID 31 to delete users
    if ($admin_id == 31) {
        $delete_id = $_GET['delete'];
        $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
        $delete_users->execute([$delete_id]);
        header('location:admin_all_users.php');
        exit();
    } else {
        // Show a message if someone other than admin ID 31 tries to delete
        echo "<script>alert('Only admin with ID 31 can delete users!');</script>";
    }
}

// Fetch all users
$select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = 'user'");
$select_users->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px; 
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-size: 16px; 
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .delete-btn {
            color: red;
            font-size: 16px;
        }
        h1.title {
            font-size: 24px; 
        }

        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.5); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            font-size: 16px;
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

        /* User details table inside modal */
        .user-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-details-table th, .user-details-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .user-details-table th {
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="user-accounts">
    <h1 class="title">User Accounts</h1>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                <td><?= $fetch_users['id']; ?></td>
                <td>
                    <a href="#" class="user-name" data-id="<?= $fetch_users['id']; ?>"><?= $fetch_users['name']; ?></a>
                </td>
                <td><?= $fetch_users['email']; ?></td>
                <td style="color:blue;"><?= $fetch_users['user_type']; ?></td>
                <td>
                    <?php if ($admin_id == 31): ?>
                        <a href="admin_all_users.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('Delete this user?');" class="delete-btn">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</section>

<!-- User Details Modal -->
<div id="userDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>User Details</h2>
        <div id="userDetails"></div>
    </div>
</div>

<script src="js/script.js"></script>
<script>

// Getting user details when name is clicked
document.querySelectorAll('.user-name').forEach(item => {
    item.addEventListener('click', event => {
        event.preventDefault();
        const userId = event.target.getAttribute('data-id');
        fetchUserDetails(userId);
    });
});

// Fetch user details using AJAX
function fetchUserDetails(userId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'user_details1.php?id=' + userId, true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById('userDetails').innerHTML = this.responseText;
            document.getElementById('userDetailsModal').style.display = 'block';
        }
    };
    xhr.send();
}

// Close modal
document.querySelector('.close').onclick = function() {
    document.getElementById('userDetailsModal').style.display = 'none';
}

// Close modal when clicking outside of the modal content
window.onclick = function(event) {
    const modal = document.getElementById('userDetailsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

</body>
</html>
