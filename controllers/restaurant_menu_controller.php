<?php
require_once '../models/manage_menu.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'|| $_SERVER['REQUEST_METHOD'] == 'GET') {
    
        // Handle file upload for images and documents
        function uploadFile($fieldName) {
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

        $imagePath = uploadFile('image');
        $action = $_GET['action'] ?? null;
        $resId = $_GET['res_id'] ?? null;
        $id = $_GET['id'];

    //functions to add menu items
    if (isset($_POST['add_menu'])) {
        $resId = $_GET['res_id'];
        $name = $_POST['name'];
        $catagory = $_POST['category'];
        $content = $_POST['content'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $imagePath; // You should handle file uploads properly

        if(Menu::addItem( $resId, $name, $catagory, $content, $description, $price, $image)) {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&success=Item successfuly added");
        } else {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&error=Failed to add item");
        }
        exit;
    }

    //functions to delete menu items
    if ($action == 'delete') {
        if (Menu::deleteItem($id)) {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&success=Item succesfuly deleted");
        } else {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&error=Failed to delete item");
        }
        exit;
    }

    //functions to delete ALL menu items
    if ($action == 'deleteAll') {
        if (Menu::deleteAllItem($resId)) {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&success=All menu items succesfuly deleted");
        } else {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&error=Failed to delete item");
        }
        exit;
    }

    //functions to update menu items
    if (isset($_POST['edit_menu']) && $action == 'editMenu') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $catagory = $_POST['category'];
        $content = $_POST['content'];
        $price = $_POST['price'];
        $image = $imagePath; // Later, we'll handle file uploads

        if (Menu::updateItem($id, $name, $description, $catagory, $content, $price, $image)) {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&success=Item succesfuly updated");
        } else {
            header("Location: ../views/restaurant/dashboard.php?page=manage_menu&error=Failed to update item");
        }
        exit;
    }
} else {
    header("Location: ../views/restaurant/dashboard.php?page=manage_menu&error=Invalid request method");
}
?>
