<?php

// Check admin authentication
if (!isset($_SESSION['userType']) && $_SESSION['userType'] = 'admin') {
    header('Location: ../auth/admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Owners Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="content">
            <h1>Restaurant Owners Management</h1>
            
            <div class="filter-bar">
                <div class="search-box">
                    <input type="text" id="search-owners" placeholder="Search owners...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <select id="filter-status">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending Approval</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            
            <div class="metrics-container">
                <div class="metric-card">
                    <h3>Total Owners</h3>
                    <p id="total-owners">0</p>
                </div>
                <div class="metric-card">
                    <h3>Pending Approval</h3>
                    <p id="pending-owners">0</p>
                </div>
                <div class="metric-card">
                    <h3>Active Owners</h3>
                    <p id="active-owners">0</p>
                </div>
                <div class="metric-card">
                    <h3>Suspended</h3>
                    <p id="suspended-owners">0</p>
                </div>
            </div>
            
            <div class="owners-table-container">
                <table id="owners-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <!-- <th>Phone number</th> -->
                            <th>Restaurant</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                            <th>Performance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
                <div class="pagination" id="pagination">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
            
            <!-- Modal for owner details -->
            <div id="owner-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <div id="modal-content"></div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>
</body>
</html>