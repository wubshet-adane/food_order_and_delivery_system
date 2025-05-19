<?php
ob_start(); // Must be the first thing

session_start();
require_once '../../config/database.php';

$owner_id = $_SESSION['user_id'] ?? null;
if (!$owner_id) {
    header('Location: ../auth/customer_login.php');
    exit();
}

$order_id = $_GET['order_id'] ?? 72;
if (!$order_id) die("No order ID found.");

// Fetch order + payment + delivery address info
$stmt = $conn->prepare("
    SELECT o.*, u.name AS account_name, r.restaurant_id, r.name AS restaurant_name,r.image AS res_image, 
           cda.name AS customer_name, cda.email AS customer_email, 
           cda.delivery_address AS delivery_address, cda.phone AS customer_phone,
           p.amount AS total_amount, p.payment_method, p.delivery_person_fee,
           p.payment_file AS payment_screenshot, p.transaction_id AS transaction_id
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.restaurant_id
    JOIN users u ON o.customer_id = u.user_id
    JOIN payments p ON o.order_id = p.order_id
    JOIN customer_delivery_address cda ON o.customer_id = cda.user_id
    WHERE o.order_id = ? AND o.customer_id = ?");

if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("ii", $order_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) die("No order found.");

$progress_map = [
    'pending' => ['value' => 20, 'color' => 'bg-yellow-500'],
    'Preparing' => ['value' => 40, 'color' => 'bg-blue-500'],
    'Ready_for_delivery' => ['value' => 60, 'color' => 'bg-purple-500'],
    'Delivering' => ['value' => 80, 'color' => 'bg-green-300'],
    'Delivered' => ['value' => 100, 'color' => 'bg-green-600']
];
$current_progress = $progress_map[$order['status']] ?? ['value' => 0, 'color' => 'bg-gray-300'];
// Fetch ordered items
$stmt_items = $conn->prepare("
    SELECT m.*, oi.*
    FROM order_items oi
    JOIN menu m ON oi.menu_id = m.menu_id
    WHERE oi.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$order_items = $result_items->fetch_all(MYSQLI_ASSOC);
$stmt_items->close();

// Submit review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $review = trim($_POST['review']);
    echo $review;
    
    $rating = intval($_POST['rating'] ?? 5);
    echo $rating;
    if (!empty($review)) {
        $stmt = $conn->prepare("
            INSERT INTO review (restaurant_id, user_id, rating, review_text, created_at)
            VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $order['restaurant_id'], $owner_id, $rating, $review);
        if (!$stmt->execute()) {
            echo "<script>document.addEventListener('DOMContentLoaded',()=>{
                showToast('Error submitting review. Please try again.');
            });</script>";
        } else {
            // Redirect to the same page to avoid resubmission
            header("Location: " . $_SERVER['PHP_SELF'] . "?order_id=" . $order_id . "&review_submitted=1");
            exit();
        }
        $stmt->close();
        echo "<script>document.addEventListener('DOMContentLoaded',()=>{
            document.getElementById('reviewModal').classList.add('hidden');
            showToast('Thank you for your review!');
        });</script>";
    }
}
$conn->close();

