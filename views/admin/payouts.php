<?php
//controll form submition continously when page reload
ob_start(); // 🛡️ Output buffering starts

// Get all restaurants owned by this user
$owner_id = $_SESSION['user_id'];
$restaurants_stmt = $conn->prepare("SELECT * FROM restaurants WHERE owner_id = ?");
$restaurants_stmt->bind_param("i", $owner_id);
$restaurants_stmt->execute();
$restaurants = $restaurants_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$restaurants_stmt->close();

// Get selected restaurant (from GET parameter or default to first)
$selected_restaurant_id = $_GET['restaurant_id'] ?? ($restaurants[0]['id'] ?? 0);

// Handle payout request form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    $restaurant_id = intval($_POST['restaurant_id']);

    //check restaurant full balance
    $stmt = $conn->prepare("
        SELECT sum(p.amount) as full_balance
        FROM orders o
        JOIN payments p ON o.order_id = p.order_id
        JOIN restaurants r ON o.restaurant_id = r.restaurant_id
        WHERE o.restaurant_id = ? AND o.status = 'delivered' AND r.owner_id = ?
        GROUP BY o.restaurant_id");
    $stmt->bind_param("ii", $restaurant_id, $owner_id);    
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant = $result->fetch_assoc();
    $stmt->close();

    // echo "<script>console.log('Restaurant ID: {$restaurant_id}');</script>";
    // echo "<script>console.log('Owner ID: {$owner_id}');</script>";
    // echo "<script>console.log('Restaurant: " . json_encode($restaurant) . "');</script>";
    // echo "<script>console.log('Result: " . json_encode($result->fetch_all(MYSQLI_ASSOC)) . "');</script>";
    if ($restaurant) {
        $full_balance = $restaurant['full_balance'];
    } else {
        $full_balance = 0;
    }

    // echo "<script>console.log('Full balance: {$full_balance}');</script>";

    // Validate restaurant belongs to owner
    $valid_restaurant = false;
    foreach ($restaurants as $r) {
        if ($r['restaurant_id'] == $restaurant_id) {
            $valid_restaurant = true;
            break;
        }
    }

    if ($valid_restaurant) {
        $min_amount = 500; // example rule
        // Check if amount is valid and within limits
        if ($amount >= $min_amount) {
            // Check if amount is less than or equal to full balance
            if ($amount <= $full_balance) {
                $status = 'pending';
                // First, check if there's already a pending request
                $checkStmt = $conn->prepare("SELECT id FROM ask_res_payout WHERE user_id = ? AND restaurant_id = ? AND status = ?");
                $checkStmt->bind_param("iis", $owner_id, $restaurant_id, $status);
                $checkStmt->execute();
                $checkStmt->store_result();
                if ($checkStmt->num_rows > 0) {
                    // A pending request already exists
                    $error = "You already have a pending payout request.";
                } else {
                    // No pending request found, proceed with inserting a new one
                    $checkStmt->close();
                    $stmt = $conn->prepare("INSERT INTO ask_res_payout (user_id, restaurant_id, pay_amount, status) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iids",  $owner_id, $restaurant_id, $amount, $status);
                    if ($stmt->execute()) {
                        header("Location: ?page=payouts&restaurant_id=" . $restaurant_id . "&success=Payout request submitted!");
                        exit;
                    } else {
                        $error = "Error submitting request. Please try again.";
                    }

                    $stmt->close();
                }
            }else {
                $error = "Requested amount exceeds available balance of {$full_balance} birr.";
            }
        } else {
            $error = "Minimum payout amount is {$min_amount} birr.";
        }
    } else {
        $error = "Invalid restaurant selection.";
    }
}

// Fetch all payout requests for selected restaurant
$payouts = [];
if ($selected_restaurant_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM ask_res_payout WHERE restaurant_id = ? ORDER BY request_date DESC");
    $stmt->bind_param("i", $selected_restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payouts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

    <div class="payout-container">
        <h3><i class="fas fa-money-bill-wave"></i> Payout Requests</h3>

        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="alert-danger" style="display: flex; justify-content: space-between;">
                <p><strong>Error!</strong> <?php echo $error; ?></p>
                <button class=".close-error" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>                
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success" id="alert-success" style="display: flex; justify-content: space-between;">
                <p><strong>Success!</strong> <?php echo $success; ?></p>
                <button class=".close-error" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endif; ?>

        <!-- Restaurant Selector -->
        <div class="restaurant-selector">
            <label for="restaurant-select">Select Restaurant:</label>
            <select id="restaurant-select" onchange="window.location.href='?page=payouts&restaurant_id='+this.value">
                <!--add disabled display on option selection area-->
                <option value=""> select restaurant</option>
                <?php foreach ($restaurants as $restaurant): ?>
                    <option value="<?= $restaurant['restaurant_id'] ?>" <?= $restaurant['restaurant_id'] == $selected_restaurant_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($restaurant['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Payout Request Form -->
        <div class="payout-form">
            <h3><i class="fas fa-plus-circle"></i> New Payout Request</h3>
            <form method="POST">
                <input type="hidden" name="restaurant_id" value="<?= $selected_restaurant_id ?>">
                
                <div class="form-group">
                    <label for="amount">Amount (birr):</label>
                    <input type="number" id="amount" name="amount" required min="100" step="100">
                </div>
                <!-- 
                <div class="form-group">
                    <label for="method">Payment Method:</label>
                    <select id="method" name="method" required>
                        <option value="">-- Choose --</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Telebirr">Telebirr</option>
                        <option value="CBE Birr">CBE Birr</option>
                        <option value="Amole">Amole</option>
                    </select>
                </div> 

                <div class="form-group">
                    <label for="account_info">Account Information:</label>
                    <textarea id="account_info" name="account_info" required placeholder="Account number, phone number, bank name etc."></textarea>
                </div>
                -->

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>

        <!-- Payout History -->
        <h3><i class="fas fa-history"></i> Payout History</h3>
        <?php if (empty($payouts)): ?>
            <div class="no-payouts">No payout requests found for this restaurant.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Payout Date</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payouts as $i => $payout): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= number_format($payout['pay_amount'], 2) ?> birr</td>
                            <td class="status-<?= strtolower($payout['status']) ?>">
                                <?= ucfirst($payout['status']) ?>
                            </td>
                            <td><?= date("M d, Y h:i A", strtotime($payout['request_date'])) ?></td>
                            <td><?= $payout['paid_date'] ? date("M d, Y", strtotime($payout['paid_date'])) : '-' ?></td>
                            <td><?= htmlspecialchars($payout['admin_note'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script>
        
        setTimeout(()=>{
                if(document.getElementById('alert-danger')){
                    document.getElementById('alert-danger').style.display = 'none';
                }
                if(document.getElementById('alert-success')){
                    document.getElementById('alert-success').style.display = 'none';
                }
            },6000   //close after 6 seconds
        );
    </script>