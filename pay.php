<?php
require('vendor/autoload.php'); // Include Razorpay SDK
use Razorpay\Api\Api;

$keyId = "your_key_id";
$keySecret = "your_key_secret";
$api = new Api($keyId, $keySecret);

$orderData = [
    'receipt'         => 'order_12345',
    'amount'          => 50000, // Amount in paise (₹500)
    'currency'        => 'INR',
    'payment_capture' => 1 // Auto capture payment
];

$order = $api->order->create($orderData);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Now</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <button id="payButton">Pay Now</button>
    <script>
        var options = {
            "key": "<?php echo $keyId; ?>",
            "amount": "<?php echo $orderData['amount']; ?>",
            "currency": "INR",
            "name": "Your Company",
            "description": "Test Payment",
            "order_id": "<?php echo $order['id']; ?>",
            "handler": function (response){
                alert("Payment Successful: " + response.razorpay_payment_id);
                window.location.href = "success.php";
            },
            "prefill": {
                "name": "John Doe",
                "email": "john@example.com",
                "contact": "9999999999"
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        var rzp1 = new Razorpay(options);
        document.getElementById('payButton').onclick = function(e){
            rzp1.open();
            e.preventDefault();
        };
    </script>
</body>
</html>
