document.addEventListener('DOMContentLoaded', function() {
    // Get order ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    
    if (!orderId) {
        alert('No order ID found in URL');
        return;
    }

    // Load order details
    loadOrderDetails(orderId);

    // Initialize modals
    initModals();

    // Initialize review functionality
    initReviewSystem(orderId);
});

function loadOrderDetails(orderId) {
    fetch(`api/get_order.php?order_id=${orderId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(order => {
            if (!order.success) throw new Error(order.message);
            renderOrderDetails(order.data);
        })
        .catch(error => {
            console.error('Error loading order:', error);
            alert('Failed to load order details. Please try again later.');
        });
}

function renderOrderDetails(order) {
    // Update status
    document.getElementById('status-badge').textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);
    
    // Update restaurant info
    document.getElementById('restaurant-name').textContent = order.restaurant.name;
    document.getElementById('restaurant-logo').src = order.restaurant.logo_url;
    renderRatingStars(order.restaurant.rating, 'restaurant-rating');
    
    // Update order items
    const itemsContainer = document.getElementById('order-items');
    itemsContainer.innerHTML = order.items.map(item => `
        <div class="order-item">
            <div class="item-info">
                <div class="item-name">${item.name}</div>
                ${item.special_instructions ? `<div class="item-notes">${item.special_instructions}</div>` : ''}
            </div>
            <div class="item-price">${item.quantity} × $${item.unit_price.toFixed(2)}</div>
        </div>
    `).join('');
    
    // Update delivery address
    document.getElementById('delivery-address-text').textContent = order.delivery_address;
    
    // Update pricing
    document.getElementById('subtotal').textContent = `$${order.subtotal.toFixed(2)}`;
    document.getElementById('delivery-fee').textContent = `$${order.delivery_fee.toFixed(2)}`;
    document.getElementById('tax').textContent = `$${order.tax.toFixed(2)}`;
    document.getElementById('total').textContent = `$${order.total.toFixed(2)}`;
    
    // Show review section if order is delivered
    if (order.status === 'delivered') {
        document.getElementById('review-section').style.display = 'block';
    }
}

function renderRatingStars(rating, elementId) {
    const container = document.getElementById(elementId);
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    
    container.innerHTML = '';
    
    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('i');
        star.className = 'fas fa-star';
        
        if (i <= fullStars) {
            star.style.color = '#FFD700'; // Gold color for full stars
        } else if (i === fullStars + 1 && hasHalfStar) {
            star.className = 'fas fa-star-half-alt';
            star.style.color = '#FFD700';
        } else {
            star.style.color = '#ddd'; // Gray for empty stars
        }
        
        container.appendChild(star);
    }
    
    // Add rating number
    const ratingText = document.createElement('span');
    ratingText.textContent = ` ${rating.toFixed(1)}`;
    ratingText.style.marginLeft = '5px';
    ratingText.style.color = '#666';
    container.appendChild(ratingText);
}

function initModals() {
    // Restaurant review modal
    const restaurantModal = document.getElementById('restaurant-review-modal');
    const restaurantBtn = document.getElementById('review-restaurant-btn');
    const restaurantClose = restaurantModal.querySelector('.close-btn');
    
    restaurantBtn.addEventListener('click', () => {
        restaurantModal.style.display = 'flex';
    });
    
    restaurantClose.addEventListener('click', () => {
        restaurantModal.style.display = 'none';
    });
    
    // Platform review modal (similar implementation)
}

function initReviewSystem(orderId) {
    // Star rating functionality
    document.querySelectorAll('.star-rating i').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            const stars = this.parentElement.querySelectorAll('i');
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
    
    // Photo upload preview
    document.getElementById('restaurant-photos').addEventListener('change', function(e) {
        const preview = document.getElementById('restaurant-photo-preview');
        preview.innerHTML = '';
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                if (!file.type.match('image.*')) return;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.style.margin = '5px';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    });
    
    // Submit restaurant review
    document.getElementById('submit-restaurant-review').addEventListener('click', function() {
        const stars = document.querySelectorAll('#restaurant-review-modal .star-rating i.active');
        const rating = stars.length;
        const comment = document.getElementById('restaurant-comment').value;
        const photos = document.getElementById('restaurant-photos').files;
        
        if (rating === 0) {
            alert('Please select a rating');
            return;
        }
        
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('rating', rating);
        formData.append('comment', comment);
        formData.append('type', 'restaurant');
        
        // Append photos if any
        if (photos.length > 0) {
            for (let i = 0; i < photos.length; i++) {
                formData.append(`photos[${i}]`, photos[i]);
            }
        }
        
        fetch('api/submit_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thank you for your review!');
                document.getElementById('restaurant-review-modal').style.display = 'none';
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error submitting review:', error);
            alert('Failed to submit review. Please try again.');
        });
    });
}