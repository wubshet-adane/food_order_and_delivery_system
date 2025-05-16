


<?php

// Handle payout actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action'];
        $admin_note = trim($_POST['admin_note'] ?? '');
        
        // Get the request details first
        $stmt = $conn->prepare("SELECT * FROM ask_res_payout WHERE id = ?");

        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        
        if ($request) {
            if ($action === 'approve') {
                // Check if user has sufficient balance
                $user_table = $request['restaurant_id'] ? 'restaurants' : 'delivery_partners';
                $user_id_field = $request['restaurant_id'] ? 'owner_id' : 'user_id';
                $user_id = $request['restaurant_id'] ? $request['restaurant_id'] : $request['user_id'];
                
                $stmt = $conn->prepare("SELECT balance FROM $user_table WHERE $user_id_field = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user && $user['balance'] >= $request['pay_amount']) {
                    // Start transaction
                    $conn->begin_transaction();
                    
                    try {
                        // Update payout request
                        $stmt = $conn->prepare("UPDATE ask_res_payout SET 
                                              status = 'paid', 
                                              paid_date = NOW(), 
                                              admin_note = ? 
                                              WHERE id = ?");
                        $stmt->bind_param("si", $admin_note, $request_id);
                        $stmt->execute();
                        
                        // Deduct from user's balance
                        $stmt = $conn->prepare("UPDATE $user_table SET 
                                              balance = balance - ? 
                                              WHERE $user_id_field = ?");
                        $stmt->bind_param("di", $request['pay_amount'], $user_id);
                        $stmt->execute();
                        
                        // // Record transaction (assuming you have a transactions table)
                        // $stmt = $conn->prepare("INSERT INTO transactions 
                        //                       (user_id, user_type, amount, type, reference_id, note) 
                        //                       VALUES (?, ?, ?, 'payout', ?, ?)");
                        // $user_type = $request['restaurant_id'] ? 'restaurant' : 'delivery';
                        // $stmt->bind_param("isdss", $user_id, $user_type, $request['pay_amount'], $request_id, $admin_note);
                        // $stmt->execute();
                        
                        $conn->commit();
                        
                        $_SESSION['message'] = "Payout approved successfully!";
                        $_SESSION['message_type'] = "success";
                    } catch (Exception $e) {
                        $conn->rollback();
                        $_SESSION['message'] = "Error processing payout: " . $e->getMessage();
                        $_SESSION['message_type'] = "error";
                    }
                } else {
                    $_SESSION['message'] = "User doesn't have sufficient balance for this payout!";
                    $_SESSION['message_type'] = "error";
                }
            } elseif ($action === 'reject') {
                $stmt = $conn->prepare("UPDATE ask_res_payout SET 
                                      status = 'rejected', 
                                      admin_note = ? 
                                      WHERE id = ?");
                $stmt->bind_param("si", $admin_note, $request_id);
                $stmt->execute();
                
                $_SESSION['message'] = "Payout request rejected!";
                $_SESSION['message_type'] = "success";
            }
        } else {
            $_SESSION['message'] = "Payout request not found!";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: dashboard.php?page=Manage_Payouts");
        exit();
    }
}

// Get filter parameters
$status = $_GET['status'] ?? 'pending';
$user_type = $_GET['user_type'] ?? 'all';

// Build query for payout requests
$query = "SELECT r.*, u.name AS user_name,
            CASE 
                WHEN r.restaurant_id IS NOT NULL THEN 'restaurant' 
                ELSE 'delivery' 
            END AS user_type,
            CASE 
                WHEN r.restaurant_id IS NOT NULL THEN res.balance 
                ELSE dp.balance 
            END AS user_balance
        FROM ask_res_payout r
        JOIN users u ON u.user_id = r.user_id
        LEFT JOIN restaurants res ON r.user_id = res.owner_id AND r.restaurant_id IS NOT NULL
        LEFT JOIN delivery_partners dp ON r.user_id = dp.user_id AND r.restaurant_id IS NULL
        WHERE r.status = ?";

$params = [$status];
$types = "s";

if ($user_type !== 'all') {
    if ($user_type === 'restaurant') {
        $query .= " AND r.restaurant_id is not null";
    } else {
        $query .= " AND r.restaurant_id IS NULL";
    }
}

$query .= " ORDER BY r.request_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
?>


   
    <div class="admin-container">
        
        <main class="main-content">
            <h1>Payout Management</h1>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            
            <div class="payout-management">
                <div class="payout-filters">
                    <div class="filter-group">
                        <label for="status" class="filter-label">Status:</label>
                        <select id="status" class="filter-select" onchange="updateFilters()">
                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="user_type" class="filter-label">User Type:</label>
                        <select id="user_type" class="filter-select" onchange="updateFilters()">
                            <option value="all" <?= $user_type === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="restaurant" <?= $user_type === 'restaurant' ? 'selected' : '' ?>>Restaurants</option>
                            <option value="delivery" <?= $user_type === 'delivery' ? 'selected' : '' ?>>Delivery Partners</option>
                        </select>
                    </div>
                </div>
                
                <div class="payout-requests">
                    <?php if (empty($requests)): ?>
                        <div class="no-requests">
                            <i class="fas fa-inbox"></i>
                            <p>No payout requests found matching your criteria</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($requests as $request): ?>
                            <div class="payout-card <?= $request['user_type'] ?> <?= $request['status'] ?>">
                                <div class="payout-header">
                                    <div class="payout-user">
                                        <div class="user-avatar <?= $request['user_type'] ?>-avatar">
                                            <?= strtoupper(substr($request['user_name'], 0, 1)) ?>
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name"><?= htmlspecialchars($request['user_name']) ?></div>
                                            <div class="user-type"><?= $request['user_type'] ?></div>
                                        </div>
                                    </div>
                                    <div class="payout-status status-<?= $request['status'] ?>">
                                        <?= ucfirst($request['status']) ?>
                                    </div>
                                </div>
                                
                                <div class="payout-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Request Date</span>
                                        <span class="detail-value"><?= date('M j, Y', strtotime($request['request_date'])) ?></span>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <span class="detail-label">Current Balance</span>
                                        <span class="detail-value"><?= number_format($request['user_balance'], 2) ?> Birr</span>
                                    </div>
                                    
                                    <?php if ($request['status'] === 'paid' && $request['paid_date']): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Paid Date</span>
                                            <span class="detail-value"><?= date('M j, Y', strtotime($request['paid_date'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="detail-item">
                                        <span class="detail-label">Request Amount</span>
                                        <span class="payout-amount"><?= number_format($request['pay_amount'], 2) ?> Birr</span>
                                    </div>
                                </div>
                                
                                <?php if (!empty($request['admin_note'])): ?>
                                    <div class="admin-note">
                                        <div class="note-label">Admin Note:</div>
                                        <div class="note-content"><?= htmlspecialchars($request['admin_note']) ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($request['status'] === 'pending'): ?>
                                    <div class="payout-actions">
                                        <button class="btn btn-approve" onclick="showActionModal(<?= $request['id'] ?>, 'approve')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-reject" onclick="showActionModal(<?= $request['id'] ?>, 'reject')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Action Modal -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div class="modal-title" id="modalTitle">Approve Payout</div>
            
            <form id="actionForm" method="POST">
                <input type="hidden" id="requestId" name="request_id">
                <input type="hidden" id="actionType" name="action">
                
                <div class="form-group">
                    <label for="adminNote" class="form-label">Admin Note (Optional):</label>
                    <textarea id="adminNote" name="admin_note" class="form-textarea" placeholder="Add any notes about this action..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-view" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn" id="modalActionBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function updateFilters() {
            const status = document.getElementById('status').value;
            const userType = document.getElementById('user_type').value;
            
            window.location.href = `dashboard.php?page=Manage_Payouts&status=${status}&user_type=${userType}`;
        }
        
        function showActionModal(requestId, action) {
            const modal = document.getElementById('actionModal');
            const modalTitle = document.getElementById('modalTitle');
            const actionBtn = document.getElementById('modalActionBtn');
            
            document.getElementById('requestId').value = requestId;
            document.getElementById('actionType').value = action;
            
            if (action === 'approve') {
                modalTitle.textContent = 'Approve Payout';
                actionBtn.textContent = 'Approve';
                actionBtn.className = 'btn btn-approve';
            } else {
                modalTitle.textContent = 'Reject Payout';
                actionBtn.textContent = 'Reject';
                actionBtn.className = 'btn btn-reject';
            }
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('actionModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('actionModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>