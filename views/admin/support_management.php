<?php
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['answer_submit'])) {
            $faq_id = intval($_POST['faq_id']);
            $answer = trim($_POST['answer']);
            
            if (!empty($answer)) {
                $status = 'answered';
                $stmt = $conn->prepare("UPDATE support SET answer = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE faq_id = ?");
                $stmt->bind_param("ssi", $answer, $status, $faq_id);
                $stmt->execute();
                
                $_SESSION['message'] = "Answer submitted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Answer cannot be empty!";
                $_SESSION['message_type'] = "error";
            }
            
            header("Location: ?page=support_management&faq_id=$faq_id");
            exit();
        }
        
        if (isset($_POST['change_status'])) {
            $faq_id = intval($_POST['faq_id']);
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE support SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE faq_id = ?");
            $stmt->bind_param("si", $status, $faq_id);
            $stmt->execute();
            
            $_SESSION['message'] = "Status updated successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: support_management.php?faq_id=$faq_id");
            exit();
        }
    }

    // Get filter parameters
    $asker_role = $_GET['asker_role'] ?? 'general';
    $status = $_GET['status'] ?? 'all';

    // Build query for FAQs list
    $query = "SELECT * FROM support WHERE 1=1";
    $params = [];
    $types = '';

    if ($asker_role !== 'general') {
        $query .= " AND asker_role = ?";
        $params[] = $asker_role;
        $types .= 's';
    }

    if ($status !== 'all') {
        $query .= " AND status = ?";
        $params[] = $status;
        $types .= 's';
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $faqs_result = $stmt->get_result();
    $faqs = $faqs_result->fetch_all(MYSQLI_ASSOC);

    // Get specific FAQ details if faq_id is set
    $faq_details = null;
    if (isset($_GET['faq_id'])) {
        $faq_id = intval($_GET['faq_id']);
        $stmt = $conn->prepare("SELECT * FROM support WHERE faq_id = ?");
        $stmt->bind_param("i", $faq_id);
        $stmt->execute();
        $faq_details = $stmt->get_result()->fetch_assoc();
    }
?>

    <div class="admin-container">
        
        <main class="main-content">
            <h1>FAQ Management</h1>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            
            <div class="faq-management">
                <!-- Filters Section -->
                <div class="faq-filters">
                    <div class="filter-group">
                        <h3>Filter by Role</h3>
                        <div class="role-filter">
                            <div class="role-option <?= $asker_role === 'all' ? 'active' : '' ?>" onclick="setFilter('asker_role', 'all')">
                                <div class="role-badge" style="background: #d1d3e2;"></div>
                                <span>All Roles</span>
                            </div>
                            <div class="role-option <?= $asker_role === 'customer' ? 'active' : '' ?>" onclick="setFilter('asker_role', 'customer')">
                                <div class="role-badge customer-badge"></div>
                                <span>Customers</span>
                            </div>
                            <div class="role-option <?= $asker_role === 'restaurant' ? 'active' : '' ?>" onclick="setFilter('asker_role', 'restaurant')">
                                <div class="role-badge restaurant-badge"></div>
                                <span>Restaurants</span>
                            </div>
                            <div class="role-option <?= $asker_role === 'delivery' ? 'active' : '' ?>" onclick="setFilter('asker_role', 'delivery')">
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
                            <span class="status-option answered-option <?= $status === 'answered' ? 'active' : '' ?>" onclick="setFilter('status', 'answered')">Answered</span>
                            <span class="status-option closed-option <?= $status === 'closed' ? 'active' : '' ?>" onclick="setFilter('status', 'closed')">Closed</span>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content Area -->
                <div>
                    <?php if (isset($_GET['faq_id']) && $faq_details): ?>
                        <!-- FAQ Details View -->
                        <div class="faq-details">
                            <div class="faq-details-header">
                                <div class="faq-details-user">
                                    <div class="faq-details-avatar <?= $faq_details['asker_role'] ?>-avatar">
                                        <?= strtoupper(substr($faq_details['name'], 0, 1)) ?>
                                    </div>
                                    <div class="faq-details-meta">
                                        <div class="faq-details-name"><?= htmlspecialchars($faq_details['name']) ?></div>
                                        <div class="faq-details-email"><?= htmlspecialchars($faq_details['email']) ?></div>
                                        <span class="faq-details-role role-<?= $faq_details['asker_role'] ?>"><?= $faq_details['asker_role'] ?></span>
                                    </div>
                                </div>
                                <div class="faq-details-status">
                                    <span class="status-<?= $faq_details['status'] ?>"><?= ucfirst($faq_details['status']) ?></span>
                                </div>
                            </div>
                            
                            <div class="faq-details-subject"><?= htmlspecialchars($faq_details['subject']) ?></div>
                            
                            <div class="faq-details-message">
                                <?= nl2br(htmlspecialchars($faq_details['message'])) ?>?
                            </div>
                            
                            <div class="faq-date">
                                Asked on <?= date('F j, Y \a\t g:i A', strtotime($faq_details['created_at'])) ?>
                            </div>
                            
                            <?php if (!empty($faq_details['answer'])): ?>
                                <div class="faq-answer-container">
                                    <div class="faq-answer-header">
                                        <div class="faq-answer-title">Admin Response</div>
                                        <span class="faq-answer-badge status-answered">Answered</span>
                                    </div>
                                    <div class="faq-answer-content">
                                        <?= nl2br(htmlspecialchars($faq_details['answer'])) ?>
                                    </div>
                                    <div class="faq-date">
                                        Last updated on <?= date('F j, Y \a\t g:i A', strtotime($faq_details['updated_at'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="faq-answer-form">
                                <form method="POST">
                                    <input type="hidden" name="faq_id" value="<?= $faq_details['faq_id'] ?>">
                                    
                                    <textarea class="answer-textarea" name="answer" placeholder="Type your answer here..." required><?= htmlspecialchars($faq_details['answer'] ?? '') ?></textarea>
                                    
                                    <div class="faq-actions">
                                        <div>
                                            <select name="status" class="status-select" /*onchange="this.form.submit()"*/>
                                                <option value="pending" <?= $faq_details['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="answered" <?= $faq_details['status'] === 'answered' ? 'selected' : '' ?>>Answered</option>
                                                <option value="closed" <?= $faq_details['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="answer_submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Submit Answer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- FAQ List View -->
                        <div class="faq-list">
                            <?php if (empty($faqs)): ?>
                                <div class="no-faqs">
                                    <i class="fas fa-inbox"></i>
                                    <p>No FAQs found matching your criteria</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($faqs as $faq): ?>
                                    <div class="faq-card <?= $faq['asker_role'] ?> <?= isset($_GET['faq_id']) && $_GET['faq_id'] == $faq['faq_id'] ? 'active' : '' ?>"
                                         onclick="window.location.href='?page=support_management&faq_id=<?= $faq['faq_id'] ?>&asker_role=<?= $asker_role ?>&status=<?= $status ?>'">
                                        <div class="faq-header">
                                            <div class="faq-user">
                                                <div class="faq-avatar <?= $faq['asker_role'] ?>-avatar">
                                                    <?= strtoupper(substr($faq['name'], 0, 1)) ?>
                                                </div>
                                                <div class="faq-meta">
                                                    <div class="faq-name"><?= htmlspecialchars($faq['name']) ?></div>
                                                    <div class="faq-role"><?= $faq['asker_role'] ?></div>
                                                </div>
                                            </div>
                                            <span class="faq-status status-<?= $faq['status'] ?>"><?= ucfirst($faq['status']) ?></span>
                                        </div>
                                        
                                        <div class="faq-subject"><?= htmlspecialchars($faq['subject']) ?></div>
                                        <div class="faq-message"><?= nl2br(htmlspecialchars(substr($faq['message'], 0, 150))) ?>...</div>
                                        <div class="faq-date">
                                            <?= date('M j, Y', strtotime($faq['created_at'])) ?>
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
                url.searchParams.set('page', '1');
            }
            
            window.location.href = url.toString();
        }
        
        // Auto-expand textarea as user types
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('.answer-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
                
                // Trigger initial resize
                const evt = new Event('input');
                textarea.dispatchEvent(evt);
            });
        });
    </script>