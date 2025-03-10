<?php
require_once '../models/manage_restaurant.php';
require_once '../config/database.php';

$restaurantModel = new Restaurant($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            $name = $_POST['name'];
            $location = $_POST['location'];
            $contact = $_POST['contact'];

            if ($restaurantModel->addRestaurant($name, $location, $contact)) {
                echo "Restaurant added successfully!";
            } else {
                echo "Failed to add restaurant.";
            }
        } elseif ($action == 'update') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $location = $_POST['location'];
            $contact = $_POST['contact'];
            $status = $_POST['status'];

            if ($restaurantModel->updateRestaurant($id, $name, $location, $contact, $status)) {
                echo "Restaurant updated successfully!";
            } else {
                echo "Failed to update restaurant.";
            }
        } elseif ($action == 'delete') {
            $id = $_POST['id'];

            if ($restaurantModel->deleteRestaurant($id)) {
                echo "Restaurant deleted successfully!";
            } else {
                echo "Failed to delete restaurant.";
            }
        }
    }
}
?>
