<?php



// Function to get total number of restaurants based on their status filter
function getRestaurantCount($conn, $status = null) {
    $count = 0;
    $sql = "SELECT COUNT(*) as count FROM restaurants";
    
    // Add status filter if status is provided
    if ($status !== null) {
        $sql .= " WHERE confirmation_status = ?";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameter if status filter is used
        if ($status !== null) {
            $stmt->bind_param("s", $status);
        }

        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        return $count;
    } else {
        // Log the error to a file for easier tracking
        error_log("Database error: " . $conn->error . " SQL: " . $sql);
        throw new Exception("Database error: Could not retrieve restaurant count.");
    }
}

// Example usage with improved error handling and performance consideration
try {
    // Example: Getting counts for restaurants and caching them for improved performance
    if (!isset($res['total_restaurants'])) {
        $res['total_restaurants'] = getRestaurantCount($conn); // Cache total count in session
    }
    if (!isset($res['pending_restaurants'])) {
        $res['pending_restaurants'] = getRestaurantCount($conn, 'pending'); // Cache pending count
    }
    if (!isset($res['approved_restaurants'])) {
        $res['approved_restaurants'] = getRestaurantCount($conn, 'approved'); // Cache approved count
    }
    if (!isset($res['rejected_restaurants'])) {
        $res['rejected_restaurants'] = getRestaurantCount($conn, 'rejected'); // Cache approved count
    }

    // Now you can use the stats for display
    $stats = [
        'total_restaurants' => $res['total_restaurants'],
        'pending_restaurants' => $res['pending_restaurants'],
        'approved_restaurants' => $res['approved_restaurants'],
        'rejected_restaurants' => $res['rejected_restaurants']
    ];

} catch (Exception $e) {
    // Log detailed error for debugging
    error_log("Error: " . $e->getMessage());
    // Display a user-friendly message to the admin
    echo "There was an error fetching the restaurant statistics. Please try again later.";
}


//get number of users of this platform basecd on their role
function getUserCountByRole($conn, $role) {
    $count = 0;
    $sql = "SELECT COUNT(*) as count FROM users WHERE role = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    } else {
        throw new Exception("Database error: " . $conn->error);
    }
}
// Example usage
try {
    $deliveryCount = getUserCountByRole($conn, 'delivery');
    $customerCount = getUserCountByRole($conn, 'customer');
    $restaurantCount = getUserCountByRole($conn, 'restaurant');

    $userStats = [
        'delivery_persons' => $deliveryCount,
        'customers' => $customerCount,
        'restaurant_owners' => $restaurantCount
    ];
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "Error fetching user statistics. Please try again.";
}


?>




<!-- about users -->
 <h3>System users analytics:</h3>
<div class="stats-container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-indigo">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Total Restaurant Owners</p>
                    <p class="stat-value"><?= htmlspecialchars($userStats['restaurant_owners']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Total Customers</p>
                    <p class="stat-value"><?= htmlspecialchars($userStats['customers']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-blue">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Total Delivery partners</p>
                    <p class="stat-value"><?= htmlspecialchars($userStats['delivery_persons']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Total balance</p>
                    <p class="stat-value"><?= htmlspecialchars($stats['total_restaurants']) ?> Birr</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- about restaurants -->
 <h3>Restaurants on our platform:</h3>
<div class="stats-container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-yellow">
                    <i class="fa-solid fa-utensils fa-beat-fade"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Total restaurants</p>
                    <p class="stat-value"><?= htmlspecialchars($stats['total_restaurants']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-blue">
                    <i class="fa-solid fa-hourglass-half fa-spin"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Pending restaurants</p>
                    <p class="stat-value"><?= htmlspecialchars($stats['pending_restaurants']) ?></p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-green">
                   <i class="fa-regular fa-square-check fa-bounce"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Confirmed restaurants</p>
                    <p class="stat-value"><?= htmlspecialchars($stats['approved_restaurants']) ?></p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon bg-yellow">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-text">
                    <p class="stat-label">Rejected restaurants</p>
                    <p class="stat-value"><?= htmlspecialchars($stats['rejected_restaurants']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>