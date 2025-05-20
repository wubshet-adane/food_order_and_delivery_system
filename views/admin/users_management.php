<?php
header("Access-Control-Allow-Origin: *");
// Handle user status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $user_id = intval($_POST['user_id']);
        $status = $_POST['status'];
        $role = $_POST['role'];
        
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $status, $user_id);
        $stmt->execute();
        
        // Update role-specific table
        if ($role === 'delivery') {
            $stmt = $conn->prepare("UPDATE delivery_partners SET status = ? WHERE user_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE restaurant_owners SET status = ? WHERE user_id = ?");
        }
        $stmt->bind_param("si", $status, $user_id);
        $stmt->execute();
        
        $_SESSION['message'] = "User status updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: dashboard.php?page=user_management");
        exit();
    }
}

// Get filter parameters
$role = $_GET['role'] ?? 'all';
$status = $_GET['status'] ?? 'pending';

// Build query for users list
$query = "SELECT u.user_id, u.name as full_name, u.email, u.role, u.status, u.created_at,
                 IF(u.role = 'delivery', d.phone, r.phone) as phone,
                 IF(u.role = 'delivery', d.vehicle_type, NULL) as vehicle_type,
                 IF(u.role = 'delivery', d.balance, r.balance) as balance
          FROM users u
          LEFT JOIN delivery_partners d ON u.role = 'delivery' AND u.user_id = d.user_id
          LEFT JOIN restaurant_owners r ON u.role = 'restaurant' AND u.user_id = r.user_id
          WHERE u.role IN ('delivery', 'restaurant')";

$params = [];
$types = '';

if ($role !== 'all') {
    $query .= " AND u.role = ?";
    $params[] = $role;
    $types .= 's';
}

if ($status !== 'all') {
    $query .= " AND u.status = ?";
    $params[] = $status;
    $types .= 's';
}

$query .= " ORDER BY u.created_at DESC";

$stmt = $conn->prepare($query);
if(!$stmt)die($conn->error);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

