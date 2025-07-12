<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('309472156167-gml8svai1kihssofustulubqso4fjt1u.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/gs/home.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    // access token from Google
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    //  user profile info from Google
    $google_service = new Google_Service_Oauth2($client);
    $google_account_info = $google_service->userinfo->get();
    
    $google_id = $google_account_info->id;
    $name = $google_account_info->name;
    $email = $google_account_info->email;
    $profile_picture = $google_account_info->picture;

    // Check if user exists in your users table
    $check_user = $conn->prepare("SELECT * FROM `users` WHERE google_id = ?");
    $check_user->execute([$google_id]);

    if ($check_user->rowCount() > 0) {
        $user = $check_user->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
    } else {
        $insert_user = $conn->prepare("INSERT INTO `users` (google_id, name, email, profile_picture) VALUES (?, ?, ?, ?)");
        $insert_user->execute([$google_id, $name, $email, $profile_picture]);
        $_SESSION['user_id'] = $conn->lastInsertId();
    }
    
    header('Location: home.php');
    exit();
} else {
    header('Location: ' . $client->createAuthUrl());
    exit();
}
?>