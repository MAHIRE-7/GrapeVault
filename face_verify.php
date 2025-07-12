<?php
$maxFileSize = 5 * 1024 * 1024; 
session_start();
require 'vendor/autoload.php';
use Aws\Rekognition\RekognitionClient;
use Aws\S3\S3Client;

// Add your AWS credentials to config.php
@include 'config.php';

$user_id = $_SESSION['user_id'];



if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $targetImage = $_POST['image'];

    $imageData = $_POST['image'];
    if(!preg_match('/^data:image\/(jpeg|png);base64,/', $imageData)) {
        die(json_encode(['success' => false, 'message' => 'Invalid image format']));
    }
    
    // Check image size
    $size = (int) (strlen(rtrim($imageData, '=')) * 3 / 4);
    if($size > $maxFileSize) {
        die(json_encode(['success' => false, 'message' => 'Image too large']));
    }

    
    
    // Configure AWS clients
    $rekognition = new RekognitionClient([
        'version' => 'latest',
         
        'region'  => $region, 
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET'
        ]
    ]);

    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $region, 
        'credentials' => [
            'key'    => 'AWS_KEY',
            'secret' => 'AWS_SECRET'
        ]
    ]);

    try {
        // Source image from S3 (stored during registration)
        $sourceImage = 'user_faces/'.$user_id.'.jpg';
        
        // Compare faces
        $result = $rekognition->compareFaces([
            'SimilarityThreshold' => 80,
            'SourceImage' => [
                'S3Object' => [
                    'Bucket' =>'grapevault',
                    'Name' => $sourceImage,
                ],
            ],
            'TargetImage' => [
                'Bytes' => base64_decode(explode(",", $targetImage)[1]),
            ],
        ]);
        
        if(count($result['FaceMatches']) > 0){
            $_SESSION['face_verified'] = true;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Face verification failed']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>