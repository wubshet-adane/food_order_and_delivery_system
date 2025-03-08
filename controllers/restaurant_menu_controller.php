<?php
require_once '../models/manage_menu.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //functions to add menu items
    if (isset($_POST['add_menu'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image']; // You should handle file uploads properly

        if(Menu::addItem($name, $description, $price, $image)) {
            header("Location: ../views/restaurant/dashboard.php?success=Item successfuly added");
        } else {
            header("Location: ../views/restaurant/dashboard.php?error=Failed to add item");
        }
    }

    //functions to delete menu items
    if (isset($_POST['delete_menu'])) {
        $id = $_POST['id'];
        if (Menu::deleteItem($id)) {
            header("Location: ../views/restaurant/dashboard.php?success=Item succesfuly deleted");
        } else {
            header("Location: ../views/restaurant/dashboard.php?error=Failed to delete item");
        }
    }

    //functions to update menu items
    if (isset($_POST['edit_menu'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image']; // Later, we'll handle file uploads

        if (Menu::updateItem($id, $name, $description, $price, $image)) {
            header("Location: ../views/restaurant/dashboard.php?success=Item succesfuly updated");
        } else {
            header("Location: ../views/restaurant/dashboard.php?error=Failed to update item");
        }
    }
}
?>
