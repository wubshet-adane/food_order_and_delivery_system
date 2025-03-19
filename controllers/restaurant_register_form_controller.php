<?php
require_once __DIR__ . '/../models/restaurant_register_form.php';
require_once __DIR__ . '/../config/database.php';

class RestaurantController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register() {
        session_start(); // Start session before anything else

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email']) || !isset($_SESSION['password']) || ($_SESSION['userType'] !=='restaurant')) {
                echo "Error: User not logged in.";
                return false;
            }

            $owner_id = $_SESSION['user_id']; // Fetch logged-in user ID

            // Handle file uploads
            $imagePath = $this->uploadFile('image');
            $bannerPath = $this->uploadFile('banner');
            $licensePath = $this->uploadFile('license'); // Fixed spelling

            $data = [
                'owner_id' => $owner_id,
                'name' => $_POST['name'],
                'image' => $imagePath,
                'banner' => $bannerPath,
                'license' => $licensePath, // Fixed key
                'location' => $_POST['location'],
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude'],
                'phone' => $_POST['phone'],
                'status' => $_POST['status'],
                'tiktok' => $_POST['tiktok'],
                'telegram' => $_POST['telegram'],
                'instagram' => $_POST['instagram'],
                'facebook' => $_POST['facebook'],
                'website' => $_POST['website'],
                'opening_and_closing_hour' => $_POST['opening_time'], // Corrected format
                'description' => $_POST['detail-description'],
            ];

            $restaurantModel = new Restaurant($this->conn); // Create the model object

            if ($restaurantModel->registerRestaurant($data)) {
                header("Location: ../views/restaurant/dashboard.php?success=restaurant successfuly registered,  keep wait until upproved by the system!!!");
            } else {
                header("Location: ../views/restaurant/restaurant_register_form.php?error=error happened,  try again later!!!");
            }
        }
    }

    public function uploadFile($fieldName) {
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
}

// Usage
$restaurantController = new RestaurantController($conn);
$restaurantController->register(); // Calling register method
?>
