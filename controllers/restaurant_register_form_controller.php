<?php

require_once __DIR__ . '/../models/restaurant_register_form.php';
require_once __DIR__ . '/../config/database.php';
session_start(); // Start session before anything else

// Validate session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password']) || ($_SESSION['userType'] !== 'restaurant')) {
    header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the previous page
    exit;
}

$action = $_GET['action'];
$restaurantId = $_GET['restaurant_id'];
// Initialize RestaurantController
$restaurantController = new RestaurantController($conn);

class RestaurantController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Register restaurant
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $owner_id = $_SESSION['user_id']; // Corrected to use the correct session variable

            // Handle file uploads
            $imagePath = $this->uploadFile('image');
            $bannerPath = $this->uploadFile('banner');
            $licensePath = $this->uploadFile('license'); // Fixed spelling

            $data = [
                'owner_id' => $owner_id,
                'name' => htmlspecialchars($_POST['name']),
                'image' => $imagePath,
                'banner' => $bannerPath,
                'license' => $licensePath,
                'location' => htmlspecialchars($_POST['location']),
                'latitude' => htmlspecialchars($_POST['latitude']),
                'longitude' => htmlspecialchars($_POST['longitude']),
                'phone' => htmlspecialchars($_POST['phone']),
                'status' => htmlspecialchars($_POST['status']),
                'tiktok' => htmlspecialchars($_POST['tiktok']),
                'telegram' => htmlspecialchars($_POST['telegram']),
                'instagram' => htmlspecialchars($_POST['instagram']),
                'facebook' => htmlspecialchars($_POST['facebook']),
                'website' => htmlspecialchars($_POST['website']),
                'opening_and_closing_hour' => htmlspecialchars($_POST['opening_time']), // Corrected format
                'description' => htmlspecialchars($_POST['detail-description']),
            ];

            $restaurantModel = new Restaurant($this->conn); // Create the model object
            if ($restaurantModel->registerRestaurant($data)) {
                header("Location: ../views/restaurant/dashboard.php?success=Restaurant successfully registered. Please wait until approved by the system.");
            } else {
                header("Location: ../views/restaurant/restaurant_register_form.php?error=An error occurred. Please try again later.");
            }
            exit;
        }
    }

    // Handle file upload for images and documents
    private function uploadFile($fieldName) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../views/restaurant/restaurantAsset/';
            $fileName = basename($_FILES[$fieldName]['name']); // Unique file name
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $filePath)) {
                return $fileName;
            }
        }
        return null;
    }


    // Update restaurant details
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['restaurant_id'] != '') {
            $restaurantId = $_GET['restaurant_id']; // Get restaurant ID
            $ownerId = $_SESSION['user_id']; // Corrected session variable for owner ID
            // Handle file uploads (optional, only if a new file is uploaded)
            $imagePath = $this->uploadFile('image');
            $bannerPath = $this->uploadFile('banner');
            $licensePath = $this->uploadFile('license');

            // Prepare the data to be updated
            $data = [
                'owner_id' => $ownerId,
                'restaurant_id' => $restaurantId,
                'name' => htmlspecialchars($_POST['name']),
                'image' => $imagePath,
                'banner' => $bannerPath,
                'license' => $licensePath,
                'location' => htmlspecialchars($_POST['location']),
                'latitude' => htmlspecialchars($_POST['latitude']),
                'longitude' => htmlspecialchars($_POST['longitude']),
                'website' => htmlspecialchars($_POST['website']),
                'tiktok' => htmlspecialchars($_POST['tiktokAccount']),
                'telegram' => htmlspecialchars($_POST['telegramAccount']),
                'instagram' => htmlspecialchars($_POST['instagramAccount']),
                'facebook' => htmlspecialchars($_POST['facebook']),
                'phone' => htmlspecialchars($_POST['phone']),
                'status' => htmlspecialchars($_POST['status']),
                'opening_and_closing_hour' => htmlspecialchars($_POST['opening_and_closing_hour']),
                'description' => htmlspecialchars($_POST['description']),
            ];

            $restaurantModel = new Restaurant($this->conn); // Create the model object
            // Update the restaurant details in the database
            if ($restaurantModel->updateRestaurant($restaurantId, $data)) {
                header("Location: ../views/restaurant/dashboard.php?success=Restaurant successfully updated.");
            } else {
                header("Location: ../views/restaurant/restaurant_update_form.php?error=An error occurred. Please try again later.");
            }
            exit;
        }
    }

    // Delete a restaurant from the database
    public function delete() {
        if (isset($_GET['restaurant_id'])) {
            $restaurantId = $_GET['restaurant_id']; // Get restaurant ID to delete
            $ownerId = $_SESSION['user_id']; // Corrected session variable for owner ID

            $restaurantModel = new Restaurant($this->conn); // Create the model object

            // Delete the restaurant
            if ($restaurantModel->deleteRestaurant($restaurantId, $ownerId)) {
                header("Location: ../views/restaurant/dashboard.php?success=Restaurant successfully deleted.");
            } else {
                header("Location: ../views/restaurant/restaurant_list.php?error=An error occurred while deleting. Please try again later.");
            }
            exit;
        }
    }
}


switch ($action) {
    case 'register':
        $restaurantController->register();
        break;
    case 'update_restaurant':
        $restaurantController->update();
        break;
    case 'delete_restaurant':
        $restaurantController->delete();
        break;
    default:
        header("Location: ../views/restaurant/restaurant_register_form.php?error=");
        exit;
}
?>
