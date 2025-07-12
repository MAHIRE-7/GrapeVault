<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $flat = htmlspecialchars($_POST['flat']);
    $street = htmlspecialchars($_POST['street']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $country = htmlspecialchars($_POST['country']);
    $pin_code = htmlspecialchars($_POST['pin_code']);

    $address = "$flat, $street, $city, $state, $country - $pin_code";

    $query = $conn->prepare("INSERT INTO addresses (user_id, flat, street, city, state, country, pin_code) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE flat = VALUES(flat), street = VALUES(street), city = VALUES(city), state = VALUES(state), country = VALUES(country), pin_code = VALUES(pin_code)");
    $query->execute([$user_id, $flat, $street, $city, $state, $country, $pin_code]);

    echo "Address updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Address</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6dd5ed, #f5f5dc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            text-align: left;
            color: #444;
        }

        input[type="text"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus {
            border-color: #2193b0;
            outline: none;
            box-shadow: 0 0 8px rgba(33, 147, 176, 0.4);
        }

        input[type="submit"] {
            margin-top: 20px;
            padding: 12px;
            font-size: 18px;
            background-color: #2193b0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #176e87;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
                margin: 10px;
            }

            input[type="text"], input[type="submit"] {
                font-size: 16px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Update Address</h2>
        <form action="" method="POST">
            <label for="flat">Flat No.:</label>
            <input type="text" name="flat" required>

            <label for="street">Street:</label>
            <input type="text" name="street" required>

            <label for="city">City:</label>
            <input type="text" name="city" required>

            <label for="state">State:</label>
            <input type="text" name="state" required>

            <label for="country">Country:</label>
            <input type="text" name="country" required>

            <label for="pin_code">PIN Code:</label>
            <input type="text" name="pin_code" required>

            <input type="submit" value="Update Address">
        </form>
    </div>
</body>
</html>
