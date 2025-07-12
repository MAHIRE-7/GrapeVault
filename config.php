<?php
// Prevent any output before session_start
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;
require 'vendor/autoload.php';

$region = "us-east-1";
$client = new SecretsManagerClient([
    'version' => 'latest',
    'region' => $region,
    'credentials' => [
        'key' => 'AWS_KEY',
        'secret' => 'AWS_SECRET',
    ]
]);

$secretName = 'Secreat_NAME';
try {
    $result = $client->getSecretValue([
        'SecretId' => $secretName,
    ]);

    $secretString = $result['SecretString'];
    $secret = json_decode($secretString, true);

    // Assign values
    $host = $secret['db_host'];
    $db_name = $secret['db_name'];
    $username = $secret['db_user'];
    $password = $secret['db_pass'];
    $aws_key = 'AWS_KEY';
    $aws_secret = 'AWS_SECRET';

} catch (AwsException $e) {
    // Store error in variable instead of echoing directly
    $aws_error = "Error: " . $e->getAwsErrorMessage();
    // Don't die here, let the script handle the error
}

// Step 4: MySQL connection
if (!isset($conn) || $conn === null) {
    try {
        $host = "HOST";
        $db_name = "shop_db";
        $username = "admin";
        $password = "Grapevault123";
        
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("All database connections failed. Please check your configuration.");
    }
}

use Aws\S3\S3Client;

$s3 = null;
if (isset($aws_key) && isset($aws_secret)) {
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $region,
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET',
        ],
    ]);
}

$bucketName = 'grapevault';
?>