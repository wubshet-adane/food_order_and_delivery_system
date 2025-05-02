<?php
// Get all restaurants owned by this user
$owner_id = $_SESSION['user_id'];
$restaurants_stmt = $conn->prepare("SELECT restaurant_id, name FROM restaurants WHERE owner_id = ?");

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
        if ($amount >= $min_amount) {
            $status = 'pending';
            $stmt = $conn->prepare("INSERT INTO ask_res_payout (restaurant_id, pay_amount, status) VALUES (?, ?, ?)");
            $stmt->bind_param("ids", $restaurant_id, $amount, $status);
            if ($stmt->execute()) {
                header("Location: ?page=payouts&restaurant_id=" . $restaurant_id . "&success=Payout request submitted!");
                exit;
            } else {
                $error = "Error submitting request. Please try again.";
            }
            $stmt->close();
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

<!--         
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="alert-danger">
                <p><strong>Error!</strong> <?php echo $error; ?></p>
                <button class="close" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>                
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success" id="alert-success">
                <p><strong>Success!</strong> <?php echo $success; ?></p>
                <button class="close" onclick="this.parentElement.style.display='none';"><i class="fa-solid fa-xmark"></i></button>
            </div>
        <?php endif; ?> -->

        <!-- Restaurant Selector -->
        <div class="restaurant-selector">
            <label for="restaurant-select">Select Restaurant:</label>
            <select id="restaurant-select" onchange="window.location.href='?page=payouts&restaurant_id='+this.value">
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
                    <input type="number" id="amount" name="amount" required min="100" step="1">
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
    <!-- <script>
        
        setTimeout(()=>{
                if(document.getElementById('alert-danger')){
                    document.getElementById('alert-danger').style.display = 'none';
                }
                if(document.getElementById('alert-success')){
                    document.getElementById('alert-success').style.display = 'none';
                }
            },6000   //close after 6 seconds
        );
    </script> -->