// Format date
$order_date = new DateTime($order['order_date']);
$formattedDate = $order_date->format('F j, Y, g:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Confirmation - <?= htmlspecialchars($order['restaurant_name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Your order confirmation for <?= htmlspecialchars($order['restaurant_name']) ?>">
  <meta name="theme-color" content="#4f46e5">
  <link rel="icon" href="../../public/images/logo-icon.png">
  <link rel="stylesheet" href="css/topbar.css">
    <link rel="stylesheet" href="css/order_success_page.css">
    <link rel="stylesheet" href="css/footer.css">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
  
    
    .star-rating input {
      display: none;
    }
    
    .star-rating label {
      color: #d1d5db;
      font-size: 2rem;
      cursor: pointer;
      transition: color 0.2s;
    }
    
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: #f59e0b;
    }
    
    .star-rating input:checked + label {
      color: #f59e0b;
    }
    
    .toast {
      animation: slideIn 0.3s, fadeOut 0.5s 2.5s forwards;
    }
    
    @keyframes slideIn {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes fadeOut {
      to { opacity: 0; }
    }
  </style>
</head>
<body >

  <?php include "topbar.php"?>
  <main class="container mx-auto px-4 py-8">
    <!-- Success Message -->
    <div class="max-w-4xl mx-auto mb-8 p-6 bg-white rounded-xl shadow-sm order-card">
      <div class="text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-check text-yellow-500 text-3xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-500 mb-2">Thank you for your order, <span class="text-blue-700"><?= htmlspecialchars($order['customer_name']) ?></span>!</h2>
        <p class="text-gray-600 mb-4">Your order #<?= $order['order_id'] ?> is placed and being prepared.</p>
        
        <!-- Order Status Progress -->
        <div class="mb-6">
          <div class="flex justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Order Status</span>
            <span class="text-sm font-medium <?= $current_progress['color'] ?> text-white px-2 py-1 rounded-full">
              <?= htmlspecialchars($order['status']) ?>
            </span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="progress-bar h-2.5 rounded-full <?= $current_progress['color'] ?>" 
                 style="width: <?= $current_progress['value'] ?>%"></div>
          </div>
          <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>Placed</span>
            <span>Preparing</span>
            <span>Ready for delivery</span>
            <span>On the way</span>
            <span>Delivered</span>
          </div>
        </div>
        
        <div class="flex flex-wrap justify-center gap-4 mt-6">
          <button onclick="window.print()" class="flex items-center px-4 py-2 bg-indigo-300 text-black rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-print mr-2"></i> Print Receipt
          </button>
          <button onclick="document.getElementById('qrModal').classList.remove('hidden')" 
                  class="flex items-center px-4 py-2 bg-purple-300 text-black rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-qrcode mr-2"></i> View QR Code
          </button>
          <button onclick="document.getElementById('reviewModal').classList.remove('hidden')" 
                  class="flex items-center px-4 py-2 bg-green-300 text-black rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-star mr-2"></i> Leave Review
          </button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
      <!-- Order Summary -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Restaurant Info -->
        <div class="bg-white p-6 rounded-xl shadow-sm order-card">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-store mr-2 text-yellow-500"></i> Restaurant Details
          </h3>
          <div class="flex items-start space-x-4">
            <div class="flex-1">
              <h4 class="font-medium text-gray-900"><?= htmlspecialchars($order['restaurant_name']) ?></h4>
              <p class="text-gray-600 mt-1">Order ID: #<?= $order['order_id'] ?></p>
              <p class="text-gray-600">Placed on: <?= $formattedDate ?></p>
            </div>
            <div class="w-16 h-16 rounded-lg overflow-hidden">
              <!-- Restaurant logo would go here -->
              <div class="w-full h-full bg-indigo-100 flex items-center justify-center">
                <img src="../restaurant/restaurantAsset/<?=$order['res_image'];?>" alt="logo" class="w-full h-full object-cover">
              </div>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white p-6 rounded-xl shadow-sm order-card">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-list-alt mr-2 text-yellow-500"></i> Your Order
          </h3>
          <div class="space-y-4">
            <?php foreach ($order_items as $item): ?>
              <div class="flex items-start space-x-4">
                <?php if ($item['image']): ?>
                  <img src="../../uploads/menu_images/<?= htmlspecialchars($item['image']) ?>" 
                       alt="<?= htmlspecialchars($item['name']) ?>" 
                       class="w-20 h-20 rounded-lg object-cover menu-item-img">
                <?php else: ?>
                  <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center menu-item-img">
                    <i class="fas fa-image text-gray-400"></i>
                  </div>
                <?php endif; ?>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></h4>
                  <p class="text-gray-600 text-sm">quantity: <?= $item['quantity'] ?></p>
                  <?php if ($item['discount'] > 0): ?>
                    <p class="text-gray-600 text-sm">
                      <span class="line-through">ETB <?= number_format($item['price'], 2) ?></span>
                      <span class="text-green-600 ml-2"><?= $item['discount'] ?>% off</span>
                    </p>
                  <?php endif; ?>
                </div>
                <div class="text-right">
                  <p class="font-medium text-gray-900">
                    ETB <?= number_format(($item['quantity'] * $item['price']) - ($item['quantity'] * $item['discount']/100), 2) ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <div class="border-t border-gray-200 mt-6 pt-4">
            <!-- <div class="flex justify-between py-2">
              <span class="text-gray-600"></span>
              <span class="font-medium">ETB <?= number_format($order['total_amount'], 2) ?></span>
            </div> -->
            <div class="flex justify-between py-2 border-t border-gray-200 mt-2">
              <span class="text-gray-800 font-semibold">Total Balance which you paid</span>
              <span class="text-indigo-600 font-bold">ETB <?= number_format((($order['total_amount'] / 0.95) + ($order['delivery_person_fee'] / 0.97 )), 2) ?></span>
            </div>
          </div>
          
          <?php if (!empty($order['o_description'])): ?>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
              <h4 class="text-sm font-medium text-blue-800 mb-1">Order Note:</h4>
              <p class="text-sm text-blue-700 italic"><?= htmlspecialchars($order['o_description']) ?></p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Delivery Info -->
        <div class="bg-white p-6 rounded-xl shadow-sm order-card">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-truck mr-2 text-yellow-500"></i> Delivery Information
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <ERD class="text-sm font-medium text-gray-500">DELIVERY ADDRESS YOU ENTERED</ERD>
              <p class="mt-1 text-gray-900"><?= htmlspecialchars($order['delivery_address']) ?></p>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-500">CONTACT</h4>
              <p class="mt-1 text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></p>
              <p class="text-gray-600"><?= htmlspecialchars($order['customer_phone']) ?></p>
            </div>
          </div>
          
          <div class="mt-4">
            <h4 class="text-sm font-medium text-gray-500">SECRET CODE</h4>
            <div class="mt-2 flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
              <span class="font-mono font-bold text-indigo-800"><?= htmlspecialchars($order['secret_code']) ?></span>
              <button onclick="copyToClipboard('<?= htmlspecialchars($order['secret_code']) ?>')" 
                      class="text-indigo-600 hover:text-indigo-800 transition">
                <i class="fas fa-copy"></i> Copy
              </button>
            </div>
            <p class="mt-1 text-xs text-gray-500">Keep the above code securely!!.</p>
          </div>
        </div>
      </div>

      <!-- Payment & Help -->
      <div class="space-y-6">
        <!-- Payment Summary -->
        <div class="bg-white p-6 rounded-xl shadow-sm order-card">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-credit-card mr-2 text-yellow-500"></i> Payment Summary
          </h3>
          <div class="space-y-3">
            <div>
              <p class="text-sm text-gray-500">Payment Method</p>
              <p class="font-medium">
                <?= htmlspecialchars($order['payment_method']) ?>
                <span class="ml-2 text-green-500">
                  <i class="fas fa-check-circle"></i> Paid
                </span>
              </p>
            </div>
            
            <div>
              <p class="text-sm text-gray-500">Transaction ID</p>
              <p class="font-mono text-sm"><?= htmlspecialchars($order['transaction_id']) ?></p>
            </div>
            
            <?php if (!empty($order['payment_screenshot'])): ?>
              <div class="mt-4">
                <p class="text-sm text-gray-500 mb-2">Payment Proof screenshot</p>
                <img src="../../uploads/payments/<?= htmlspecialchars($order['payment_screenshot']) ?>" 
                     alt="Payment proof" 
                     class="rounded-lg border border-gray-200 cursor-pointer"
                     onclick="openImageModal('../../uploads/payments/<?= htmlspecialchars($order['payment_screenshot']) ?>')">
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Need Help? -->
        <div class="bg-white p-6 rounded-xl shadow-sm order-card">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-question-circle mr-2 text-yellow-500"></i> Need Help?
          </h3>
          <p class="text-gray-600 mb-4">If you have any questions about your order, please contact us.</p>
          <div class="space-y-3">
            <a href="tel:+251912345678" class="flex items-center text-indigo-600 hover:text-indigo-800 transition">
              <i class="fas fa-phone-alt mr-2 text-yellow-500"></i> +251 965868933
            </a>
            <a href="/../food_ordering_system/public/support.php" class="flex items-center text-indigo-600 hover:text-indigo-800 transition">
              <i class="fas fa-envelope mr-2 text-yellow-500"></i> g3_food order/support
            </a>
          </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white p-6 rounded-xl shadow-sm order-card">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-history mr-2 text-yellow-500"></i> Order Timeline
          </h3>
          <div class="space-y-4">
            <div class="flex items-start">
              <div class="flex-shrink-0 mt-1">
                <div class="w-4 h-4 bg-indigo-500 rounded-full"></div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Order placed</p>
                <p class="text-sm text-gray-500"><?= $formattedDate ?></p>
              </div>
            </div>
            
            <div class="flex items-start">
              <div class="flex-shrink-0 mt-1">
                <div class="w-4 h-4 <?= $order['status'] !== 'pending' ? 'bg-indigo-500' : 'bg-gray-300' ?> rounded-full"></div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Order confirmed</p>
                <p class="text-sm text-gray-500">
                  <?= $order['status'] !== 'pending' ? $formattedDate : 'Pending' ?>
                </p>
              </div>
            </div>
            
            <div class="flex items-start">
              <div class="flex-shrink-0 mt-1">
                <div class="w-4 h-4 <?= in_array($order['status'], ['Preparing', 'Ready_for_delivery', 'Delivering', 'Delivered']) ? 'bg-indigo-500' : 'bg-gray-300' ?> rounded-full"></div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Food preparation</p>
                <p class="text-sm text-gray-500">
                  <?= in_array($order['status'], ['Preparing', 'Ready_for_delivery', 'Delivered']) ? 'In progress' : 'Pending' ?>
                </p>
              </div>
            </div>
            
            <div class="flex items-start">
              <div class="flex-shrink-0 mt-1">
                <div class="w-4 h-4 <?= in_array($order['status'], ['Ready_for_delivery', 'Delivering', 'Delivered']) ? 'bg-indigo-500' : 'bg-gray-300' ?> rounded-full"></div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Out for delivery</p>
                <p class="text-sm text-gray-500">
                  <?= in_array($order['status'], ['Delivering', 'Delivered']) ? 'On the way' : 'Pending' ?>
                </p>
              </div>
            </div>
            
            <div class="flex items-start">
              <div class="flex-shrink-0 mt-1">
                <div class="w-4 h-4 <?= $order['status'] === 'Delivered' ? 'bg-indigo-500' : 'bg-gray-300' ?> rounded-full"></div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">Delivered</p>
                <p class="text-sm text-gray-500">
                  <?= $order['status'] === 'Delivered' ? 'Completed' : 'Pending' ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- QR Code Modal -->
  <div id="qrModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-xl max-w-sm w-full p-6 relative">
      <button onclick="document.getElementById('qrModal').classList.add('hidden')" 
              class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
      <h3 class="text-xl font-bold text-center text-gray-800 mb-4">Order Verification</h3>
      <div class="flex justify-center mb-4">
        <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= htmlspecialchars($order['secret_code']) ?>&size=200x200" 
             alt="QR Code" class="w-48 h-48">
      </div>
      <p class="text-center text-gray-600 mb-2">Scan this QR code to verify your order</p>
      <p class="text-center text-sm text-gray-500">Order #<?= $order['order_id'] ?></p>
      <div class="mt-4 flex justify-center">
        <button onclick="downloadQRCode()" 
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
          <i class="fas fa-download mr-2"></i> Download QR
        </button>
      </div>
    </div>
  </div>

  <!-- Review Modal -->
  <div id="reviewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-xl max-w-md w-full p-6 relative">
      <button onclick="document.getElementById('reviewModal').classList.add('hidden')" 
              class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
        <i class="fas fa-times"></i>
      </button>
      <h3 class="text-xl font-bold text-center text-gray-800 mb-4">Rate Your Experience</h3>
      <form method="POST">
        <div class="mb-6">
          <p class="text-center text-gray-600 mb-3">How would you rate <?= htmlspecialchars($order['restaurant_name']) ?>?</p>
          <div class="star-rating flex justify-center space-x-2">
            <input type="radio" id="star5" name="rating" value="5" checked />
            <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
            <input type="radio" id="star4" name="rating" value="4" />
            <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
            <input type="radio" id="star3" name="rating" value="3" />
            <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
            <input type="radio" id="star2" name="rating" value="2" />
            <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
            <input type="radio" id="star1" name="rating" value="1" />
            <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
          </div>
        </div>
        <div class="mb-4">
          <label for="review" class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
          <textarea name="review" id="review" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" 
                    placeholder="How was your experience?"></textarea>
        </div>
        <div class="flex justify-center space-x-3">
          <button type="submit" name="submit_review" 
                  class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-check mr-2"></i> Submit
          </button>
          <button type="button" onclick="document.getElementById('reviewModal').classList.add('hidden')" 
                  class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-times mr-2"></i> Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Image Modal -->
  <div id="imageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-75">
    <div class="relative max-w-4xl">
      <button onclick="document.getElementById('imageModal').classList.add('hidden')" 
              class="absolute -top-15 right-4 bg-red-500 text-white hover:text-gray-300">
        <i class="fas fa-times text-2xl ml-4 mr-4"></i>
      </button>
      <img id="modalImage" src="" alt="Enlarged view" class="max-h-screen rounded-lg">
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toast" class="fixed bottom-4 right-4 hidden">
    <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
      <i class="fas fa-check-circle mr-2"></i>
      <span id="toastMessage"></span>
    </div>
  </div>

  <?php include_once __DIR__ . "/footer.php";?>

  <script>
    // Show review modal after delay
    setTimeout(() => {
      document.getElementById('reviewModal').classList.remove('hidden');
    }, 1000); // Show after 1 seconds

    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!');
      });
    }

    function showToast(message) {
      const toast = document.getElementById('toast');
      const toastMessage = document.getElementById('toastMessage');
      toastMessage.textContent = message;
      toast.classList.remove('hidden');
      setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    function openImageModal(src) {
      document.getElementById('modalImage').src = src;
      document.getElementById('imageModal').classList.remove('hidden');
    }

    function downloadQRCode() {
      const link = document.createElement('a');
      link.href = `https://api.qrserver.com/v1/create-qr-code/?data=<?= htmlspecialchars($order['secret_code']) ?>&size=200x200`;
      link.download = `order-<?= $order['order_id'] ?>-qrcode.png`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>
  <script src="javaScript/scroll_up.js"></script>
</body>
</html>