<?php

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['restaurant_id'])) {
        $restaurant_id = intval($_POST['restaurant_id']);
        $action = $_POST['action'];
        
        if ($action === 'approve' || $action === 'reject') {
            $status = $action === 'approve' ? 'approved' : 'rejected';
            $stmt = $conn->prepare("UPDATE restaurants SET confirmation_status = ? WHERE restaurant_id = ?");
            $stmt->bind_param("si", $status, $restaurant_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Restaurant $status successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Failed to update restaurant status.";
                $_SESSION['message_type'] = "error";
            }
            $stmt->close();
        }
    }
    //header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Get all restaurant owners with their restaurants count
$owners = [];
$query = "SELECT u.user_id, u.name as full_name, u.phone, u.email, COUNT(r.restaurant_id) as restaurant_count 
          FROM users u 
          LEFT JOIN restaurants r ON u.user_id = r.owner_id 
          WHERE u.role = 'restaurant' 
          GROUP BY u.user_id";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $owners[] = $row;
    }
}else{
    die('feild to get data!!');
}

// Get restaurants for a specific owner when requested
$restaurants = [];
$selected_owner = null;
if (isset($_GET['owner_id'])) {
    $owner_id = intval($_GET['owner_id']);
    $query = "SELECT * FROM restaurants WHERE owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }
    
    // Get owner details
    foreach ($owners as $owner) {
        if ($owner['user_id'] == $owner_id) {
            $selected_owner = $owner;
            break;
        }
    }
}
?>


    <div class="admin-container">
        <main class="main-content">
            <h>Restaurant Management</h>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div id="session-alert" style="display: flex; justify-content: space-between; flex-direction: row; align-items: center; padding: 1rem; border-radius: 5px;" class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <span><?= $_SESSION['message'] ?></span>
                    <span onclick="document.getElementById('session-alert').style.display='none';" style="cursor: pointer;">
                        <i class="fa-solid fa-xmark" style="border-radius: 5px; background-color: #ff990004; padding: 5px;"></i>
                    </span>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('session-alert');
                    if (alert) alert.style.display = 'none';
                }, 5000); // Closes after 5 seconds
            </script>
            
            <div class="restaurant-management">
                <!-- Owners List Section with Accordion -->
                <section class="owners-section">
                    <h2>Restaurant Owners</h2>
                    <div class="owners-grid">
                        <?php foreach ($owners as $owner): ?>
                            <button class="owner-card owner-toggle" data-owner-id="<?= $owner['user_id'] ?>">
                                <div class="owner-info">
                                    <h3><?= ucfirst(htmlspecialchars($owner['full_name'])) ?></h3>
                                    <p><?= htmlspecialchars($owner['email']) ?></p>
                                    <p><?= htmlspecialchars($owner['phone']) ?></p>
                                </div>
                                <div class="restaurant-count">
                                    <span><?= $owner['restaurant_count'] ?></span>
                                    <small>Restaurants</small>
                                </div>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </button>
                            
                            <div class="accordion-content" id="owner-<?= $owner['user_id'] ?>">
                                <div class="loading-spinner-container">
                                    <div class="loading-spinner"></div>
                                    <span>Loading restaurants...</span>
                                </div>
                                <!-- Restaurant content will be loaded here via AJAX -->
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Add this at the bottom of your HTML, before the closing body tag -->
    <div id="restaurantModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div class="modal-header">
                <h2 id="modalRestaurantName"></h2>
                <span id="modalRestaurantStatus" class="status-badge"></span>
            </div>
            
            <div class="modal-body">
                <div class="restaurant-images">
                    <div class="main-image-container">
                        <img id="modalMainImage" src="" alt="Restaurant Main Image" class="main-image">
                    </div>
                    <div class="thumbnail-container" id="thumbnailGallery"></div>
                </div>
                
                <div class="restaurant-details">
                    <div class="detail-section">
                        <h3>Basic Information</h3>
                        <div class="detail-row">
                            <span class="detail-label">Location:</span>
                            <span id="modalLocation" class="detail-value"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone:</span>
                            <span id="modalPhone" class="detail-value"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Opening Hours:</span>
                            <span id="modalHours" class="detail-value"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Rating:</span>
                            <span id="modalRating" class="detail-value"></span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Description</h3>
                        <p id="modalDescription" class="restaurant-description"></p>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Social Media</h3>
                        <div class="social-links">
                            <a id="modalFacebook" href="#" target="_blank" class="social-link facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a id="modalInstagram" href="#" target="_blank" class="social-link instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a id="modalTiktok" href="#" target="_blank" class="social-link tiktok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a id="modalTelegram" href="#" target="_blank" class="social-link telegram">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a id="modalWebsite" href="#" target="_blank" class="social-link website">
                                <i class="fas fa-globe"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h3>License Information</h3>
                        <div class="license-container">
                            <a id="modalLicense" href="#" target="_blank" class="license-link">
                                <i class="fas fa-file-pdf"></i> View License Document
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="javaScript/restaurant_detail_modal.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accordion functionality
            const ownerToggles = document.querySelectorAll('.owner-toggle');
            
            ownerToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const ownerId = this.getAttribute('data-owner-id');
                    const accordionContent = document.getElementById(`owner-${ownerId}`);
                    const icon = this.querySelector('.accordion-icon');
                    
                    // Toggle active class
                    this.classList.toggle('active');
                    
                    // Toggle icon
                    if (this.classList.contains('active')) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                        
                        // Load restaurants if not already loaded
                        if (!accordionContent.hasAttribute('data-loaded')) {
                            loadRestaurants(ownerId);
                        }
                    } else {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                    
                    // Toggle accordion content
                    accordionContent.style.display = accordionContent.style.display === 'block' ? 'none' : 'block';
                });
            });
            
            // Function to load restaurants via AJAX
            function loadRestaurants(ownerId) {
                const accordionContent = document.getElementById(`owner-${ownerId}`);
                const loadingContainer = accordionContent.querySelector('.loading-spinner-container');
                
                fetch(`get_restaurants.php?owner_id=${ownerId}`)
                    .then(response => response.text())
                    .then(data => {
                        accordionContent.innerHTML = data;
                        accordionContent.setAttribute('data-loaded', 'true');
                        
                        // Initialize action buttons for the loaded content
                        initActionButtons();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loadingContainer.innerHTML = '<p class="error-message">Failed to load restaurants. Please try again.</p>';
                    });
            }
            
            // Initialize action buttons (approve/reject)
            function initActionButtons() {
                const rejectButtons = document.querySelectorAll('.btn-reject');
                rejectButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        if (!confirm('Are you sure you want to reject this restaurant?')) {
                            e.preventDefault();
                        }
                    });
                });
                
                const forms = document.querySelectorAll('.action-form');
                forms.forEach(form => {
                    form.addEventListener('submit', function() {
                        const buttons = form.querySelectorAll('button');
                        buttons.forEach(button => {
                            button.disabled = true;
                            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        });
                    });
                });
            }
        });
    </script>