// Get specific user details if user_id is set
$user_details = null;
$documents = [];
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Get basic user info
    $stmt = $conn->prepare("SELECT user_id, name as full_name, image, email, password, role, status, created_at, phone
                                    FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_details = $stmt->get_result()->fetch_assoc();
    
    if ($user_details) {
        // Get role-specific details
        if ($user_details['role'] === 'delivery') {
            $stmt = $conn->prepare("SELECT * FROM delivery_partners WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $role_details = $stmt->get_result()->fetch_assoc();
            
            // Prepare documents for delivery partner
            $documents = [
                ['label' => 'Government ID(front)', 'file' => $role_details['id_front']],
                ['label' => 'Government ID(Back)', 'file' => $role_details['id_back']],
                ['label' => 'License Copy', 'file' => $role_details['license_copy']]
            ];
        } else {
            $stmt = $conn->prepare("SELECT * FROM restaurant_owners WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $role_details = $stmt->get_result()->fetch_assoc();
            
            // Prepare documents for restaurant owner
            $documents = [
                ['label' => 'National ID Front', 'file' => $role_details['national_id_front']],
                ['label' => 'National ID Back', 'file' => $role_details['national_id_back']]
            ];
        }
        
        // Merge user details with role details
        $user_details = array_merge($user_details, $role_details);
    }
}
?>



    
    <div class="admin-container">
        <main class="main-content">
            <h1>User Management</h1>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            
            <div class="user-management">
                <!-- Filters Section -->
                <div class="user-filters">
                    <div class="filter-group">
                        <h3>Filter by Role</h3>
                        <div class="role-filter">
                            <div class="role-option <?= $role === 'all' ? 'active' : '' ?>" onclick="setFilter('role', 'all')">
                                <div class="role-badge" style="background: #d1d3e2;"></div>
                                <span>All Roles</span>
                            </div>
                            <div class="role-option <?= $role === 'restaurant' ? 'active' : '' ?>" onclick="setFilter('role', 'restaurant')">
                                <div class="role-badge restaurant-badge"></div>
                                <span>Restaurant Owners</span>
                            </div>
                            <div class="role-option <?= $role === 'delivery' ? 'active' : '' ?>" onclick="setFilter('role', 'delivery')">
                                <div class="role-badge delivery-badge"></div>
                                <span>Delivery Partners</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3>Filter by Status</h3>
                        <div>
                            <span class="status-option <?= $status === 'all' ? 'active' : '' ?>" onclick="setFilter('status', 'all')">All</span>
                            <span class="status-option pending-option <?= $status === 'pending' ? 'active' : '' ?>" onclick="setFilter('status', 'pending')">Pending</span>
                            <span class="status-option approved-option <?= $status === 'approved' ? 'active' : '' ?>" onclick="setFilter('status', 'approved')">Approved</span>
                            <span class="status-option rejected-option <?= $status === 'rejected' ? 'active' : '' ?>" onclick="setFilter('status', 'rejected')">Rejected</span>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content Area -->
                <div>
                    <?php if (isset($_GET['user_id']) && $user_details): ?>
                        <!-- User Details View -->
                        <div class="user-details-container">
                            <div class="user-details-header">
                                <div class="user-details-info">
                                    <div class="user-details-avatar <?= $user_details['role'] ?>-avatar">
                                        <?= strtoupper(substr($user_details['full_name'], 0, 1)) ?>
                                    </div>
                                    <div class="user-details-meta">
                                        <div class="user-details-name"><?= htmlspecialchars($user_details['full_name']) ?></div>
                                        <div class="user-details-email"><?= htmlspecialchars($user_details['email']) ?></div>
                                        <span class="user-details-role role-<?= $user_details['role'] ?>-bg">
                                            <?= $user_details['role'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="user-details-status">
                                    <span class="status-badge status-<?= $user_details['status'] ?>">
                                        <?= ucfirst($user_details['status']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="user-details-content">
                                <div class="details-section">
                                    <h3 class="section-title">Basic Information</h3>
                                    <div class="details-grid">
                                        <div class="detail-item-large">
                                            <span class="detail-label-large">Phone</span>
                                            <span class="detail-value-large"><?= htmlspecialchars($user_details['phone'] ?? 'N/A') ?></span>
                                        </div>
                                        <div class="detail-item-large">
                                            <span class="detail-label-large">Date of Birth</span>
                                            <span class="detail-value-large"><?= !empty($user_details['dob']) ? date('M j, Y', strtotime($user_details['dob'])) : 'N/A' ?></span>
                                        </div>
                                        <div class="detail-item-large">
                                            <span class="detail-label-large">Address</span>
                                            <span class="detail-value-large"><?= htmlspecialchars($user_details['address'] ?? 'N/A') ?></span>
                                        </div>
                                        <div class="detail-item-large">
                                            <span class="detail-label-large">Registered On</span>
                                            <span class="detail-value-large"><?= date('M j, Y', strtotime($user_details['created_at'])) ?></span>
                                        </div>
                                        
                                        <?php if ($user_details['role'] === 'delivery'): ?>
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">Vehicle Type</span>
                                                <span class="detail-value-large"><?= htmlspecialchars($user_details['vehicle_type'] ?? 'N/A') ?></span>
                                            </div>
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">License Number</span>
                                                <span class="detail-value-large"><?= htmlspecialchars($user_details['license_number'] ?? 'N/A') ?></span>
                                            </div>
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">Plate Number</span>
                                                <span class="detail-value-large"><?= htmlspecialchars($user_details['plate_number'] ?? 'N/A') ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Bank Details Section -->
                                <div class="details-section">
                                    <h3 class="section-title">Bank Details</h3>
                                    <div class="bank-details">
                                        <div class="bank-info">
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">Bank Name</span>
                                                <span class="detail-value-large"><?= htmlspecialchars($user_details['bank_name'] ?? 'N/A') ?></span>
                                            </div>
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">Account Name</span>
                                                <span class="detail-value-large"><?= htmlspecialchars($user_details['account_name'] ?? 'N/A') ?></span>
                                            </div>
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">Account Number</span>
                                                <span class="detail-value-large"><?= htmlspecialchars($user_details['account_number'] ?? 'N/A') ?></span>
                                            </div>
                                            <div class="detail-item-large">
                                                <span class="detail-label-large">Current Balance</span>
                                                <span class="detail-value-large"><?= isset($user_details['balance']) ? number_format($user_details['balance'], 2) . ' Birr' : 'N/A' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Documents Section -->
                                <?php if (!empty($documents)): ?>
                                    <div class="details-section">
                                        <h3 class="section-title">Documents</h3>
                                        <div class="documents-grid">
                                            <?php foreach ($documents as $doc): ?>
                                                <?php if (!empty($doc['file'])): ?>
                                                    <div class="document-card">
                                                        <div class="document-label"><?= $doc['label'] ?></div>
                                                        <img src="../../uploads/user_profiles/<?= htmlspecialchars($doc['file']) ?>" alt="<?= $doc['label'] ?>" class="document-image">
                                                        <div class="document-actions">
                                                            <a href="../../uploads/user_profiles/<?= htmlspecialchars($doc['file']) ?>" target="_blank" class="document-btn">
                                                                <i class="fas fa-expand"></i> View Full
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Update Form -->
                                <div class="details-section">
                                    <h3 class="section-title">Update Status</h3>
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?= $user_details['user_id'] ?>">
                                        <input type="hidden" name="role" value="<?= $user_details['role'] ?>">
                                        
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status:</label>
                                            <select id="status" name="status" class="form-select" required>
                                                <option value="pending" <?= $user_details['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="approved" <?= $user_details['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                                <option value="rejected" <?= $user_details['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                        </div>
                                        
                                        <div class="modal-actions">
                                            <a href="dashboard.php?page=user_management" class="btn btn-view">Back to List</a>
                                            <button type="submit" name="update_status" class="btn btn-primary">
                                                Update Status
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Users List View -->
                        <div class="user-list">
                            <?php if (empty($users)): ?>
                                <div class="no-users">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No users found matching your criteria</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <div class="user-card <?= $user['role'] ?> <?= isset($_GET['user_id']) && $_GET['user_id'] == $user['user_id'] ? 'active' : '' ?>"
                                         onclick="window.location.href='dashboard.php?page=user_management&user_id=<?= $user['user_id'] ?>&role=<?= $role ?>&status=<?= $status ?>'">
                                        <div class="user-header">
                                            <div class="user-info">
                                                <div class="user-avatar <?= $user['role'] ?>-avatar">
                                                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                                </div>
                                                <div class="user-meta">
                                                    <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                                                    <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                                                    <span class="user-role role-<?= $user['role'] ?>"><?= $user['role'] ?></span>
                                                </div>
                                            </div>
                                            <div class="user-status status-<?= $user['status'] ?>">
                                                <?= ucfirst($user['status']) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="user-details">
                                            <div class="detail-item">
                                                <span class="detail-label">Phone</span>
                                                <span class="detail-value"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></span>
                                            </div>
                                            
                                            <?php if ($user['role'] === 'delivery'): ?>
                                                <div class="detail-item">
                                                    <span class="detail-label">Vehicle</span>
                                                    <span class="detail-value"><?= htmlspecialchars($user['vehicle_type'] ?? 'N/A') ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="detail-item">
                                                <span class="detail-label">Balance</span>
                                                <span class="detail-value"><?= number_format($user['balance'], 2) ?> Birr</span>
                                            </div>
                                            
                                            <div class="detail-item">
                                                <span class="detail-label">Joined</span>
                                                <span class="detail-value"><?= date('M j, Y', strtotime($user['created_at'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function setFilter(type, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(type, value);
            
            // Reset page to 1 when changing filters
            if (type !== 'page') {
                url.searchParams.set('page', 'user_management');
            }
            
            window.location.href = url.toString();
        }
    </script>
</body>
</html>